<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ramble\Ramble;

// Report the earliest startup errors.
// Ramble will keep this on/turn off based on the DEBUG setting.
ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Page generation time.
$GLOBALS['execute_time'] = microtime(true);

require __DIR__ . "/../src/Ramble/Ramble.php";


// Register a basic error handler which will be replaced by Slim during actual app init
set_error_handler(function ($errorNumber, $message, $errfile, $errline) {
    $debugBar = new \DebugBar\StandardDebugBar();
    $renderer = $debugBar->getJavascriptRenderer();
    ?>

    <html>
        <head><?php echo $renderer->renderHead() ?></head>
        <body>
    <?php
    switch ($errorNumber) {
        case E_ERROR :
            $errorLevel = 'Error';
            break;

        case E_WARNING :
            $errorLevel = 'Warning';
            break;

        case E_NOTICE :
            $errorLevel = 'Notice';
            break;

        default :
            $errorLevel = 'Undefined';
    }

    echo '<br/><b>' . $errorLevel . '</b>: ' . $message . ' in <b>'.$errfile . '</b> on line <b>' . $errline . '</b><br/>' . $renderer->render() . '</body></html>';
});


$blog = new Ramble();

session_start();
$blog();

function jsFriendly($string) {
    return htmlspecialchars($string, ENT_QUOTES);
}


$defaultCategory = new \Ramble\Models\CategoryQuery();
if (!$defaultCategory->findPK(1)) {
    $category = new \Ramble\Models\Category();
    $category->setName('Stuff');
    $category->setRoot('/');
    $category->setColour('#66C4F0');
    $category->save();
}

