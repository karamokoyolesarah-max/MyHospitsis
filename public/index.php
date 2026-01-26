<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

if (isset($_GET['debug_server'])) {
    die('<pre>' . print_r([
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'N/A',
        'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
        'PHP_SELF' => $_SERVER['PHP_SELF'] ?? 'N/A',
        'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
    ], true) . '</pre>');
}

// Fix for subdirectory hosting on Namecheap - modify global $_SERVER before Laravel captures it
if (isset($_SERVER['REQUEST_URI'])) {
    // On récupère le chemin du dossier actuel (ex: /MyHospitsis/public)
    $basePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1));
    $uri = $_SERVER['REQUEST_URI'];
    
    // On retire ce chemin de l'URL pour que Laravel ne voit que la route
    if (strpos($uri, $basePath) === 0) {
        $uri = substr($uri, strlen($basePath));
    }
    
    // On s'assure que ça commence par / et que ce n'est pas vide
    $uri = '/' . ltrim($uri, '/');
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
