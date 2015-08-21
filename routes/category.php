<?php

// Find all posts for a category
$app->get('/category/:categorySlug(/:page)', function ($categorySlug, $page = 1) use ($app, $quote) {

    $categories = CategoryQuery::create()
            ->setQueryKey('get_all_categories')
            ->find();

    $specificCategory = CategoryQuery::create()
        ->findOneBySlug($categorySlug);

    $maxPerPage = 10;

    $posts = ArticleQuery::create()
        //->setQueryKey('homepage')
        ->orderById('DESC')
        ->filterByCategory($specificCategory)
        ->filterByDraft(false)
        ->paginate($page, $maxPerPage);

    $maxPages = ceil($posts->getNbResults() / $maxPerPage);
    if ($specificCategory == null) {
        $app->flash('denied', "$categorySlug hasn't been created yet");
        $app->redirect('/');
    } elseif (!$maxPages > 0) {
        $app->flash('denied', "I haven't posted anything in $categorySlug");
        $app->redirect('/');
    }

    $pagelist = $posts->getLinks(5);

    $app->render('home.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        'posts' => $posts,
        'current_page' => $page,
        'page_list' => $pagelist,
        'pagination_url' => '/category/'.$categorySlug.'/',
        'categories' => $categories,
        'max_pages' => $maxPages,
    ));

});
