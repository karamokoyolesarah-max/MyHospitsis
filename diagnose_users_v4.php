<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$u = App\Models\User::where('name', 'DAO')->first();
if ($u) {
    echo "USER:DAO|ROLE:{$u->role}|S_ID:".($u->service_id ?? 'NULL')."\n";
    if ($u->service) {
        echo "SERVICE:{$u->service->name}|TYPE:{$u->service->type}\n";
    }
}
$c = App\Models\User::where('role', 'cashier')->where('hospital_id', 2)->first();
if ($c) {
    echo "USER:{$c->name}|ROLE:{$c->role}|S_ID:".($c->service_id ?? 'NULL')."\n";
    if ($c->service) {
        echo "SERVICE:{$c->service->name}|TYPE:{$c->service->type}\n";
    }
}
$types = App\Models\Service::where('hospital_id', 2)->pluck('type')->unique();
echo "TYPES:" . implode(',', $types->toArray()) . "\n";
