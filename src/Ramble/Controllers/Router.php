<?php
/**
 * Created by PhpStorm.
 * User: sacredskull
 * Date: 22/08/16
 * Time: 22:14
 */

namespace Ramble\Controllers;

use Psr7Middlewares\Middleware\TrailingSlash;
use Slim\App;

class Router {
	private function __construct(){ }

	/**
	 * @param App $app
	 */
	public static function pave(App $app){
		// Middleware
		$app->add(new TrailingSlash(false));

		/*
		 * Homepage handlers
		 */
		$app->get('/', Home::class)->setName("GET_HOME");
		// Note: slugs MUST have zero ('0') placeholders for single digit month/day (hence the \d\d instead of \d\d??)
		$app->get('/{year:20\d\d}/{month:\d\d}/{day:\d\d}/{slugArticle:[a-zA-Z0-9_.-]+}', HomeFilter::class . ":filterByPost")->setName("GET_HOME_FILTER_POST");
		$app->get('/category/{category}[/page/{page:\d{1,4}}]', HomeFilter::class . ":filterByCategory")->setName("GET_HOME_FILTER_CATEGORY");
		$app->get('/tag/{tag}[/page/{page:\d{1,4}}]', HomeFilter::class . ":filterByTag")->setName("GET_HOME_FILTER_TAG");

		// I feel there should be a better way to do this.
		$app->group('/{year:20\d\d}', function() use ($app) {
			$app->get('[/page/{page:\d{1,4}}]', HomeFilter::class . ":filterByDate");
			$app->group('/{month:\d{1,2}}', function() use ($app) {
				$app->get('[/page/{page:\d{1,4}}]', HomeFilter::class . ":filterByDate");
				$app->group('/{day:\d{1,2}}', function() use ($app) {
					$app->get('[/page/{page:\d{1,4}}]', HomeFilter::class . ":filterByDate")->setName("GET_HOME_FILTER_DATE");
				});
			});
		});

		/*
		 * Portfolio
		 */
		$app->get('/portfolio', Portfolio::class);

		/*
		 * XMLRPC API
		 */
		$app->any('/xmlrpc', XMLRPC_Controller::class)->setName("XMLRPC");
	}
}