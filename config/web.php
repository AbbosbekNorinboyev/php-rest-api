<?php

use yii\db\Connection;

return [
    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => 'pgsql:host=localhost;port=5432;dbname=php',
            'username' => 'postgres',
            'password' => '2210',
            'charset' => 'utf8',
            'schema' => 'php_rest',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,

            'rules' => [
                // Health endpoints
                'GET health' => 'health/index',
                'GET health/db' => 'health/db',
                'GET health/info' => 'health/info',

            ],
        ],
    ],
];