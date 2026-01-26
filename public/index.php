<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Fix for subdirectory hosting on Namecheap - modify global $_SERVER before Laravel captures it
if (isset($_SERVER['REQUEST_URI'])) {
    // Nettoyage agressif pour Namecheap
    $originalUri = $_SERVER['REQUEST_URI'];
    
    // On retire le préfixe du sous-dossier et du dossier public s'ils sont présents
    $uri = preg_replace('/^\/MyHospitsis(\/public)?/', '', $originalUri);
    
    // Assurer qu'on a au moins une racine
    if (empty($uri) || $uri === '' || $uri === '//') {
        $uri = '/';
    }
    
    // Nettoyage des doubles slashes au début
    if (strpos($uri, '//') === 0) {
        $uri = substr($uri, 1);
    }

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
