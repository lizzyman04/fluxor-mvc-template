<?php

use Dotenv\Dotenv;
use Cycle\Database;
use Cycle\Database\Config;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$dbConfig = new Config\DatabaseConfig([
    'default' => 'default',
    'databases' => [
        'default' => ['connection' => $_ENV['DB_CONNECTION'] ?? 'mysql']
    ],
    'connections' => [
        'mysql' => new Config\MySQLDriverConfig(
            connection: new Config\MySQL\TcpConnectionConfig(
                database: $_ENV['DB_DATABASE'],
                host: $_ENV['DB_HOST'],
                port: (int) ($_ENV['DB_PORT'] ?? 3306),
                user: $_ENV['DB_USERNAME'],
                password: $_ENV['DB_PASSWORD']
            ),
            queryCache: true
        ),
        'sqlite' => new Config\SQLiteDriverConfig(
            connection: new Config\SQLite\FileConnectionConfig(
                database: $_ENV['DB_DATABASE'] ?? __DIR__ . '/../database.sqlite'
            )
        )
    ]
]);

$database = new Database\DatabaseManager($dbConfig);

return $database;