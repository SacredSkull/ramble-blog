<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 04/07/2017
 * Time: 01:25
 */

namespace Ramble\Models;


class QueryBuilder {
    public function ArticleQuery() {
        return ArticleQuery::create();
    }

    public function ArticleTagQuery() {
        return ArticleTagQuery::create();
    }

    public function ArticleVersionQuery() {
        return ArticleVersionQuery::create();
    }

    public function CategoryQuery() {
        return CategoryQuery::create();
    }

    public function TagQuery() {
        return TagQuery::create();
    }

    public function VoteQuery() {
        return VoteQuery::create();
    }
}