<?php

use Aws\S3\S3Client;

$app->group('/api', function () use ($app) {
    $app->get('/post', function () use ($app) {
        $app->redirect('/api/posts/', 301);
    });
    $app->get('/posts', function () {
        $allPosts = ArticleQuery::create()->find();
        $posts[] = null;
        foreach ($allPosts as $post) {
            $title_js_ready = jsFriendly($post->getTitle());
            $category_js_ready = jsFriendly($post->getCategory()->getName() . " (" . $post->getCategory()->getFont() . ")");
            $posts[$post->getId()] = array('title' => $title_js_ready, 'category' =>  $category_js_ready, 'id' => $post->getId() );
        }
        unset($posts[0]);
        echo json_encode((object) $posts);
    });
    $app->get('/post/:id', function ($id) {
        // TODO: API output for specific post ID
    })->conditions(array('id' => '\d{1,10}'));

    $app->get('/post/:slugArticle', function ($slugArticle) {
        // TODO: API input for specific post slug
    });

    $app->get('/tag/:id', function ($name) {
        // TODO: API output for specific tag
    });
    $app->post('/tag/:id', function ($name) {
        // TODO: API input for specific post tag
    });
    $app->get('/tag/:id/posts', function ($name) {

    });
    // List categories
    $app->get('/category/:name', function ($name) use ($app) {
        $category = CategoryQuery::create()->findOneBySlug($name);
        $allPosts = ArticleQuery::create()->filterByCategory($category)->find();
        $posts[] = null;
        foreach ($allPosts as $post) {
            $title_js_ready = jsFriendly($post->getTitle());
            $category_js_ready = jsFriendly($post->getCategory()->getName());
            $posts[$post->getId()] = array('title' => $title_js_ready, 'category' =>  $category_js_ready, 'id' => $post->getId() );
        }
        unset($posts[0]);
        echo json_encode((object) $posts);
    });
});

$app->get('/upload/:id', function ($id) use ($app) {
    isAdmin() ? $app->redirect('/admin/'.$id) : $app->notFound();
})->conditions(array('post' => '\d{1,10}'));

$app->post('/upload/:id', function ($id) use ($app) {
    if (isAdmin()) {
        $local_path = SITE_ROOT."/images/uploads/".$id."/".str_replace(' ', '_', $_FILES['file']['name']);
        $remote_path = "images/".$id."/".str_replace(' ', '_', $_FILES['file']['name']);

        // Trivia: @ operator suppresses error messages!
        @mkdir(SITE_ROOT."/images/uploads/".$id);

        $temp = $_FILES['file']['tmp_name'];
        move_uploaded_file($temp, $local_path);

        if (USING_WINDOWS) {
            // Use ugly, inefficient exec() work-around for PATH issues & general DLL nonsense on Windows.
            exec("E:\\WPNXM\\bin\\imagick\\convert.exe ".$local_path." -resize 600x600 ".$local_path);
        } else {
            // TODO: Set up linux imagick processing
            die("This section hasn't been set up for proper linux operation. Please complete the
                relevant ImageMagick / GraphicsMagick code");
        }

        $client = S3Client::factory(array(
            'key' => 'AKIAJ6LFRWILE2M7YCGA',
            'secret' => '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',
        ));

        $result = "";
        try {
            $result = $client->putObject(array(
                'Bucket' => 'sacredskull-blog',
                'Key'          => $remote_path,
                'SourceFile'   => $local_path,
                'ContentType'  => $_FILES['file']['type'],
                'ACL'          => 'public-read',
                'StorageClass' => 'STANDARD',
                'Metadata'     => array(
                    'category' => 'image',
                ),
            ));
            echo '{"url": "'.$remote_path.'"}';
        } catch (S3Exception $e) {
            // TODO: Use keyword "status" rather than error for AJAX return codes, such as 200 OK and so on.
            echo '{"error": "'.$e->getMessage().'"}';
        }
    } else {
        $app->flash('denied', "Sorry, you aren't allowed in ".$req->getResourceUri()."!");
        $app->redirect('/');
    }
})->conditions(array('post' => '\d{1,10}'));
