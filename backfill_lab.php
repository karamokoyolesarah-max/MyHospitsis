<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
use Illuminate\Contracts\Http\Kernel;

$kernel = $app->make(Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

$count = \App\Models\LabRequest::whereDate('created_at', '2026-02-09')->update(['is_visible_to_patient' => true]);
echo "Updated " . $count . " lab requests from today.";
