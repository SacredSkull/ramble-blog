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
use Ramble\Models\Category;

class Wordpress extends MovingType {
    protected function getNamespace() : string{
        return "wp";
    }

    public function getServiceDefinitions(): array {
        return array(
            $this->createFunction(
                "getUsersBlogs",
                function($req){return $this->wpGetBlog($req);},
                array(array(Value::$xmlrpcArray, Value::$xmlrpcString, Value::$xmlrpcString)),
                'Gets blog info'
            ),

            // returns: int (category ID) - parameters blogID, username, password, category struct
            $this->createFunction(
                "newCategory",
                function($req){return $this->newCategory($req);},
                array(array(Value::$xmlrpcInt, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcString, Value::$xmlrpcStruct)),
                "Creates a category. Parameters: blogID, username, password, category struct/array name"
            ),

            $this->createFunction(
                "getCategories",
                function($req){return $this->getCategories($req);},
                array(array(Value::$xmlrpcArray, Value::$xmlrpcValue, Value::$xmlrpcString, Value::$xmlrpcString)),
                "Returns [categories]. Parameters: blogID, username, password"
            )
        );
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

    public function newCategory(Request $req) {
        $params = $this->encoder->decode($req);

        $username = $params[1];
        $password = $params[2];
        $categoryStruct = $params[3];

        if (!$this->passwordAdmin($username, $password)) {
            return $this->badAuth($req);
        }

        if(!isset($categoryStruct['name']) || !empty($categoryStruct[0])){
            return $this->returnValue($req, false, 400);
        }

        $name = $categoryStruct['name'] ?? $categoryStruct[0];

        $category = $this->queryBuilder->CategoryQuery()->findOneByName($name);

        if ($category == null) {
            $category = new Category();
            $category->setName($name);
        }

        $category->save();
        $this->logger->info('Category created', ['ID' => $category->getId(), 'Title' => $category->getName()]);

        return new \PhpXmlRpc\Response(new \PhpXmlRpc\Value($category->getId(), "int"));
    }
}