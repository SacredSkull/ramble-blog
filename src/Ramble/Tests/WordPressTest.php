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

class WordPressTest extends XmlRpcTest {
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

    public function testGetUserBlogsGoodAuth(Response $response = null) {
        $response = $response ?? $this->getUsersBlogs(true);
        $this->assertGoodAuth($response);

        self::assertNotEmpty($response->value()->scalarval(),
            "Wasn't given an array of blog info (empty array or non-array value provided): 
            {$response->value()->serialize('UTF-8')}");

        $rawArray = $response->value()->scalarval()[0];
        self::assertArrayContainsKeys(['blogid', 'url', 'blogName', 'isAdmin', 'xmlrpc'], $rawArray);
    }

    public function testGetCategoriesGoodAuth(Response $response = null) {
        $response = $response ?? $this->GetCategories(true);

        $this->assertGoodAuth($response);
    }
}