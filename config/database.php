<?php

use yii\db\Connection;

return [
    'class' => Connection::class,
    'dsn' => 'pgsql:host=localhost;port=5432;dbname=php',
    'username' => 'postgres',
    'password' => '2210',
    'charset' => 'utf8',
    'schema' => 'php_rest'
];