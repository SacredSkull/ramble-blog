<?php

$adminHandler = function ($id = -1) use ($app, $defaultTheme) {
    if (!isAdmin()) {
        $app->notFound();
    }

    // GET request handler
    if ($app->request->isGet()) {
        if ($id == -1) {
            $post = new Article();
            $post->setTitle(null);
            $post->setBody(null);
            $preparedFromWebFromArray = array('New Post', '#Excited');
            $tagArray = implode(',', $preparedFromWebFromArray);
            $post->setTags($tagArray);
            $post->setTheme($defaultTheme->findPK(1));
            $post->setDraft(true);

            $post->save();

            $app->flash('mode', 'new');
            $app->redirect('/admin/'.$post->getId());

            $quote = "  CREATION MODE!  ";
            $app->render('create.php', array(
                'debug' => DEBUG,
                'wireframe' => WIREFRAME,
                'skull_greeting' => $quote,
                'mode' => 'new',
            ));
        } else {
            $quote = "  EDITING #".$id."!  ";

            $post = ArticleQuery::create()->findPK($id);
            $app->render('create.php', array(
                'debug' => DEBUG,
                'wireframe' => WIREFRAME,
                'skull_greeting' => $quote,
                'mode' => 'edit',
                'post' => $post,
            ));
        }
    } elseif ($app->request->isPost()) {
        if ($id == -1) {
            $app->response->setStatus(400);
            $app->halt();
        }
        $post = ArticleQuery::create()->findOneById($id);
        $allPostVars = $app->request->post();
        $post->setTitle($allPostVars['title']);
        $post->setBody($allPostVars['body']);
        $post->save();
        echo $post->getId();
        if (!$app->request->isAjax()) {
            //$app->redirect('/');
        }
    }
};

// Admin page
$app->get('/admin(/:id)', $adminHandler)->conditions(array('id' => '\d{1,10}'));

// Update post by ID
$app->post('/admin/:id', $adminHandler)->conditions(array('id' => '\d{1,10}'));
