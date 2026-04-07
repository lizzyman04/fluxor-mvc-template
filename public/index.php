<?php
/**
 * Copyright (C) 2025 lizzyman04
 * Fluxor PHP Framework - Front Controller
 * 
 * Please read the LICENSE file in the root of the project.
 *
 * This is a lightweight Fluxor MVC template. You can download, edit, or alter
 * any part of this code for personal or commercial purposes.
 * This file is the entry point for the application. It loads the 
 * global settings and starts routing the application. Change 
 * to "/.." bellow if you move this file to the project root.
 * 
 * The framework auto-detects base path and URL.
 */

// Load Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Initialize the Fluxor application
$app = new Fluxor\App();

// Configure paths
$app->setConfig([
    'router_path' => __DIR__ . '/../app/router',
    'views_path' => __DIR__ . '/../src/Views',
]);

// Run the application
$app->run();