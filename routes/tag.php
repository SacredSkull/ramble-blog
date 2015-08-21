<?php

$app->get('/tag/:tag(/)(:page)', function ($tag, $page = 1) use ($app, $quote) {

    $categories = CategoryQuery::create()
            ->setQueryKey('get_all_categories')
            ->find();

    $maxPerPage = 10;
    $selectedTag = TagQuery::create()->findOneByName($tag);

    if ($selectedTag) {
        $posts = ArticleQuery::create()
            ->filterByTag($selectedTag)
            ->filterByDraft(false)
            ->paginate($page, $maxPerPage);

        $maxPages = ceil($posts->getNbResults() / $maxPerPage);
        $pagelist = $posts->getLinks(5);

        if ($page == 1 && $app->request()->getPathInfo() != "/tag/".$tag) {
            $app->redirect('/tag/'.$tag);
        }

        if ($page > $maxPages) {
            $app->flash('denied', "I've failed you senpai.. I haven't got that many post pages!");
            $app->redirect('/tag/'.$tag."/".$maxPages);
        }
    } else {
        $app->flash('denied', "That tag ($tag) doesn't currently exist");
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
});
