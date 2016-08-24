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

