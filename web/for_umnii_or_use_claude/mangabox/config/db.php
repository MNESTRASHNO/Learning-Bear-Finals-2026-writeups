<?php

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$name = getenv('DB_NAME') ?: 'mangabox';

return [
    'class' => 'yii\db\Connection',
    'dsn' => "pgsql:host={$host};port={$port};dbname={$name}",
    'username' => getenv('DB_USER') ?: 'mangabox',
    'password' => getenv('DB_PASS') ?: 'mangabox',
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
            'class' => 'yii\db\pgsql\Schema',
            'defaultSchema' => 'public',
        ],
    ],
];
