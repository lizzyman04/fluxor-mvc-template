<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/connection.php';

use Cycle\Database\DatabaseManager;

function executeSchema(DatabaseManager $database): void
{
    $schemaFile = __DIR__ . '/../config/schema.sql';

    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: config/schema.sql");
    }

    $sql = file_get_contents($schemaFile);

    $sql = preg_replace('/--.*$/m', '', $sql);
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    $successCount = 0;
    $errorCount = 0;

    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^\s*$/', $statement)) {
            try {
                $database->database()->execute($statement);
                echo "✅ Executed: " . substr(trim($statement), 0, 60) . "\n";
                $successCount++;
            } catch (Exception $e) {
                echo "❌ Failed: " . substr(trim($statement), 0, 60) . "\n";
                echo "   Error: " . $e->getMessage() . "\n";
                $errorCount++;
            }
        }
    }

    echo "\n📊 Schema execution completed:\n";
    echo "   ✅ Successful: {$successCount}\n";
    echo "   ❌ Failed: {$errorCount}\n";
}

function checkExistingTables(DatabaseManager $database): array
{
    try {
        $tables = $database->database()->getTables();
        $tableNames = [];

        foreach ($tables as $table) {
            $tableNames[] = $table->getName();
        }

        return $tableNames;
    } catch (Exception $e) {
        return [];
    }
}

echo "🚀 Starting database schema setup...\n\n";

try {
    $database = require 'connection.php';

    echo "📡 Checking database connection...\n";
    $database->database()->execute('SELECT 1');
    echo "✅ Database connection successful\n\n";

    echo "🔍 Checking existing tables...\n";
    $existingTables = checkExistingTables($database);

    if (!empty($existingTables)) {
        echo "📋 Existing tables found:\n";
        foreach ($existingTables as $table) {
            echo "   - {$table}\n";
        }
        echo "\n⚠️  Note: Some statements may fail if tables already exist\n\n";
    } else {
        echo "✅ No existing tables found\n\n";
    }

    echo "📝 Executing schema from config/schema.sql...\n\n";
    executeSchema($database);

    echo "\n🎉 Database schema setup completed!\n";
    echo "💡 You can now run: composer dev\n";

} catch (Exception $e) {
    echo "❌ Schema setup failed: " . $e->getMessage() . "\n";
    exit(1);
}