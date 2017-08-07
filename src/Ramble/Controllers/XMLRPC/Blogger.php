<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 15/07/2017
 * Time: 22:56
 */

namespace Ramble\Controllers\XMLRPC;


use DateTime;
use function foo\func;
use PhpXmlRpc\Request;
use PhpXmlRpc\Response;
use PhpXmlRpc\Value;
use Psr\Container\ContainerInterface;

class Blogger extends Service
{
    protected function getNamespace() : string{
        return "blogger";
    }

    public function getServiceDefinitions() : array {
        return $this->functions = [
            // returns: array of struct - parameters: API key, username, password
            $this->createFunction(
                "getUsersBlogs",
                function($req) {return $this->getBlog($req);},
                array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
                'Gets blog info'
            ),

            // returns: struct - parameters: $postid, $username, $password
            $this->createFunction(
                "getPost",
                function($req) {return $this->getPost($req);},
                array(array(Value::$xmlrpcStruct, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
                'Gets a specific post. Parameters: postID, username, password'
            ),

            // returns: array - parameters blogID, username, password, noOfPosts
            $this->createFunction(
                "getRecentPosts",
                function($req) {return $this->getRecentPosts($req);},
                array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcInt)),
                'Gets a certain number of posts, ordered by recency. Parameters: blogID, username, password, noOfPosts'
            ),

            // returns: bool - parameters: API key (ignored), $postid, $username, $password, $publish (ignored - what the hell is this doing here?!)
            $this->createFunction(
                "deletePost",
                function($req) {return $this->deletePost($req);},
                array(array(Value::$xmlrpcBoolean, Value::$xmlrpcValue, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcValue)),
                "Deletes a post, returns true if deleted. Parameters: appKey, postID, username, password, publish bool (ignored)"
            ),
        ];
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
            "xmlrpc" => "http://" . $_SERVER['SERVER_NAME'] .
                str_replace('.php', '', $this->router->pathFor('XMLRPC'))
        ));

        if ($return[0]["isAdmin"] != 1) {
            return $this->badAuth($req);
        }
        return new Response($this->encoder->encode($return));
    }

    // returns: bool - parameters appKey, postID, username, password, (ignored) publish
    public function deletePost(Request $req) {
        $params = $this->encoder->decode($req);

        $postID = $params[1];
        $username = $params[2];
        $password = $params[3];

        $post = $this->queryBuilder->ArticleQuery()->findOneById($postID);

        if (!$this->passwordAdmin($username, $password)) {
            return $this->badAuth($req);
        }

        $post->delete();
        $this->logger->info('Deleted post', ['ID' => $post->getId(), 'Title' => $post->getTitle()]);

        return new Response(new Value("true", "boolean"));
    }

    public function getPost(Request $req) {
        $params = $this->encoder->decode($req);

        $postID = $params[0];
        $post = $this->queryBuilder->ArticleQuery()->findPK($postID);

        if($post == null) {
            return $this->notFound($req);
        }

        $postArr = array(
            "postid" => $post->getId(),
            "userid" => $params[2] ?? '',
            "dateCreated" => new Value($post->getCreatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
            "content" => htmlspecialchars($post->getBodyhtml())
        );

        return new Response($this->encoder->encode($postArr));
    }

    public function getRecentPosts(Request $req) {
        $params = $this->encoder->decode($req);

        $postCount = $params[3];

        if (strlen($postCount) < 1)
            $postCount = 10;
        if(!is_numeric($postCount))
            $postCount = 10;

        $posts = $this->queryBuilder->ArticleQuery()->lastCreatedFirst()->limit($postCount)->find();

        $allPosts = array();

        foreach ($posts as $post) {
            $postArr = array(
                "postid" => $post->getId(),
                "content" => htmlspecialchars($post->getBody()),
                "dateCreated" => new Value($post->getCreatedAt()->format(DateTime::ISO8601), "dateTime.iso8601"),
                "userid" => $params[2] ?? '',
            );

            $allPosts[] = $postArr;
        }

        return new Response($this->encoder->encode($allPosts));
    }
}