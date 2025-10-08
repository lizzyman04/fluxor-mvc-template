<?php

/**
 * ============================================
 * GENERAL PROJECT CONFIGURATION
 * ============================================
 *
 * This file defines the basic system configuration,
 * including path configuration, autoload loading,
 * database initialization and application routing.
 *
 * - BASE_PATH: Returns the absolute path of the project.
 * - BASE_URL: Returns the full URL (http:// or https://).
 * - ERROR REPORTING: Uncomment for dev, comment for prod.
 */

use Core\Router;

// Load environment constants only
require_once __DIR__ . '/env.php';

// ================================
// ERROR REPORTING (Development Mode)
// Uncomment to display errors. Comment in production.
// ================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ================================
// AUTOLOAD, DATABASE & ROUTING
// This section includes the Composer autoloader, initializes
// the database connection, and runs the router with the defined endpoints.
// ================================
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/db/connection.php';

Router::run(include BASE_PATH . '/config/router/index.php');
