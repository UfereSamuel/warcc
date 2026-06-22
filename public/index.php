<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Load APP_URL early so subdirectory installs can normalize REQUEST_URI before routing.
if (file_exists($envFile = __DIR__.'/../.env') && class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
}

require __DIR__.'/../bootstrap/subdirectory.php';
warcc_strip_subdirectory_prefix_from_request();

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
