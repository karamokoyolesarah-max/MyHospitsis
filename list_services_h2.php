<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (App\Models\Service::where('hospital_id', 2)->get() as $s) {
    echo "ID: {$s->id} | Name: {$s->name} | Type: {$s->type}\n";
}
