<?php
/**
 * Created by PhpStorm.
 * User: sacredskull
 * Date: 22/08/16
 * Time: 22:14
 */

namespace Ramble\Controllers;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr7Middlewares\Middleware\FormatNegotiator;
use Psr7Middlewares\Middleware\Piwik;
use Psr7Middlewares\Middleware\TrailingSlash;
use Slim\App;

class Router {
	private function __construct(){ }

	/**
	 * @param App $app
	 */
	public static function pave(App $app){
		// Homepage handlers
		$app->get('/', BaseController::class)->setName("GET_HOME");
		$app->get('/{year:20\d\d}/{month:\d\d}/{day:\d\d}/{slugArticle:[a-zA-Z0-9_.-]+}[/]', BaseController::class . ":filterByPost")->setName("GET_HOME_FILTER_POST");
		$app->get('/category/{category}[/page/{page:\d{1,4}}]', BaseController::class . ":filterByCategory")->setName("GET_HOME_FILTER_CATEGORY");

		// I feel there should be a better way to do this.
		$app->group('/{year:20\d\d}', function() use ($app) {
			$app->get('[/page/{page:\d{1,4}}]', BaseController::class . ":filterByDate");
			$app->group('/{month:\d\d}', function() use ($app) {
				$app->get('[/page/{page:\d{1,4}}]', BaseController::class . ":filterByDate");
				$app->group('/{day:\d\d}', function() use ($app) {
					$app->get('[/page/{page:\d{1,4}}]', BaseController::class . ":filterByDate")->setName("GET_HOME_FILTER_DATE");
				});
			});
		});

		// XMLRPC API handler
		$app->any('/xmlrpc[.php]', XMLRPC_Controller::class)->setName("XMLRPC");

		// Middleware
		$app->add(new TrailingSlash(false));
	}
}