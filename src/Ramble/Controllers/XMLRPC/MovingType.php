<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 15/07/2017
 * Time: 22:56
 */

namespace Ramble\Controllers\XMLRPC;


use PhpXmlRpc\Request;
use PhpXmlRpc\Value;

class MovingType extends Metaweblog
{
    protected function getNamespace() : string{
        return "mt";
    }

    public function getServiceDefinitions(): array {
        return array(
            // returns: bool - parameters: $postId, $username, $password
            $this->createFunction(
                "publishPost",
                function($req){return $this->publishPost($req);},
                array(array(Value::$xmlrpcBoolean, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
                'Publishes a certain post. Parameters: postID, username, password'
            ),

            // returns: bool - parameters: $postId, $username, $password, struct
            $this->createFunction(
                "setPostCategories",
                function($req){return $this->setPostCategory($req);},
                array(array(Value::$xmlrpcBoolean, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcArray)),
                'Edits the post of a a certain ID. Parameters: postID, username, password, struct of categories (only parses the primary or first category)'
            ),

            $this->createFunction(
                "getRecentPostTitles",
                function($req){return $this->getRecentPosts($req);},
                array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcInt)),
                'Gets a certain number of posts, ordered by recency. Parameters: blogID, username, password, noOfPosts'
            ),

            // returns: array - blogID, username, password, noOfPosts
            $this->createFunction(
                "getPostCategories",
                function($req){return $this->getPostCategories($req);},
                array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
                'Gets a certain number of posts, ordered by recency. Parameters: blogID, username, password, noOfPosts'
            ),

            // returns: array - parameters blogID, username, password
            $this->createFunction(
                "getCategoryList",
                function($req){return $this->getCategories($req);},
                array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
                "Returns [categories]. Parameters: blogID, username, password"
            ),

            // Gets trackbacks/pings on a post, not implemented currently.
            $this->createFunction(
                "getTrackbackPings",
                function($req){return $this->returnValue($req, false, 501);},
                array(array(Value::$xmlrpcArray, Value::$xmlrpcValue)),
                "Returns array(['pingTitle', 'pingURL', 'pingIP']). Parameters: postID"
            ),

            // Not supported by WP, will probably ignore it
            $this->createFunction(
                "supportedTextFilters",
                function($req){return $this->returnValue($req, false, 501);},
                array(array(Value::$xmlrpcArray)),
                "Returns array(['pingTitle', 'pingURL', 'pingIP']). Parameters: postID"
            ),
        );
    }

    // Parameters: postID, username, password
    public function getPostCategories(Request $req){
        $params = $this->encoder->decode($req);

        $id = $params[0];

        $post = $this->queryBuilder->ArticleQuery()->findOneById($id);

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

    public function publishPost(Request $req){
        $params = $this->encoder->decode($req);

        $username = $params[1];
        $password = $params[2];

        if (!$this->passwordAdmin($username, $password))
            return $this->badAuth($req);

        $wrappedReq = new Request($req->methodname);
        $wrappedReq->addParam(new Value($params[0], Value::$xmlrpcInt));
        $wrappedReq->addParam(new Value($params[1], Value::$xmlrpcString));
        $wrappedReq->addParam(new Value($params[2], Value::$xmlrpcString));
        $wrappedReq->addParam(new Value(false, Value::$xmlrpcBoolean));
        $wrappedReq->addParam(new Value(true, Value::$xmlrpcBoolean));

        return $this->editPost($wrappedReq);
    }

    // returns: bool - parameters: postId, username, password, struct
    public function setPostCategory(Request $req){
        $params = $this->encoder->decode($req);

        $id = $params[0];
        $username = $params[1];
        $password = $params[2];
        $categories = $params[3];

        if (!$this->passwordAdmin($username, $password))
            return $this->badAuth($req);

        $post = $this->queryBuilder->ArticleQuery()->findPK($id);

        if($post == null)
            return $this->notFound($req);

        if(!is_array($categories)) {
            return $this->returnValue($req, false, 400);
        }

        $prime = null;
        $first = true;

        // Iterates over each category until it finds a valid match by id or slug (i.e. the first valid category)
        foreach ($categories as $category) {
            if(isset($category[0])) {
                if ($first) {
                    $prime = $this->queryBuilder->CategoryQuery()->findOneById($category[0]) ??
                        $this->queryBuilder->CategoryQuery()->findOneBySlug($category[0]);
                    $first = false;
                } else if (isset($category[1])) {
                    if (boolval($category[1])) {
                        $prime = $this->queryBuilder->CategoryQuery()->findOneById($category[0]) ??
                            $this->queryBuilder->CategoryQuery()->findOneBySlug($category[0]);
                        break;
                    }
                }
            }
        }

        $post->setCategory($prime);
        return $this->returnValue($req, true);
    }


}