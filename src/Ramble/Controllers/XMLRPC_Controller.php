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
use PhpXmlRpc\Value;
use Propel\Runtime\Propel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramble\Models\Article;
use Ramble\Models\ArticleQuery;
use Ramble\Models\Category;
use Ramble\Models\CategoryQuery;
use Ramble\Models\Map\ArticleTableMap;
use Ramble\Models\Map\TagTableMap;
use Ramble\Models\Tag;
use Ramble\Models\TagQuery;

class XMLRPC_Controller extends Controller {
	private $DEBUG = false;

	public function __construct(ContainerInterface $ci) {
		parent::__construct($ci);
		$this->DEBUG = $ci["ramble"]["debug"] ?? false;
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args) {
		// Easy connection marking
		header('Access-Control-Allow-Origin: https://gggeek.github.io/');
		header('Access-Control-Allow-Origin: http://gggeek.github.io/');
		header('X-XMLRPC: blog XMLRPC');
		header('Content-Type: text/xml');
		// Nice example of how to actually fake an XMLRPC request for testing purposes.
		//ddd($this->getCategories(new \PhpXmlRpc\Request("metaWeblog.editPost", [new \PhpXmlRpc\Value("1", "string"), new \PhpXmlRpc\Value("SacredSkull", "string"), new \PhpXmlRpc\Value("<insert password here>", "string")])));
		$this->serve();
	}

	public function serve() : \PhpXmlRpc\Server{
		return new \PhpXmlRpc\Server(array(
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
			 * DELETE CATEGORY
			 */

			// returns: array - parameters blogID, username, password
			"metaWeblog.getCategories" => array(
				"function" => array($this, "getCategories"),
				"signature" => array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
				"docstring" => "Returns categories. Parameters: blogID, username, password"
			),
		));
	}

	//TODO: if distributing, I'd recommend you change this!
	public function passwordAdmin($user, $pass) {
		$verified = (strcmp($user, "SacredSkull") == 0) && password_verify($pass, '***REMOVED***');
		if(!$verified) {
			$this->logger->warn('Bad login details used.', ['Username' => $user, 'Password' => preg_replace('/./', '*', $pass)]);
		} else {
			$this->logger->debug('Successful login', ['Username' => $user]);
		}
		return $verified;
	}

	public function getBlog($req) {
		$encoder = new \PhpXmlRpc\Encoder();
		$params = $encoder->decode($req);

		$user = $params[1];
		$pass = $params[2];

		// param[0] Appkey is irrelevant for now
		$return = array(array(
			"blogid" => 1,
			"url" => "http://" . $_SERVER['SERVER_NAME'],
			"blogName" => "Main",
			"isAdmin" => static::passwordAdmin($user, $pass),
			"xmlrpc" => ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . $_SERVER['SERVER_NAME'] . "/xmlrpc"
		));

		if ($return[0]["isAdmin"] != 1) {
			header('HTTP/1.0 403 Forbidden');
			return new \PhpXmlRpc\Response($encoder->encode(array(array())));
		}
		return new \PhpXmlRpc\Response($encoder->encode($return));
	}

	public function newPost($req) {
		$encoder = new \PhpXmlRpc\Encoder();
		$params = $encoder->decode($req);

		$blogID = $params[0];
		$username = $params[1];
		$password = $params[2];
		$postStruct = $params[3];
		$publish = $params[4];

		if (!static::passwordAdmin($username, $password)) {
			header('HTTP/1.0 403 Forbidden');
			return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value("false", "boolean"));
		}

		$post = new Article();
		$post->setDraft(!$publish);
		$cat = explode("|", $postStruct["categories"][0]);
		if($cat[0] != null || strlen($cat[0]) >= 1)
			$post->setCategory(CategoryQuery::create()->findOneByName($cat[0]));
		else
			$post->setCategory(CategoryQuery::create()->findOne());
		$post->setTitle($postStruct["title"]);
		$post->setBody(html_entity_decode($postStruct["description"]));
		$post->setCreatedAt($postStruct["dateCreated"] ?? new DateTime());

		// Tag parsing
		$tagArray = explode(',', $postStruct['mt_keywords']);
		$this->logger->debug("Parsed tag bundle", $tagArray);

