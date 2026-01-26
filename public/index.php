<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Fix for subdirectory hosting on Namecheap - modify global $_SERVER before Laravel captures it
if (isset($_SERVER['REQUEST_URI'])) {
    $uri = $_SERVER['REQUEST_URI'];
    // Remove the subdirectory and public folder from the URI
    $uri = str_replace(['/MyHospitsis/public', '/MyHospitsis'], '', $uri);
    // Ensure we have a leading slash and it's not empty
    if (empty($uri) || $uri === '') $uri = '/';
    if ($uri[0] !== '/') $uri = '/' . $uri;
    
    $_SERVER['REQUEST_URI'] = $uri;
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
