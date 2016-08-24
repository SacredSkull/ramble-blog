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
];
