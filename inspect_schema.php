<?php
use Illuminate\Support\Facades\Schema;
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cols = Schema::getColumnListing('appointments');
echo "start_at: " . (in_array('patient_confirmation_start_at', $cols) ? "YES" : "NO") . "\n";
echo "end_at: " . (in_array('patient_confirmation_end_at', $cols) ? "YES" : "NO") . "\n";
echo "stars: " . (in_array('rating_stars', $cols) ? "YES" : "NO") . "\n";
echo "comment: " . (in_array('rating_comment', $cols) ? "YES" : "NO") . "\n";
