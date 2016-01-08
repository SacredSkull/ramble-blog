<?php

use Propel\Runtime\Propel;
use Map\ArticleTableMap;
use Map\CategoryTableMap;

$app->get('/test(/:count)', function ($count) use ($app, $quote, $logger) {

    $generator = Faker\Factory::create('en_UK');
    if(empty($count))
        $count = 20;

    if (isAdmin()) {
        $postCon = Propel::getWriteConnection(ArticleTableMap::DATABASE_NAME);
        $categoryCon = Propel::getWriteConnection(CategoryTableMap::DATABASE_NAME);

        $category = CategoryQuery::create()->findOneByName("Fake");
        if($category == NULL){
            $category = new Category();
            $category->setName('Fake');
            $category->setRoot('/');
            $category->setColour('gold');
            $category->save($categoryCon);
        }

        $postCon->beginTransaction();
        $categoryCon->beginTransaction();
        for ($i = 1; $i < $count; $i++) {
            if ($i == ($count - 1)) {
                $post = new Article();
                $post->setDraft(false);
                $post->setTitle('An article that was actually typed!');
                $post->setBody("#A post's header\n##Some subtext for the header.\n**Finally, a bit of *bold***\n\n{img a:My namesake's avatar t:The not-so household logo of SacredSkull}test.jpg{/img}");
                $post->setCategory($category);
                $post->save($postCon);
                break;
            }

            $post = new Article();
            $post->setDraft(false);
            $post->setTitle($generator->realText(25));
            $post->setBody($generator->realText(4000));
            $post->setCreatedAt($generator->dateTimeThisDecade());
            $post->setCategory($category);
            $post->save($postCon);
        }
        $postCon->commit();
        $categoryCon->commit();
    }

    $logger->info("All test values inserted correctly!");

    $app->render('test.php', array(
        'admin' => isAdmin(),
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        //'posts' => $posts,
        //'json_category' => $jsonCategorys
    ));
})->conditions(array(
    'page' => '\d{1,4}',
));;
