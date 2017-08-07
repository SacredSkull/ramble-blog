<?php
/**
 * Created by PhpStorm.
 * User: sacredskull
 * Date: 22/08/16
 * Time: 22:14
 */

namespace Ramble\Controllers;

use DebugBar\Bridge\MonologCollector;
use DebugBar\Bridge\Propel2Collector;
use DebugBar\Bridge\Twig\TraceableTwigEnvironment;
use DebugBar\Bridge\Twig\TwigCollector;
use DebugBar\StandardDebugBar;
use Psr7Middlewares\Middleware;
use Psr7Middlewares\Middleware\TrailingSlash;
use Ramble\Controllers\XMLRPC\XMLRPCServer;
use Ramble\Ramble;
use Slim\App;
use Propel\Runtime\Propel;

class Router {
	private function __construct(){ }

	/**
	 * @param App $app
	 */
	public static function pave(App $app) {

        // Middleware
        $app->add(Middleware::TrailingSlash(false));
        $app->add(Middleware::FormatNegotiator());
        // Debug bar simply does not work
//        if(Ramble::$DEBUG) {
//            $debugBar = new StandardDebugBar();
//            /*
//            $debugBar->addCollector(new MonologCollector($app->getContainer()->get('logger')));
//            $debugBar->addCollector(new TwigCollector(new TraceableTwigEnvironment(
//                $app->getContainer()
//                    ->get('view')
//                    ->getEnvironment()
//            )));*/
//            //$debugBar->addCollector(new Propel2Collector(Propel::getConnection()));
//            $mw = Middleware::DebugBar($debugBar);
//            $mw->captureAjax(false);
//            $app->add($mw);
//        }

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
		$app->any('/xmlrpc[.php]', XMLRPCServer::class)->setName("XMLRPC");
		$app->get('/rsd[.xml]', XMLRPCServer::class . ":rsdRender")->setName("RSD");
	}
}
