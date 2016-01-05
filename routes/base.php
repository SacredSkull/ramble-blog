<?php

$additionalFonts = array();

$homepageHandler = function ($page = 1) use ($app, $quote) {
    $maxPerPage = 10;

    $categories = CategoryQuery::create()
    ->setQueryKey('get_all_categories')
    ->find();

    // Paginate() is currently not compatible with setQueryKey, and only caches the first
    // count query, which is useless because then it causes Twig to throw an exception
    // because propel threw an exception. It was horrible to diagnose and you'd better take
    // your own word for it!
    //
    // TL;DR - paginate() & setQueryKey() do not play well together currently!
    $posts = ArticleQuery::create()
        //->setQueryKey('homepage')
        ->orderById('DESC')
        ->filterByDraft(false)
        ->paginate($page, $maxPerPage);

    $maxPages = ceil($posts->getNbResults() / $maxPerPage);
    $pagelist = $posts->getLinks(5);

    if ($page == 1 && $app->request()->getPathInfo() != "/") {
        $app->redirect('/');
    }

    if ($page > $maxPages && $maxPages != 0) {
        $app->flash('denied', "I've failed you senpai.. I haven't got that many post pages!");
        $app->redirect('/');
    }

    $app->render('home.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        'posts' => $posts,
        'current_page' => $page,
        'page_list' => $pagelist,
        'categories' => $categories,
        'max_pages' => $maxPages,
    ));
};//https://jsfiddle.net/rudyjahchan/u5c4k1xg/

$app->get('/page/:page(/)', $homepageHandler)->conditions(array(
    'page' => '\d{1,4}',
));

$app->get('/', $homepageHandler);
