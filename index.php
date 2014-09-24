<?php

define('DEBUG', true);
define('WIREFRAME', false);
require './vendor/autoload.php';
require './lib/rb.php';
require './restricted/constants.php';

$arrayBB = bbcode_create(array(
		// Italics
		'i' =>		array('type' => BBCODE_TYPE_NOARG, 'open_tag' => '<i>', 'close_tag' => '</i>', 'childs' => 'b'),

		// Bold
		'b'=>		array('type' => BBCODE_TYPE_NOARG, 'open_tag' => '<b>', 'close_tag' => '</b>', 'childs' => 'i'),

		// URL
		'url'=>     array('type' => BBCODE_TYPE_OPTARG, 'open_tag' => '<a href="{PARAM}">', 'close_tag' => '</a>',
						  'default_arg' => '{CONTENT}', 'childs' => 'b,i'),

		// Strikethrough/deleted
		'deleted'=>	array('type' => BBCODE_TYPE_NOARG, 'open_tag' => '<del>', 'close_tag' => '</del>'),
));

R::setup('mysql:host=127.0.0.1;dbname=sacredskull', db_user, db_pass);

if(DEBUG == true)
{
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    R::freeze( false );
}
else
{
    ini_set('display_errors', 'Off');
    error_reporting(0);
    R::freeze( true );
}

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig(),
	'templates.path' => './templates',
	'debug' => false
));

$view = $app->view();

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension()
);

$app->get('/', function () use ($app, $arrayBB) {


	$sayings = explode("\n", file_get_contents('include/etc/skull-phrases.txt'));
	$random = rand(0,sizeof($sayings)-1);

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

	$app->render('home.php', array(
		'debug' => DEBUG,
		'wireframe' => WIREFRAME,
		'skull_greeting' => $sayings[$random],
		'newestpost' => $newestPost
	));
});

$app->run();

R::close();

?>
