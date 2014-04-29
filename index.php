<?php

define('DEBUG', true);

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

require 'vendor/autoload.php';

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig(),
	'templates.path' => './templates'
));

//ini_set('magic_quotes_gpc', 'On');
//ini_set('magic_quotes_runtime', 'On');

$view = $app->view();

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension()
);

$app->get('/', function () use ($app) {
	$sayings = explode("\n", file_get_contents('include/etc/skull-phrases.txt'));
	$random = rand(0,sizeof($sayings)-1);
	if(!DEBUG){
		require 'include/php/cssmin-v3.0.1-minified.php';
		$result = "";
		$files = glob('include/css/*.{css}', GLOB_BRACE);
		foreach($files as $file) {
			$result .= CssMin::minify(file_get_contents($file));
		}
		$app->render('testTemplate.php', array(
			'css_output' => $result,
			'skull_greeting' => $sayings[$random]
		));
	} else {
		$files = glob('include/css/*.{css}', GLOB_BRACE);
		$result = "";
		foreach($files as $file) {
			$result .= html_entity_decode(file_get_contents("$file"));
		}
		$app->render('testTemplate.php', array(
			'css_output' => $result,
			'skull_greeting' => $sayings[$random]
		));
	}
    //echo "You passed me \"" . $name . "\"." ;
});

$app->run();

?>