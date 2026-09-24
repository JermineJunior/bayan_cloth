<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/medical/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloadermedical.
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the requestmedical.
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
