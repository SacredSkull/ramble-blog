<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

if ($_SERVER['HTTP_HOST'] !== "sacredskull.net") {
    define('DEBUG_SLIM', true);
    define('DEBUG', true);
} else {
    define('DEBUG_SLIM', false);
    define('DEBUG', false);
}
define('WIREFRAME', false);
define('CERT_AUTH', false);
define('BYPASS_AUTH', true);
define('SITE_ROOT', realpath(dirname(__FILE__)));
define('USING_PARSEDOWN', false);
if (defined('USING_WINDOWS')) {
    define('USING_WINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));
}

if (DEBUG == true) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 'Off');
    error_reporting(0);
}

// Composer
require './vendor/autoload.php';
// Propel auto-conf
require './generated-conf/config.php';
// Defined BBCodes, if set

// Either use cheap network access control list or expensive
// but quality client certificate authorisation
// Defined via the CERT_AUTH constant.
// Unfortunate that Cloudflare doesn't support two-way AUTH :(
// Instead nginx passes the on or off parameter to PHP based on
// the domain name. Obviously only certain domains have this turned to
// on.
function isAdmin()
{
    if (CERT_AUTH) {
        if (($_SERVER['HTTPS'] == "on" && $_SERVER['VERIFIED'] == "SUCCESS") || $_SERVER['VERIFIED'] == "OVERRIDE") {
            return true;
        }

        return false;
    } else if(BYPASS_AUTH){
        return true;
    } else {
        // use a cookie for auth?
        $allowedips = array('127.0.0.1', '192.168.1.102');
        $ips = $_SERVER['REMOTE_ADDR'];
        if (in_array($ips, $allowedips)) {
            return true;
        }

        return false;
    }
}

function jsFriendly($string)
{
    return htmlspecialchars($string, ENT_QUOTES);
}

// Generation time.
$GLOBALS['execute_time'] = microtime(true);

$logger = new Logger('defaultLogger');
$logger->pushHandler(new StreamHandler('./logs/propel.log'));
\Propel\Runtime\Propel::getServiceContainer()->setLogger('defaultLogger', $logger);

session_start();

$defaultCategory = new CategoryQuery();
if (!$defaultCategory->findPK(1)) {
    $category = new Category();
    $category->setName('Stuff');
    $category->setRoot('/');
    $category->setColour('#66C4F0');
    $category->save();
}

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig(),
    'templates.path' => './templates',
    'debug' => DEBUG_SLIM,
    'debug.revealHttpVariables' => DEBUG_SLIM,
));

$view = $app->view();

$view->parserOptions = array(
    'cache' => dirname(__FILE__).'/cache',
    'debug' => DEBUG,
);

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
    new \Twig_Extensions_Extension_Text(),
    new \SacredSkull\Blog\TwigExtensionHTMLTruncaterFilter(),
    new \SacredSkull\Blog\TwigExtensionExecutionTime(),
);

$sayings = explode("\n", file_get_contents('include/etc/skull-phrases.txt'));
$random = rand(0, sizeof($sayings)-1);

$quote = $sayings[$random];

preg_match("/\/(?:19|20)\d\d|\/\w*/", $app->request()->getPathInfo(), $path);

require './routes/base.php';

if ($path[0] == "/admin") {
    require './routes/admin.php';
} elseif ($path[0] == "/category") {
    require './routes/category.php';
} elseif ($path[0] == "/tag") {
    require './routes/tag.php';
} elseif ($path[0] == "/api") {
    require './routes/api.php';
} elseif ($path[0] == "/test" && DEBUG) {
    require './routes/test.php';
} else {
    require './routes/post.php';
    require './routes/search.php';
}

$app->run();
