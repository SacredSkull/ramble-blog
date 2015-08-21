<?php

/*
 * MULTIPLE/SEARCH POST ROUTES
*/

// Drafts page
$app->get('/drafts(/:page)', function ($page = 1) use ($app, $quote) {
    if (isAdmin()) {
        $maxPerPage = 10;

        $categories = CategoryQuery::create()
        //->setQueryKey('get_all_categories')
        ->find();

        $posts = ArticleQuery::create()
            ->orderById('DESC')
            ->filterByDraft(true)
            ->paginate($page, $maxPerPage);

        $maxPages = ceil($posts->getNbResults() / $maxPerPage);
        $pagelist = $posts->getLinks(5);

        if ($page == 1 && $app->request()->getPathInfo() != "/drafts") {
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
    } else {
        $app->redirect('/');
    }
});

// Find all posts for a specific time period
$app->get('/:year/(:month/(:day/(:page)))', function ($year, $month = -1, $day = -1, $page = 1) use ($app, $quote) {

    $categories = CategoryQuery::create()
        ->setQueryKey('get_all_categories')
        ->find();

    $firstDate = "";
    $secondDate = "";
    $rawDate = "";

    if ($month == -1) {
        $firstDate = new DateTime($year."/01"."/01"." 00:00:00");
        $secondDate = new DateTime($year."/12"."/31"." 23:59:59");

        $rawDate = $firstDate->format('Y');
        $rawDate = "in ".$rawDate;
    } elseif ($day == -1) {
        if ($month > 12 || $month < 1) {
            $month = 1;
        }
        $firstDate = new DateTime($year."/".$month."/01"." 00:00:00");
        $days = $firstDate->format('t');

        $secondDate = new DateTime($year."/".$month."/".$days." 23:59:59");

        $rawDate = $firstDate->format('F, Y');
        $rawDate = "in ".$rawDate;
    } else {
        if ($month > 12 || $month < 1) {
            $month = 1;
        }
        if ($day > 31 || $day < 1) {
            $day = 1;
        }
        $firstDate = new DateTime($year."/".$month."/".$day." 00:00:00");
        $secondDate = new DateTime($year."/".$month."/".$day." 23:59:59");

        $rawDate = $firstDate->format('jS l, F Y');
        $rawDate = "on the ".$rawDate;
    }

    $maxPerPage = 10;

    // See the main home route ("/") for information about why setQueryKey should always be commented out for paginations
    $posts = ArticleQuery::create()
        //->setQueryKey('homepage')
        ->orderById('DESC')
        ->filterByCreatedAt(array('min' => $firstDate, 'max' => $secondDate))
        ->paginate($page, $maxPerPage);

    $maxPages = ceil($posts->getNbResults() / $maxPerPage);
    $pagelist = $posts->getLinks(5);

    if (!$maxPages > 0) {
        $app->flash('denied', "I haven't posted anything $rawDate");
        $app->redirect('/');
    }

    if ($page > $maxPages) {
        $quote = "No more pages!";
        //$app->redirect('/');
    }

    $app->render('home.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        'posts' => $posts,
        'current_page' => $page,
        'page_list' => $pagelist,
        'pagination_url' => '/'.$year."/".$month."/".$day."/",
        'categories' => $categories,
        'max_pages' => $maxPages,
    ));
})->conditions(array(
    'year' => '(19|20)\d\d',
    'month' => '\d\d',
    'day' => '\d\d',
    'page' => '\d{1,10}',
));
