<?php

define('DEBUG', true);
define('WIREFRAME', false);
require './vendor/autoload.php';
require './lib/rb.phar';
require './restricted/constants.php';

R::setup('mysql:host=127.0.0.1;dbname=sacredskull',db_user,db_pass);

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
	'templates.path' => './templates'
));

$view = $app->view();

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension()
);

$app->get('/', function () use ($app) {


	$sayings = explode("\n", file_get_contents('include/etc/skull-phrases.txt'));
	$random = rand(0,sizeof($sayings)-1);

	$newPost = R::dispense('post');
	$newPost->title = "HELLO";
	$newPost->body = "Hi! This is an initial post to test the actual script to ensure it's working! This would be an interesting, and hopefully, original post about something I'm interested in. Whether or not anyone else is interested is another story!";
	$newPost->date = date('d-m-y');
	$id = R::store($newPost);

	$newestPost = R::findOne('post', 'ORDER BY id DESC');

	$newestPost = array(
		'title' => $newestPost->title,
		'body' => $newestPost->body,
		'date' => $newestPost->date,
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