		foreach ($tagArray as $tag) {
			$tag = trim($tag, ',');
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

	public function editPost($req) {
		$encoder = new \PhpXmlRpc\Encoder();
		$params = $encoder->decode($req);

		$postID = $params[0];
		$username = $params[1];
		$password = $params[2];
		$postStruct = $params[3];
		$publish = $params[4];

		if (!static::passwordAdmin($username, $password)) {
			header('HTTP/1.0 403 Forbidden');
			return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value("false", "boolean"));
		}

		$post = ArticleQuery::create()->findOneById($postID);
		$post->setDraft(!$publish);

		$exploded = explode('|', $postStruct["categories"][0]);

		if (!empty($exploded) && !empty($exploded[0])) {
			$post->setCategory(CategoryQuery::create()->findOneByName($exploded[0]));
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

		$post->setExcerpt($postStruct["mt_excerpt"]);
		$post->setAllowComments(isset($postStruct["mt_allow_comments"]) ? $postStruct["mt_allow_comments"] : true);
		$originalVersion = $post->getVersion();
		$post->save();
		$this->logger->info("Edited existing post", ['ID' => $post->getId(), 'Title' => $post->getTitle(),
			'Changes' => ($originalVersion == $post->getVersion())? "None" : $post->compareVersions($originalVersion,
				$post->getVersion(), 'columns', null, ['UpdatedAt', 'CreatedAt', 'Slug', 'BodyHTML', 'Body'])]);

		return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value("true", "boolean"));
	}

	public function getPost($req) {
		$encoder = new \PhpXmlRpc\Encoder();
		$params = $encoder->decode($req);

		$postID = $params[0];
		$username = $params[1];
		$password = $params[2];

		$post = ArticleQuery::create()->findPK($postID);

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
			"categories" => array($post->getCategory()->getName()),
			"mt_keywords" => $tagList,
			"mt_excerpt" => "Excerpt does not exist in database schema currently - neither does toggling comments",
			"mt_allow_comments" => "true",
			"wp_slug" => $post->getSlug(),
			"post_draft" => $post->getDraft(),
		);

		return new \PhpXmlRpc\Response($encoder->encode($postArr));
	}

	public function getRecentPosts($req) {
		$encoder = new \PhpXmlRpc\Encoder();
		$params = $encoder->decode($req);

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
				"categories" => array($post->getCategory()->getName()),
				"mt_keywords" => $tagList,
				"mt_excerpt" => $post->getExcerpt(),
				"mt_allow_comments" => "true",
				"wp_slug" => $post->getSlug(),
				"post_draft" => $post->getDraft(),
			);

			$allPosts[] = $postArr;
		}

		return new \PhpXmlRpc\Response($encoder->encode($allPosts));
	}

	public function getCategories($req) {
		$encoder = new \PhpXmlRpc\Encoder();
		$params = $encoder->decode($req);

		$username = $params[1];
		$password = $params[2];

		$categoriesArr = "";

		if (!static::passwordAdmin($username, $password)) {
			header('HTTP/1.0 403 Forbidden');
			return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value("false", "boolean"));
		}

		$categories = CategoryQuery::create()->find();

		foreach ($categories as $category) {
			$categoriesArr[] = array(
				"categoryId" => $category->getId(),
				"categoryName" => $category->getName() . "|" . $category->getColour() /*. "|" . $category->getFont()*/ . "|" . $category->getAdditionalCSS()
			);
		}

		return new \PhpXmlRpc\Response($encoder->encode($categoriesArr));
	}

// returns: bool - parameters appKey, postID, username, password, (ignored) publish
	public function deletePost($req) {
		$encoder = new \PhpXmlRpc\Encoder();
		$params = $encoder->decode($req);

		$postID = $params[1];
		$username = $params[2];
		$password = $params[3];

		$post = ArticleQuery::create()->findOneById($postID);

		if (!static::passwordAdmin($username, $password)) {
			header('HTTP/1.0 403 Forbidden');
			return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value("false", "boolean"));
		}

		$post->delete();
		$this->logger->info('Deleted post', ['ID' => $post->getId(), 'Title' => $post->getTitle()]);

		return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value("true", "boolean"));
	}

	public function addCategory($req) {
		$encoder = new \PhpXmlRpc\Encoder();
		$params = $encoder->decode($req);

		$username = $params[1];
		$password = $params[2];
		$categoryStruct = $params[3];

		$categoriesArr = array();

		if (!static::passwordAdmin($username, $password)) {
			header('HTTP/1.0 403 Forbidden');
			return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value("false", "boolean"));
		}

		$parsedCatName = explode("|", $categoryStruct["name"]);
		$category = CategoryQuery::create()->findOneByName($parsedCatName[0]);

		if ($category->count() == 0) {
			$category = new Category();
			$category->setName($parsedCatName[0]);
		}
		if (strlen($parsedCatName[1]) > 0)
			$category->setColour($parsedCatName[1]);
		$category->setRoot($parsedCatName[0]);
		// Fonts are a bit overkill?!
		// If re-enabling make sure to change indexes appropriately
//		if (strlen($parsedCatName[2]) > 0)
//			$category->setFont($parsedCatName[2]);
		if (strlen($parsedCatName[2]) > 0)
			$category->setAdditionalCSS($parsedCatName[2]);

		$category->save();
		$this->logger->info('Category created', ['ID' => $category->getId(), 'Title' => $category->getName()]);

		return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value($category->getId(), "int"));
	}
}