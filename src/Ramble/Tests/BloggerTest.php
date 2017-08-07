<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 04/07/2017
 * Time: 00:58
 */

namespace Ramble\Tests;

use PhpXmlRpc\Response;

class BloggerTest extends XmlRpcTest {
    public $methodNamespace = 'blogger';

    public function testGetUsersBlogsGoodAuth(Response $response = null) {
        $response = $response ?? $this->getUsersBlogs(true);
        $this->assertGoodAuth($response);

        self::assertNotEmpty($response->value()->scalarval(),
            "Wasn't given an array of blog info (empty array or non-array value provided): 
            {$response->value()->serialize('UTF-8')}");

        $rawArray = $response->value()->scalarval()[0];
        self::assertArrayContainsKeys(['blogid', 'url', 'blogName', 'isAdmin', 'xmlrpc'], $rawArray);
    }

    public function testGetUsersBlogsBadAuth(Response $response = null) {
        $response = $response ?? $this->getUsersBlogs(false);
        $this->assertBadAuth($response);
    }

    public function testDeletePostGoodAuth(Response $response = null) {
        $response = $response ?? $this->deletePost(true);
        $this->assertGoodAuth($response);
    }

    public function testDeletePostBadAuth(Response $response = null) {
        $response = $response ?? $this->deletePost(false);
        $this->assertBadAuth($response);
    }

    public function testGetUserInfoGoodAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testGetUserInfoBadAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testGetPostGoodAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testGetPostBadAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testGetRecentPostsGoodAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testGetRecentPostsBadAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testNewPostGoodAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testNewPostBadAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testEditPostGoodAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testEditPostBadAuth(Response $response = null) {
        $this->markTestIncomplete();
    }
}