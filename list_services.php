<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hospitalId = 2;
echo "--- SERVICES FOR HOSPITAL $hospitalId ---\n";
foreach(App\Models\Service::where('hospital_id', $hospitalId)->get() as $s) {
    echo "ID: {$s->id} | Name: {$s->name} | Type: {$s->type}\n";
}
