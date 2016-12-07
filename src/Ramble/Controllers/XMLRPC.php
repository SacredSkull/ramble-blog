<?php
/**
 * Created by PhpStorm.
 * User: sacredskull
 * Date: 22/08/16
 * Time: 21:59
 */

namespace Ramble\Controllers;

use DateTime;
use Interop\Container\ContainerInterface;
use PhpXmlRpc\Encoder;
use PhpXmlRpc\Request;
use PhpXmlRpc\Value;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramble\Models\Article;
use Ramble\Models\ArticleQuery;
use Ramble\Models\Category;
use Ramble\Models\CategoryQuery;
use Ramble\Models\Tag;
use Ramble\Models\TagQuery;

class XMLRPC extends Controller {
	private $DEBUG = false;
	/**
	 * @var Encoder
	 */
	private $encoder = null;

	public function __construct(ContainerInterface $ci) {
		parent::__construct($ci);
		$this->DEBUG = $ci["ramble"]["debug"] ?? false;
		$this->encoder = new Encoder();
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args) {
		// Easy connection marking
		header('X-XMLRPC: blog XMLRPC');
		// Nice example of how to actually fake an XMLRPC request for testing purposes.
		//ddd($this->getCategories(new \PhpXmlRpc\Request("metaWeblog.editPost", [new \PhpXmlRpc\Value("1", "string"), new \PhpXmlRpc\Value("SacredSkull", "string"), new \PhpXmlRpc\Value("<insert password here>", "string")])));
		return $this->serve();
	}

