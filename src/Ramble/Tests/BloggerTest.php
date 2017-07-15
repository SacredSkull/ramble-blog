<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 04/07/2017
 * Time: 00:58
 */

namespace Ramble\Tests;


use Mockery;
use PhpXmlRpc\Encoder;
use PhpXmlRpc\Request;
use PhpXmlRpc\Response;
use PhpXmlRpc\Server;
use PhpXmlRpc\Value;
use Ramble\Controllers\AuthorisationInterface;
use Ramble\Controllers\XMLRPC;
use Ramble\Models\Article;
use Ramble\Models\ArticleQuery;
use Ramble\Models\QueryBuilder;

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
}