<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 15/07/2017
 * Time: 22:56
 */

namespace Ramble\Controllers\XMLRPC;


use DateTime;
use PhpXmlRpc\Request;
use PhpXmlRpc\Response;
use PhpXmlRpc\Value;
use Ramble\Models\Article;
use Ramble\Models\Tag;

class Metaweblog extends Blogger
{
    protected function getNamespace() : string{
        return "metaWeblog";
    }

    public function getServiceDefinitions(): array {
        // TODO: convert these to ServiceFunction objects
        return $this->functions = array(
            // returns: array of struct - parameters: API key, username, password
            $this->createFunction(
                "getUsersBlogs",
                function($req){return $this->getBlog($req);},
                array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
                'Gets blog info'
            ),

            // returns: struct - parameters: $postid, $username, $password
            $this->createFunction(
                "getPost",
                function($req){return $this->getPost($req);},
                array(array(Value::$xmlrpcStruct, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
                'Gets a specific post. Parameters: postID, username, password'
            ),

            // returns: array - blogID, username, password, noOfPosts
            $this->createFunction(
                "getRecentPosts",
                function($req){return $this->getRecentPosts($req);},
                array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcInt)),
                'Gets a certain number of posts, ordered by recency. Parameters: blogID, username, password, noOfPosts'
            ),

            // returns: array - parameters blogID, username, password
            $this->createFunction(
                "getCategories",
                function($req){return $this->getCategories($req);},
                array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
                "Returns [categories]. Parameters: blogID, username, password"
            ),

            // returns: string - parameters: $blogid, $username, $password, $struct, $publish
            $this->createFunction(
                "newPost",
                function($req){return $this->newPost($req);},
                array(array(Value::$xmlrpcString, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcStruct, Value::$xmlrpcValue)),
                'Creates a new post from the associative array/struct. Parameters: blogID, username, password, post data struct, publish bool'
            ),

            // returns: bool - parameters: API key (ignored), $blogid, $username, $password, $struct, $publish
            $this->createFunction(
                "editPost",
                function($req){return $this->editPost($req);},
                array(array(Value::$xmlrpcBoolean, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcStruct, Value::$xmlrpcValue)),
                'Edits the post of a a certain ID. Parameters: postID, username, password, post data struct, publish'
            ),

            // returns: bool - parameters: API key (ignored), $postid, $username, $password, $publish (ignored - what the hell is this doing here?!)
            $this->createFunction(
                "deletePost",
                function($req){return $this->deletePost($req);},
                array(array(Value::$xmlrpcBoolean, Value::$xmlrpcValue, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcValue)),
                'Deletes a post, returns true if deleted. Parameters: appKey, postID, username, password, publish bool (ignored)'
            ),
        );
    }

    public function getPost(Request $req) {
        $params = $this->encoder->decode($req);

        $postID = $params[0];
        $post = $this->queryBuilder->ArticleQuery()->findPK($postID);

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

        return new Response($this->encoder->encode($postArr));
    }

    public function getRecentPosts(Request $req) {
        $params = $this->encoder->decode($req);

        $postCount = $params[3];

        if (strlen($postCount) < 1)
            $postCount = 10;

        $posts = $this->queryBuilder->ArticleQuery()->lastCreatedFirst()->limit($postCount)->find();

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

    public function getCategories(Request $req) {
        $params = $this->encoder->decode($req);

        $username = $params[1];
        $password = $params[2];

        $categoriesArr = null;

        $categories = $this->queryBuilder->CategoryQuery()->find();

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
        $post->setCategory($this->queryBuilder->CategoryQuery()->findOneBySlug($cat) ??
            $this->queryBuilder->CategoryQuery()->findOne());
        $post->setTitle($postStruct["title"]);
        $post->setBody(html_entity_decode($postStruct["description"]));
        $post->setCreatedAt($postStruct["dateCreated"] ?? new DateTime());

        // Tag parsing
        $tagArray = explode(',', $postStruct['mt_keywords']) ?? [];
        $this->logger->debug("Parsed tag bundle", $tagArray);

        foreach ($tagArray as $tag) {
            $tag = trim($tag, ', ');
            $exists = $this->queryBuilder->TagQuery()->setIgnoreCase(true)->findOneByName($tag);
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

        $post = $this->queryBuilder->ArticleQuery()->findOneById($postID);
        $post->setDraft(!$publish);
        $originalVersion = $post->getVersion();
        // Allows direct use of mt.publishPost without any real modification (see publishPost())
        if($postStruct != false) {
            $category = "";

            if (!empty($category)) {
                $category = $postStruct["categories"][0];
                $post->setCategory($this->queryBuilder->CategoryQuery()->findOneBySlug($category));
            }
            $post->setTitle($postStruct["title"]);
            $post->setBody(html_entity_decode($postStruct["description"]));

            // Tag parsing
            $tagArray = explode(',', $postStruct['mt_keywords']);
            $this->logger->debug("Parsed tag bundle", $tagArray);

            foreach ($tagArray as $tag) {
                $tag = trim($tag, ',');
                $exists = $this->queryBuilder->TagQuery()->setIgnoreCase(true)->findOneByName($tag);
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
                $post->getVersion(), 'columns', null,
                ['UpdatedAt', 'CreatedAt', 'Slug', 'BodyHTML', 'Body'])]);

        return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value("true", "boolean"));
    }
}