	public function serve() : \PhpXmlRpc\Server{
		$server = new \PhpXmlRpc\Server(array(
			//
			/* The first parameter is the RETURN of the function! */
			//

			// Additionally, some clients (charm) will try to be good citizens and send an Int where there should be a string
			// (perhaps the opposite too - though that probably makes you a dick client - looking at you Blogilo!),
			// so those fields use Value::$xmlrpcValue, rather than Value::$xmlrpcString or Value::$xmlrpcInt.
			// PHP can take it - because it's not a hero.

			/*
			 * GET BLOG INFO
			 */

			// returns: array of struct - parameters: API key, username, password
			"blogger.getUsersBlogs" => array(
				"function" => array($this, "getBlog"),
				"signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
				"docstring" => 'Gets blog info',
			),

			"metaWeblog.getUsersBlogs" => array(
				"function" => array($this, "getBlog"),
				"signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
				"docstring" => 'Gets blog info',
			),

			"wp.getUsersBlogs" => array(
				"function" => array($this, "wpGetBlog"),
				"signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcString, Value::$xmlrpcString)),
				"docstring" => 'Gets blog info',
			),

			/*
			 * CREATE/EDIT POST
			 */

			// returns: string - parameters: $blogid, $username, $password, $struct, $publish
			"metaWeblog.newPost" => array(
				"function" => array($this, "newPost"),
				"signature" => array(array(Value::$xmlrpcString, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcStruct, Value::$xmlrpcValue)),
				"docstring" => 'Creates a new post from the associative array/struct. Parameters: blogID, username, password, post data struct, publish bool',
			),

			// returns: bool - parameters: API key (ignored), $blogid, $username, $password, $struct, $publish
			"metaWeblog.editPost" => array(
				"function" => array($this, "editPost"),
				"signature" => array(array(Value::$xmlrpcBoolean, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcStruct, Value::$xmlrpcValue)),
				"docstring" => 'Edits the post of a a certain ID. Parameters: postID, username, password, post data struct, publish',
			),

			// returns: bool - parameters: $postId, $username, $password
			"mt.publishPost" => array(
				"function" => array($this, "publishPost"),
				"signature" => array(array(Value::$xmlrpcBoolean, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
				"docstring" => 'Publishes a certain post. Parameters: postID, username, password',
			),

			// returns: bool - parameters: $postId, $username, $password, struct
			"mt.setPostCategories" => array(
				"function" => array($this, "setCategory"),
				"signature" => array(array(Value::$xmlrpcBoolean, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcArray)),
				"docstring" => 'Edits the post of a a certain ID. Parameters: postID, username, password, struct of categories (only parses the primary or first category)',
			),

			/*
			 * DELETE
			 */

			// returns: bool - parameters: API key (ignored), $postid, $username, $password, $publish (ignored - what the hell is this doing here?!)
			"blogger.deletePost" => array(
				"function" => array($this, "deletePost"),
				"signature" => array(array(Value::$xmlrpcBoolean, Value::$xmlrpcValue, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcValue)),
				"docstring" => "Deletes a post, returns true if deleted. Parameters: appKey, postID, username, password, publish bool (ignored)",
			),

			// returns: bool - parameters: API key (ignored), $postid, $username, $password, $publish (ignored - what the hell is this doing here?!)
			"metaWeblog.deletePost" => array(
				"function" => array($this, "deletePost"),
				"signature" => array(array(Value::$xmlrpcBoolean, Value::$xmlrpcValue, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcValue)),
				"docstring" => "Deletes a post, returns true if deleted. Parameters: appKey, postID, username, password, publish bool (ignored)",
			),

			/*
			 * GET POST(S)
			 */

			// returns: struct - parameters: $postid, $username, $password
			"metaWeblog.getPost" => array(
				"function" => array($this, "getPost"),
				"signature" => array(array(Value::$xmlrpcStruct, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
				"docstring" => 'Gets a specific post. Parameters: postID, username, password',
			),

			// returns: array - blogID, username, password, noOfPosts
			"metaWeblog.getRecentPosts" => array(
				"function" => array($this, "getRecentPosts"),
				"signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcInt)),
				"docstring" => 'Gets a certain number of posts, ordered by recency. Parameters: blogID, username, password, noOfPosts',
			),

			// returns: array - blogID, username, password, noOfPosts
			"mt.getRecentPostTitles" => array(
				"function" => array($this, "getRecentPosts"),
				"signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcInt)),
				"docstring" => 'Gets a certain number of posts, ordered by recency. Parameters: blogID, username, password, noOfPosts',
			),

			// returns: array - blogID, username, password, noOfPosts
			"mt.getPostCategories" => array(
				"function" => array($this, "getPostCategories"),
				"signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
				"docstring" => 'Gets a certain number of posts, ordered by recency. Parameters: blogID, username, password, noOfPosts',
			),

			/*
			 * CREATE CATEGORY
			 */

			// returns: int (category ID) - parameters blogID, username, password, category struct
			"wp.newCategory" => array(
				"function" => array($this, "addCategory"),
				"signature" => array(array(Value::$xmlrpcInt, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcStruct)),
				"docstring" => "Creates a category. Parameters: blogID, username, password, category struct/array name"
			),

			/*
			 * GET CATEGORIES
			 */

			// returns: array - parameters blogID, username, password
			"metaWeblog.getCategories" => array(
				"function" => array($this, "getCategories"),
				"signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
				"docstring" => "Returns [categories]. Parameters: blogID, username, password"
			),

			// returns: array - parameters blogID, username, password
			"mt.getCategoryList" => array(
				"function" => array($this, "getCategories"),
				"signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
				"docstring" => "Returns [categories]. Parameters: blogID, username, password"
			),

			"wp.getCategories" => array(
				"function" => array($this, "getCategories"),
				"signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
				"docstring" => "Returns [categories]. Parameters: blogID, username, password"
			),

			/*
			 * MISCELLANEOUS
			 */

			// Gets trackbacks/pings on a post, not implemented currently.
			"mt.getTrackbackPings" => array(
				"function" => array($this, "returnEmpty"),
				"signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcValue)),
				"docstring" => "Returns array(['pingTitle', 'pingURL', 'pingIP']). Parameters: postID"
			),

			// Not supported by WP, will probably ignore it
			"mt.supportedTextFilters" => array(
				"function" => array($this, "returnEmpty"),
				"signature" => array(array(Value::$xmlrpcArray)),
				"docstring" => "Returns array(['pingTitle', 'pingURL', 'pingIP']). Parameters: postID"
			),
		));

		$server->setDebug(3);
		$server->exception_handling = 1;

		return $server;
	}

	public function rsdRender(ServerRequestInterface $request, ResponseInterface $response, array $args) : ResponseInterface {
        return $this->render($response, 'rsd.html.twig');
    }

	//TODO: if distributing, I'd recommend you change this!
	public function passwordAdmin($user, $pass) {
		$verified = (strcmp($user, $this->ci['auth']['user']) == 0) && password_verify($pass, $this->ci['auth']['password']);
		if(!$verified) {
			$this->logger->warn('[XMLRPC] Bad login details used.', ['Username' => $user, 'Password' => preg_replace('/./', '*', $pass)]);
		} else {
			$this->logger->debug('[XMLRPC] Successful login', ['Username' => $user]);
		}
		return $verified;
	}

	public function returnEmpty(Request $req, $empty = array(), int $responseCode = 200){
		return new \PhpXmlRpc\Response($this->encoder->encode($empty), $responseCode);
	}

	public function notFound(Request $req){
		return $this->returnEmpty($req, false, 404);
	}

	public function badAuth(Request $req){
		return $this->returnEmpty($req, false, 403);
	}

	public function getBlog(Request $req) {
		$params = $this->encoder->decode($req);

		$user = $params[1];
		$pass = $params[2];

		// param[0] Appkey is irrelevant
		$return = array(array(
			"blogid" => 1,
			"url" => "http://" . $_SERVER['SERVER_NAME'],
			"blogName" => "Main",
			"isAdmin" => $this->passwordAdmin($user, $pass),
			"xmlrpc" => "http://" . $_SERVER['SERVER_NAME'] . str_replace('.php', '', $this->router->pathFor('XMLRPC'))
		));

		if ($return[0]["isAdmin"] != 1) {
			return $this->badAuth($req);
		}
		return new \PhpXmlRpc\Response($this->encoder->encode($return));
	}

	public function wpGetBlog(Request $req){
		$params = $this->encoder->decode($req);
		$wrappedReq = new Request($req->methodname);

		// Add API, Username & password
		$wrappedReq->addParam(new Value('FAKEAPIKEY', Value::$xmlrpcString));
		$wrappedReq->addParam(new Value($params[0], Value::$xmlrpcString));
		$wrappedReq->addParam(new Value($params[1], Value::$xmlrpcString));

		return $this->getBlog($wrappedReq);
	}

	public function newPost(Request $req) {
		$params = $this->encoder->decode($req);

		$username = $params[1];
		$password = $params[2];
		$postStruct = $params[3];
		$publish = $params[4];

		if (!$this->passwordAdmin($username, $password)) {
			return $this->badAuth($req);
		}

		$post = new Article();
		$post->setDraft(!$publish);
		$cat = $postStruct["categories"][0];
		$post->setCategory(CategoryQuery::create()->findOneBySlug($cat) ?? CategoryQuery::create()->findOne());
		$post->setTitle($postStruct["title"]);
		$post->setBody(html_entity_decode($postStruct["description"]));
		$post->setCreatedAt($postStruct["dateCreated"] ?? new DateTime());

		// Tag parsing
		$tagArray = explode(',', $postStruct['mt_keywords']) ?? [];
		$this->logger->debug("Parsed tag bundle", $tagArray);

		foreach ($tagArray as $tag) {
			$tag = trim($tag, ', ');
			$exists = TagQuery::create()->setIgnoreCase(true)->findOneByName($tag);
			if ($exists != null) {
				$this->logger->debug("Existing tag found", ['Name' => $tag]);
			} else if(!empty($tag)){
				$exists = new Tag();
				$exists->setName($tag);
				$this->logger->debug('Non-existing tag found, creating now', ['Name' => $tag]);
				$exists->save();
			} else {
				// Malformed tag (probably an empty string)
				continue;
			}
			$post->addTag($exists);
		}
		$post->setExcerpt($postStruct["mt_excerpt"] ?? (function($body) : string{
			$firstWords = "";
			preg_match("/(?:\w+(?:\W+|$)){0,20}/", $body, $firstWords);
			return $firstWords[0];
		})($postStruct["description"]));


		$post->setAllowComments(isset($postStruct["mt_allow_comments"]) ? $postStruct["mt_allow_comments"] : true);

		$post->save();
		$this->logger->info("Created new post", ['ID' => $post->getId(), 'Title' => $post->getTitle()]);

		return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value($post->getId(), "string"));
	}

	public function editPost(Request $req) {
		$params = $this->encoder->decode($req);

		$postID = $params[0];
		$username = $params[1];
		$password = $params[2];
		$postStruct = $params[3];
		$publish = $params[4];

		if (!$this->passwordAdmin($username, $password)) {
			return $this->badAuth($req);
		}

		$post = ArticleQuery::create()->findOneById($postID);
		$post->setDraft(!$publish);
		$originalVersion = $post->getVersion();
		// Allows direct use of mt.publishPost without any real modification (see publishPost())
		if($postStruct != false) {
			$category = "";

			if (!empty($category)) {
				$category = $postStruct["categories"][0];
				$post->setCategory(CategoryQuery::create()->findOneBySlug($category));
			}
			$post->setTitle($postStruct["title"]);
			$post->setBody(html_entity_decode($postStruct["description"]));

			// Tag parsing
			$tagArray = explode(',', $postStruct['mt_keywords']);
			$this->logger->debug("Parsed tag bundle", $tagArray);

			foreach ($tagArray as $tag) {
				$tag = trim($tag, ',');
				$exists = TagQuery::create()->setIgnoreCase(true)->findOneByName($tag);
				if ($exists != null) {
					$this->logger->debug("Existing tag found", ['Name' => $tag]);
				} else if (!empty($tag)) {
					$exists = new Tag();
					$exists->setName($tag);
					$this->logger->debug('Non-existing tag found, creating now', ['Name' => $tag]);
					$exists->save();
				} else {
					// Malformed tag (probably an empty string)
					continue;
				}
				$post->addTag($exists);
			}

			$post->setExcerpt($postStruct["mt_excerpt"]);
			$post->setAllowComments(isset($postStruct["mt_allow_comments"]) ? $postStruct["mt_allow_comments"] : true);
			$originalVersion = $post->getVersion();
		}
		$post->save();
		$this->logger->info("Edited existing post", ['ID' => $post->getId(), 'Title' => $post->getTitle(),
			'Changes' => ($originalVersion == $post->getVersion())? "None" : $post->compareVersions($originalVersion,
				$post->getVersion(), 'columns', null, ['UpdatedAt', 'CreatedAt', 'Slug', 'BodyHTML', 'Body'])]);

		return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value("true", "boolean"));
	}

	public function publishPost(Request $req){
		$params = $this->encoder->decode($req);

		$wrappedReq = new Request($req->methodname);
		$wrappedReq->addParam(new Value($params[0], Value::$xmlrpcInt));
		$wrappedReq->addParam(new Value($params[1], Value::$xmlrpcString));
		$wrappedReq->addParam(new Value($params[2], Value::$xmlrpcString));
		$wrappedReq->addParam(new Value(false, Value::$xmlrpcBoolean));
		$wrappedReq->addParam(new Value(true, Value::$xmlrpcBoolean));

		return $this->editPost($wrappedReq);
	}

	// returns: bool - parameters: postId, username, password, struct
	public function setCategory(Request $req){
		$params = $this->encoder->decode($req);

		$id = $params[0];
		$username = $params[1];
		$password = $params[2];
		$categories = $params[3];

		if (!$this->passwordAdmin($username, $password))
			return $this->badAuth($req);

		$post = ArticleQuery::create()->findPK($id);

		if($post == null)
			return $this->notFound($req);

		if(!is_array($categories)) {
			return $this->returnEmpty($req, false, 400);
		}

		$prime = null;
		$first = true;

		// Uses the first 'primary' category, or the first category it encounters if it can't find one
		foreach ($categories as $category) {
			if(isset($category[0])) {
				if ($first) {
					$prime = CategoryQuery::create()->findOneById($category[0]) ??
						CategoryQuery::create()->findOneBySlug($category[0]);
					$first = false;
				} else if (isset($category[1])) {
					if (boolval($category[1])) {
						$prime = CategoryQuery::create()->findOneById($category[0]) ??
							CategoryQuery::create()->findOneBySlug($category[0]);
						break;
					}
				}
			}
		}

		$post->setCategory($prime);
		return $this->returnEmpty($req, true);
	}

	public function getPost(Request $req) {
		$params = $this->encoder->decode($req);

		$postID = $params[0];
		$post = ArticleQuery::create()->findPK($postID);

		if($post == null){
			return $this->notFound($req);
		}

		$tags = $post->getTags();
		$tagList = "";
		foreach ($tags as $tag) {
			$tagList .= $tag->getName() . ",";
		}

		$postArr = array(
			"postid" => $post->getId(),
			"title" => $post->getTitle(),
			"description" => $post->getBody(),
			"link" => sprintf("%s%s/id/%s", (isset($_SERVER["HTTPS"]) && !strcasecmp("off", $_SERVER["HTTPS"])) ? "https://" : "http://", $_SERVER["HTTP_HOST"], $post->getId()),
			"dateCreated" => new Value($post->getCreatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
			"date_created_gmt" => new Value($post->getUpdatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
			"date_modified" => new Value($post->getCreatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
			"date_modified_gmt" => new Value($post->getUpdatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
			"wp_post_thumbnail" => (isset($_SERVER["HTTPS"]) && !strcasecmp("off", $_SERVER["HTTPS"]) ? "https://" : "http://") . "s3-eu-west-1.amazonaws.com/sacredskull-blog/images/" . $post->getImage(),
			"categories" => array($post->getCategory()->getSlug()),
			"mt_keywords" => $tagList,
			"mt_excerpt" => $post->getExcerpt(),
			"mt_allow_comments" => "true",
			"wp_slug" => $post->getSlug(),
			"post_draft" => $post->getDraft(),
		);

		return new \PhpXmlRpc\Response($this->encoder->encode($postArr));
	}

	public function getRecentPosts(Request $req) {
		$params = $this->encoder->decode($req);

		$postCount = $params[3];

		if (strlen($postCount) < 1)
			$postCount = 10;

		$posts = ArticleQuery::create()->lastCreatedFirst()->limit($postCount)->find();

		$allPosts = array();

		foreach ($posts as $post) {
			$tags = $post->getTags();
			$tagList = "";
			foreach ($tags as $tag) {
				$tagList .= $tag->getName() . ",";
			}

			$postArr = array(
				"postid" => $post->getId(),
				"title" => $post->getTitle(),
				"description" => $post->getBody(),
				"link" => sprintf("%s%s/id/%s", (isset($_SERVER["HTTPS"]) && !strcasecmp("off", $_SERVER["HTTPS"])) ? "https://" : "http://", $_SERVER["HTTP_HOST"], $post->getId()),
				"dateCreated" => new Value($post->getCreatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
				"date_created_gmt" => new Value($post->getCreatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
				"date_modified" => new Value($post->getUpdatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
				"date_modified_gmt" => new Value($post->getUpdatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
				"wp_post_thumbnail" => (isset($_SERVER["HTTPS"]) && !strcasecmp("off", $_SERVER["HTTPS"])) ? "https://" : "http://" . "s3-eu-west-1.amazonaws.com/sacredskull-blog/images/" . $post->getImage(),
				"categories" => array($post->getCategory()->getSlug()),
				"mt_keywords" => $tagList,
				"mt_excerpt" => $post->getExcerpt(),
				"mt_allow_comments" => "true",
				"wp_slug" => $post->getSlug(),
				"post_draft" => $post->getDraft(),
			);

			$allPosts[] = $postArr;
		}

		return new \PhpXmlRpc\Response($this->encoder->encode($allPosts));
	}

	// Parameters: postID, username, password
	public function getPostCategories(Request $req){
		$params = $this->encoder->decode($req);

		$id = $params[0];

		$post = ArticleQuery::create()->findOneById($id);

		if($post == null){
			return $this->notFound($req);
		}

		return new \PhpXmlRpc\Response($this->encoder->encode([
			[
				'categoryName' => $post->getCategory()->getName(),
				'categoryId' => $post->getCategoryId(),
				'isPrimary' => true,
			]
		]));
	}

	public function getCategories(Request $req) {
		$params = $this->encoder->decode($req);

		$username = $params[1];
		$password = $params[2];

		$categoriesArr = "";

		$categories = CategoryQuery::create()->find();

		foreach ($categories as $category) {
			$categoriesArr[] = array(
				"categoryId" => $category->getId(),
				"categoryName" => $category->getName(),
				"parentId" => null,
				"categoryDescription" => $category->getName(),
				"htmlUrl" => $this->router->pathFor('GET_HOME_FILTER_CATEGORY', [
					'category' => $category->getSlug()
				]),
				"rssUrl" => null
			);
		}

		return new \PhpXmlRpc\Response($this->encoder->encode($categoriesArr));
	}

// returns: bool - parameters appKey, postID, username, password, (ignored) publish
	public function deletePost(Request $req) {
		$params = $this->encoder->decode($req);

		$postID = $params[1];
		$username = $params[2];
		$password = $params[3];

		$post = ArticleQuery::create()->findOneById($postID);

		if (!$this->passwordAdmin($username, $password)) {
			return $this->badAuth($req);
		}

		$post->delete();
		$this->logger->info('Deleted post', ['ID' => $post->getId(), 'Title' => $post->getTitle()]);

		return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value("true", "boolean"));
	}

	public function addCategory(Request $req) {
		$params = $this->encoder->decode($req);

		$username = $params[1];
		$password = $params[2];
		$categoryStruct = $params[3];

		if (!$this->passwordAdmin($username, $password)) {
			return $this->badAuth($req);
		}

		if(!isset($categoryStruct['name']) || !empty($categoryStruct[0])){
			return $this->returnEmpty($req, false, 400);
		}

		$name = $categoryStruct['name'] ?? $categoryStruct[0];

		$category = CategoryQuery::create()->findOneByName($name);

		if ($category == null) {
			$category = new Category();
			$category->setName($name);
		}

		$category->save();
		$this->logger->info('Category created', ['ID' => $category->getId(), 'Title' => $category->getName()]);

		return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value($category->getId(), "int"));
	}
}
