<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 04/07/2017
 * Time: 00:58
 */

namespace Ramble\Tests;

use PhpXmlRpc\Response;

class MetaweblogTest extends BloggerTest
{
    public $methodNamespace = 'metaWeblog';

    public function testGetCategoriesGoodAuth(Response $response = null) {
        $this->markTestIncomplete();
    }

    public function testGetCategoriesBadAuth(Response $response = null) {
        $this->markTestIncomplete();
    }
}