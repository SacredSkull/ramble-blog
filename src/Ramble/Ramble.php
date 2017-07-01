<?php

namespace Ramble;

use Interop\Container\ContainerInterface;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Propel\Runtime\Propel;
use Ramble\Controllers\Router;
use Ramble\Models\Cacher;
use Ramble\Models\Redis;
use RedisException;
use Slim\Views\Twig;
use Twig_Extension_Debug;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/Config/Propel/generated-conf/config.php';

class Ramble {
	public static $DEBUG = false;
	public static $CERT_AUTH = false;
	public static $BYPASS_AUTH = false;
	public static $SITE_ROOT = "";
	public static $WINDOWS = false;
	/* @var $app \Slim\App */
	private $app = null;

    public function __construct() {
	    date_default_timezone_set("Europe/Belfast");

	    if ($_SERVER['HTTP_HOST'] ?? "" !== "sacredskull.net") {
	    	static::$DEBUG = true;
	    }

	    if (static::$DEBUG == true) {
		    ini_set('display_errors', 'On');
		    error_reporting(E_ALL);
	    } else {
		    ini_set('display_errors', 'Off');
		    error_reporting(0);
		    set_error_handler(null);
	    }

	    static::$SITE_ROOT = realpath(dirname(__FILE__));
	    static::$WINDOWS = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

	    $this->app = $this->init();
    }

    private function init() : \Slim\App {

    	$app = new \Slim\App(["settings" => require __DIR__ . "/Config/Slim/config.php"]);

	    $container = $app->getContainer();

	    $container['ramble'] = function (ContainerInterface $c) {
		    $sayings = explode("\n", file_get_contents(Ramble::getPublicDir() . '/etc/skull-phrases.txt'));
		    $random = rand(0, sizeof($sayings)-1);

	    	return [
			    'quote' => $sayings[$random],
			    'debug' => static::$DEBUG,
			    'admin' => $this->isAdmin(),
		    ];
	    };

	    $container['memoryDB'] = function (ContainerInterface $c) {
		    try {
			    return new Redis($c->get('settings')['redis']['host'], $c->get('settings')['redis']['port']);
		    } catch (RedisException $exception) {
			    return new Cacher();
		    }
	    };

	    $container['auth'] = require __DIR__ . "/Config/auth.php";

	    // Monolog
		/* Lazy loading is nice when it works...
		 * Propel seems to locate a logger by name before actually accessing the logger object; thus it is instantiated
		 * too late and fails.
		 * In this case, the delegate is not stored - rather its result - hence the wrapping ()s.
		 * Huh? I could've just assigned it directly? Sure, but then I wouldn't get to use an anon function :(
	     */

        /** @var Logger $container['logger'] */
        $container['logger'] = (function (ContainerInterface $c) {
		    $loggerSettings = $c['settings']['logger'];
		    $logger = new Logger($loggerSettings['name']);
		    //$logger->pushProcessor(new UidProcessor());
            if(is_writable($loggerSettings['path']))
                $logger->pushHandler(new RotatingFileHandler($loggerSettings['path'], 2, $c['ramble']['debug']? Logger::DEBUG : Logger::INFO));
            else
                $logger->pushHandler(new ErrorLogHandler());
		    return $logger;
        })($container);

	    // Twig
        /** @var Twig $container['view'] */
        $container['view'] = function (ContainerInterface $c) {
		    $view = new \Slim\Views\Twig($c->get('settings')['renderer']['template_path'], [
			    'cache' => self::$DEBUG ? false : $c->get('settings')['renderer']['cache'],
			    'debug' => self::$DEBUG,
			    'autoescape' => false
		    ]);

			$view->addExtension(new \Slim\Views\TwigExtension(
			    $c['router'],
			    $c['request']->getUri()
		    ));

		    $view->addExtension(new Twig_Extension_Debug());
		    $view->addExtension(new \Twig_Extensions_Extension_Text());
		    $view->addExtension(new \Ramble\Twig\HTMLTruncaterExtension());
		    $view->addExtension(new \Ramble\Twig\ExecutionTimeExtension());
		    return $view;
	    };

		//$deprecations = new \Twig\Util\DeprecationCollector($container['view']->getEnvironment());
		//print_r($deprecations->collectDir('/mnt/c/Users/Peter/GitHub/blog/src/Ramble/Templates'));

	    // Register provider
	    $container['flash'] = function () {
		    return new \Slim\Flash\Messages();
	    };

	    Propel::getServiceContainer()->setLogger('defaultLogger', $container['logger']);
	    return $app;
    }

    public function __invoke() {
	    Router::pave($this->app);
	    return $this->app->run();
    }

    public static function getPublicDir() {
    	return __DIR__ . "/../../Public";
    }

    // Either use cheap network Access Control List or expensive
	// but quality client certificate authorisation
	// Defined via the CERT_AUTH constant.
	// Unfortunate that Cloudflare doesn't support two-way AUTH :(
	// Instead nginx passes the on or off parameter to PHP based on
	// the domain name. Obviously only debug/testing domains have this enabled.
	/**
	 * @return bool
	 * @deprecated
	 */
	function isAdmin() {
		if (static::$CERT_AUTH) {
			if (($_SERVER['HTTPS'] == "on" && $_SERVER['VERIFIED'] == "SUCCESS") || $_SERVER['VERIFIED'] == "OVERRIDE") {
				return true;
			}
			return false;
		} else if(static::$BYPASS_AUTH){
			return true;
		} else {
			// use a cookie for auth?
			$allowedips = array('127.0.0.1', '192.168.1.102');
			$ips = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
			if (in_array($ips, $allowedips)) {
				return true;
			}

			return false;
		}
	}
}
