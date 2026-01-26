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
    // Nettoyage complet
    $uri = $_SERVER['REQUEST_URI'];
    
    // Supprime tout ce qui précède le routage Laravel réel
    $uri = str_replace('/MyHospitsis/public', '', $uri);
    $uri = str_replace('/MyHospitsis', '', $uri);
    
    // Nettoyage final pour s'assurer que ça commence par / et qu'il n'y a pas de double slash
    $uri = '/' . ltrim($uri, '/');
    if ($uri === '') $uri = '/';
    
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
