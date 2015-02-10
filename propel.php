<?php

return [
    'propel' => [
        'database' => [
            'connections' => [
                'blog' => [
                    'adapter'    => 'mysql',
                    //debugging
                    'classname'  => 'Propel\Runtime\Connection\DebugPDO',
                    //production
                    //'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
                    'dsn'        => 'mysql:host=localhost;dbname=blog',
                    'user'       => 'blog',
                    'password'   => 'sacredskullBlog',
                    'attributes' => [],
                ],
            ],
        ],
        'runtime' => [
            'defaultConnection' => 'blog',
            'connections' => ['blog'],
            'log' => [
                'defaultLogger' => [
                    'type' => 'stream',
                    //'path' => '/var/www/html/html/logs/propel.log',
                    'path' => 'logs/propel.log',
                    'level' => 100,
                ],
            ],
        ],
        'generator' => [
            'defaultConnection' => 'blog',
            'connections' => ['blog'],
        ],
    ]
];
