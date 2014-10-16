<?php

define('DEBUG', true);
define('WIREFRAME', false);

// Composer
require './vendor/autoload.php';
// Constants
require './restricted/constants.php';
// Propel auto-conf
require './generated-conf/config.php';
// BBCode Twig Extension
require './lib/Twig_Extension_BBCode.php';
// Defined BBCodes
require './lib/bbcodes.php';

if(DEBUG == true)
{
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}
else
{
    ini_set('display_errors', 'Off');
    error_reporting(0);
}

$defaultTheme = new ThemeQuery();
if(!$defaultTheme->findPK(1)){
   	$theme = new Theme();
   	$theme->setName('Default');
   	$theme->setThemeRoot('/');
   	$theme->save();
}

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig(),
	'templates.path' => './templates'
));

$view = $app->view();

$view->parserOptions = array(
    'cache' => dirname(__FILE__) . '/cache',
    'debug' => DEBUG,
);

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
    new Twig_Extension_BBCode($arrayBB)
);

$sayings = explode("\n", file_get_contents('include/etc/skull-phrases.txt'));
$random = rand(0,sizeof($sayings)-1);
$quote = $sayings[$random];

$app->get('/', function () use ($app, $arrayBB, $defaultTheme, $quote) {



    /*
	$newPost = R::dispense('post');
	$newPost->title = "Interesting Title To Inspire Readers";
	$newPost->body = "[b]Hi![/b] This is an [i]initial post to test[/i] the actual script to ensure it's working! This would be an interesting, and hopefully, [deleted]original[/deleted] nothing-new-under-the-sun post about something I'm interested in. Whether or not anyone else is interested is another story!";
	$newPost->date = date('Y-m-d H:i:s');
    $newPost->poll = true;
	$id = R::store($newPost);

	$newestPost = R::findOne('post', 'ORDER BY id DESC');

	$newestPost = array(
		'title' => $newestPost->title,
		'body' => bbcode_parse($arrayBB, $newestPost->body),
		'date' => date('H:i l d, F o', strtotime($newestPost->date)),
	);
    */


    $post = new Article();
    $post->setTitle('Inspiring title to make people read it!');
    $post->setBody("[b]Hi![/b] <a href='#'>Dodgy link!</a>This is an [i]initial post to test[/i] the actual script to ensure it's working! This would be an interesting, and hopefully, [deleted]original[/deleted] nothing-new-under-the-sun post about something I'm interested in. Whether or not anyone else is interested is another story!");
    $preparedFromWebFromArray = array('New Post', '#Excited');
    $tagArray = implode(',', $preparedFromWebFromArray);
    $post->setTags($tagArray);
    $post->setTheme($defaultTheme->findPK(1));
    $post->save();

    $app->render('home.php', array(
		'debug' => DEBUG,
		'wireframe' => WIREFRAME,
		'skull_greeting' => /*$quote*/ $post->getSlug(),
		'newestpost' => $post
	));
});

$app->get('/:slug', function($slug) use ($app, $arrayBB, $quote) {
    $post = ArticleQuery::create()->findOneBySlug($slug);
    $app->render('home.php', array(
        'debug' => DEBUG,
        'wireframe' => WIREFRAME,
        'skull_greeting' => $quote,
        'newestpost' => $post
    ));
});

$app->run();

?>
