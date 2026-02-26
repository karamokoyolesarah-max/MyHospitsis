<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$results = Illuminate\Support\Facades\DB::select('DESCRIBE services');
foreach ($results as $column) {
    echo "Field: {$column->Field} | Type: {$column->Type} | Null: {$column->Null} | Key: {$column->Key} | Default: " . ($column->Default ?? 'NULL') . "\n";
}
