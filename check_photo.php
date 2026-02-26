<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

use App\Models\MedecinExterne;

$medecin = MedecinExterne::where('email', 'Sarah@gmail.com')->first();
if ($medecin) {
    echo "Email: " . $medecin->email . "\n";
    echo "Name: " . $medecin->prenom . " " . $medecin->nom . "\n";
    echo "Photo path: " . ($medecin->profile_photo_path ?? 'NULL') . "\n";
} else {
    echo "Medecin not found\n";
}
