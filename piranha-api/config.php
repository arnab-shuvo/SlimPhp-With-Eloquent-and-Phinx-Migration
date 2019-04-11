<?php
require_once './vendor/autoload.php';
require 'composer.json';
return [
    'paths' => [
        'migrations' => 'migrations'
    ],
    'migration_base_class' => 'App\Migration\Migration',
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'dev',
        'dev' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'piranha',
            'username' => 'root',
            'password' => 'Wsit_97480',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ]
];