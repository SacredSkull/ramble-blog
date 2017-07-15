<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 04/07/2017
 * Time: 01:01
 */

namespace Ramble\Tests;

use PhpXmlRpc\Response;
use PhpXmlRpc\Value;

class WordPressTest extends MetaweblogTest {
    public $methodNamespace = 'wp';

    public function getUsersBlogs(bool $validAuth, $method = 'getUsersBlogs'): Response {
        $this->authMock($validAuth);
        $this->ci->router->shouldReceive('pathFor')->andReturn('xmlrpc');

        $request = $this->createRequest($method, array(
            new Value("blah", Value::$xmlrpcString),
            new Value("asd", Value::$xmlrpcString)
        ));

        return $this->xmlrpcServer->service($request->serialize('UTF-8'), false);
    }

    public function testGetCategoriesGoodAuth(Response $response = null) {
        $response = $response ?? $this->GetCategories(true);

        $this->assertGoodAuth($response);
    }

    public function testGetCategoriesBadAuth(Response $response = null) {
        $response = $response ?? $this->GetCategories(false);

        $this->assertGoodAuth($response);
    }

    public function testGetPostGoodAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testGetPostBadAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testDeletePostGoodAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testDeletePostBadAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testGetRecentPostsGoodAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testGetRecentPostsBadAuth(Response $response = null) {
        $this->markTestIncomplete();
    }
}