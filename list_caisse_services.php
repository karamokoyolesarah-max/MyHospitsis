<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;

$s = Service::where('hospital_id', 2)->get();
foreach($s as $item) {
    echo "ID: {$item->id} | Name: {$item->name} | Type: {$item->type} | CaisseType: {$item->caisse_type} | ParentID: " . ($item->parent_id ?? 'None') . "\n";
}
