<?php

return [
    'displayErrorDetails' => \Ramble\Ramble::$DEBUG,
    'addContentLengthHeader' => false,
    // Renderer settings
    'renderer' => [
        'template_path' => __DIR__ . '/../../Templates',
        'cache' => __DIR__ . '/../../Cache',
        'autoescape' => false
    ],
    // Monolog settings
    'logger' => [
        'name' => 'Ramble',
        'path' => __DIR__ . '/../../Logs/ramble.log',
    ],
    'debug' => [
        'revealHttpVariables' => \Ramble\Ramble::$DEBUG
    ],
    'flickr' => require __DIR__ . '/flickr.php',
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379
    ],
    'auth' => [

    ]
];
