<?php

$adminHandler = function ($id = -1) use ($app, $defaultCategory, $logger) {
    if (!isAdmin()) {
        $app->notFound();
    }

    $categories = CategoryQuery::create()->find();

    // GET request handler
    if ($app->request->isGet()) {
        if ($id == -1) {
            $post = new Article();
            $post->setCategory($defaultCategory->findPK(1));
            $post->setDraft(true);

            $post->save();

            $app->flash('mode', 'new');
            $app->redirect('/admin/'.$post->getId());
        } else {
            $quote = "  EDITING #".$id."!  ";

            $post = ArticleQuery::create()->findPK($id);
            $app->render('create.php', array(
                'debug' => DEBUG,
                'wireframe' => WIREFRAME,
                'skull_greeting' => $quote,
                'mode' => 'edit',
                'post' => $post,
                'categories' => $categories,
            ));
        }
    } elseif ($app->request->isPost()) {
        if ($id == -1) {
            $app->response->setStatus(400);
            $app->halt();
        }
        try {
            $post = ArticleQuery::create()->findOneById($id);
            $allPostVars = $app->request->post();
            if (!isset($allPostVars['draft'])) {
                $allPostVars['draft'] = false;
            }

            $tagArray = explode(',', $allPostVars['tags']);

            foreach ($tagArray as $tag) {
                $exists = TagQuery::create()->filterByName($tag);
                if ($exists->count() < 1) {
                    $exists = new Tag();
                    $exists->setName(trim($tag));
                    $exists->save();
                } else {
                    $exists = $exists->findOne();
                }
                $post->addTag($exists);
            }

            $category = CategoryQuery::create()->findOneById($allPostVars['category']);

            $post->setCategory($category);
            $post->setTitle($allPostVars['title']);
            $post->setBody($allPostVars['body']);
            $post->setDraft($allPostVars['draft']);
            $post->save();
            echo $post->getId();
            if (!$app->request->isAjax()) {
                //$app->redirect('/');
            }
        } catch (ErrorException $e) {
            $logger->error($e->getMessage());
        }
    } elseif ($app->request->isDelete()) {
        $deletePost = ArticleQuery::create()->findOneById($id);
        $deletePost->delete();
        $app->redirect('/');
    }
};

// Admin page
$app->get('/admin(/:id)', $adminHandler)->conditions(array('id' => '\d{1,10}'));

// Update post by ID
$app->post('/admin/:id', $adminHandler)->conditions(array('id' => '\d{1,10}'));

// Delete post by ID
$app->delete('/admin/:id', $adminHandler)->conditions(array('id' => '\d{1,10}'));
