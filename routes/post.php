<?php

/*
 * SINGULAR POST ROUTES
*/

// Find post by ID
$app->get('/id/:idArticle(/)', function ($idArticle) use ($app, $quote) {
    $post = ArticleQuery::create()->findPK($idArticle);
    $app->render('post.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        'post' => $post,
        'additionalFonts' => array($post->getCategory()->getFont()),
    ));
})->conditions(array(
    'idArticle' => '\d{1,10}',
));

// Find post by slug, split by date
$app->get('/:year/:month/:day/:slugArticle(/)', function ($year, $month, $day, $slugArticle) use ($app, $quote) {
    $slug = $year."-".$month."-".$day."_".$slugArticle;

    $post = ArticleQuery::create()->findOneBySlug($slug);

    if ($post == null) {
        // 404 page...
        $app->notFound();
    }

    $app->render('post.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        'post' => $post,
        'additionalFonts' => array($post->getCategory()->getFont()),
    ));
})->conditions(array(
    'year' => '(19|20)\d\d',
    'month' => '\d\d',
    'day' => '\d\d',
    'slugArticle' => '[a-zA-Z0-9_.-]+',
));
