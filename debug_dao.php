<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$u = User::where('name', 'like', '%DAO%')->with('service')->first();
if ($u) {
    echo "User: " . $u->name . "\n";
    echo "Role: " . $u->role . "\n";
    echo "Service ID: " . ($u->service_id ?? 'None') . "\n";
    echo "Service Name: " . ($u->service ? $u->service->name : 'None') . "\n";
    echo "Service Type: " . ($u->service ? $u->service->type : 'None') . "\n";
    echo "isMedical: " . ($u->isMedical() ? 'YES' : 'NO') . "\n";
    echo "isTechnical: " . ($u->isTechnical() ? 'YES' : 'NO') . "\n";
} else {
    echo "User DAO not found\n";
}
