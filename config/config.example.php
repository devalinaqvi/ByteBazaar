<?php
return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: 3306,
        'dbname' => getenv('DB_NAME') ?: 'byte_bazaar',
        'user' => getenv('DB_USER') ?: '',
        'pass' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
    ],
];
