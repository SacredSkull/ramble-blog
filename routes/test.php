<?php

$app->get('/test(/)', function () use ($app, $quote, $logger) {

    $generator = Faker\Factory::create('en_UK');

    if (isAdmin()) {
        for ($i = 1; $i < 20; $i++) {
            if ($i == 19) {
                $theme = new Theme();
                $theme->setName('A real category!');
                $theme->setRoot('/');
                $theme->setColour('gold');
                $theme->save();

                $post = new Article();
                $post->setDraft(false);
                $post->setTitle('An article that was actually typed!');
                $post->setBody("#A post's header\n##Some subtext for the header.\n**Finally, a bit of *bold***\n\n{img a:My namesake's avatar t:The not-so household logo of SacredSkull}test.jpg{/img}");
                $post->setTheme($theme);
                $post->save();
                break;
            }
            $theme = new Theme();
            $theme->setName($generator->company);
            $theme->setRoot('/');
            $theme->setColour($generator->hexcolor);
            $theme->save();

            $post = new Article();
            $post->setDraft(false);
            $post->setTitle($generator->realText(25));
            $post->setBody($generator->realText(4000));
            $post->setTheme($theme);
            $post->save();
        }
    }

    $logger->info("All test values inserted correctly!");

    $app->render('test.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        //'posts' => $posts,
        //'json_theme' => $jsonThemes
    ));
});
