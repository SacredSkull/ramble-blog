<?php

$app->get('/category/:slugTheme(/)(:page)', function ($slugTheme, $page = 1) use ($app, $quote, $themes) {
    $maxPerPage = 10;
    $theme = ThemeQuery::create()->findOneBySlug($themeSlug);

    // Paginate() is currently not compatible with setQueryKey, and only caches the first
    // count query, which is useless because then it causes Twig to throw an exception
    // because propel threw an exception. It was horrible to diagnose and you'd better take
    // your own word for it!
    //
    // TL;DR - paginate() & setQueryKey() do not play well together currently!
    $themedPosts = ArticleQuery::create()
        //->setQueryKey('posts_of_particular_theme')
        ->filterByTheme($theme)
        ->paginate($page, $maxPerPage);

    $maxPages = ceil($themedPosts->getNbResults() / $maxPerPage);
    $pagelist = $themedPosts->getLinks(5);

    if ($page == 1 && $app->request()->getPathInfo() != "/category/".$themeSlug) {
        $app->redirect('/category/'.$themeSlug);
    }

    if ($page > $maxPages) {
        $app->flash('denied', "I've failed you senpai.. I haven't got that many post pages!");
        $app->redirect('/'.$themeSlug."/".$maxPages);
    }

    $app->render('home.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        'posts' => $themedPosts,
        'current_page' => $page,
        'page_list' => $pagelist,
        'themes' => $themes,
        'max_pages' => $maxPages,
    ));
});
