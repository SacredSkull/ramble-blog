<?php

return [
    'propel' => [
        'database' => [
            'connections' => [
                'blog' => [
                    'adapter'    => 'mysql',
                    'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
                    'dsn'        => 'mysql:host=localhost;dbname=blog',
                    'user'       => 'blog',
                    'password'   => 'sacredskullBlog',
                    'attributes' => []
                ]
            ]
        ],
        'runtime' => [
            'defaultConnection' => 'blog',
            'connections' => ['blog'],
            'log' => [
                'defaultLogger' => [
                    'type' => 'stream',
                    'path' => 'E:\WPNXM\www\blog\logs\propel.log',
                    'level' => 300
                ],
                'bookstore' => [
                    'type' => 'stream',
                    'path' => 'E:\WPNXM\www\blog\logs\propel_blog.log',
                ]
            ]
        ],
        'generator' => [
            'defaultConnection' => 'blog',
            'connections' => ['blog']
        ]
    ]
];

?>
