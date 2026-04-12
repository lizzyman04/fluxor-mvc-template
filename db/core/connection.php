<?php

/**
 * Database Connection
 */

require_once __DIR__ . '/bootstrap.php';

use Cycle\Database;
use Cycle\Database\Config;

$dbConfig = new Config\DatabaseConfig([
    'default' => 'default',
    'databases' => [
        'default' => ['connection' => env('DB_CONNECTION', 'mysql')]
    ],
    'connections' => [
        // MySQL
        'mysql' => new Config\MySQLDriverConfig(
            connection: new Config\MySQL\TcpConnectionConfig(
                database: env('DB_DATABASE', 'fluxor_db'),
                host: env('DB_HOST', 'localhost'),
                port: (int) env('DB_PORT', 3306),
                user: env('DB_USERNAME', 'root'),
                password: env('DB_PASSWORD', '')
            ),
            queryCache: true
        ),
        
        // MySQL with Socket (Unix)
        'mysql_socket' => new Config\MySQLDriverConfig(
            connection: new Config\MySQL\SocketConnectionConfig(
                database: env('DB_DATABASE', 'fluxor_db'),
                socket: env('DB_SOCKET', '/var/run/mysqld/mysqld.sock'),
                user: env('DB_USERNAME', 'root'),
                password: env('DB_PASSWORD', '')
            ),
            queryCache: true
        ),
        
        // PostgreSQL
        'pgsql' => new Config\PostgresDriverConfig(
            connection: new Config\Postgres\TcpConnectionConfig(
                database: env('DB_DATABASE', 'fluxor_db'),
                host: env('DB_HOST', 'localhost'),
                port: (int) env('DB_PORT', 5432),
                user: env('DB_USERNAME', 'root'),
                password: env('DB_PASSWORD', '')
            ),
            schema: 'public',
            queryCache: true
        ),
        
        // SQLite
        'sqlite' => new Config\SQLiteDriverConfig(
            connection: new Config\SQLite\FileConnectionConfig(
                database: env('DB_DATABASE', base_path('db/database.sqlite'))
            ),
            queryCache: true
        ),
        
        // SQLite
        'sqlite_memory' => new Config\SQLiteDriverConfig(
            connection: new Config\SQLite\MemoryConnectionConfig(),
            queryCache: true
        ),
        
        // SQL Server
        'sqlsrv' => new Config\SQLServerDriverConfig(
            connection: new Config\SQLServer\TcpConnectionConfig(
                database: env('DB_DATABASE', 'fluxor_db'),
                host: env('DB_HOST', 'localhost'),
                port: (int) env('DB_PORT', 1433),
                user: env('DB_USERNAME', 'sa'),
                password: env('DB_PASSWORD', '')
            ),
            queryCache: true
        ),
    ]
]);

return new Database\DatabaseManager($dbConfig);