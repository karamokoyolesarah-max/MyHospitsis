<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$services = App\Models\Service::where('hospital_id', 2)->get();
echo "Total services: " . $services->count() . "\n";
foreach ($services as $s) {
    if (str_contains(strtolower($s->name), 'caisse') || str_contains(strtolower($s->name), 'labo')) {
        echo "ID: {$s->id} | Name: {$s->name} | Type: {$s->type}\n";
    }
}
