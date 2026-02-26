<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$users = App\Models\User::where('hospital_id', 2)->whereIn('role', ['cashier', 'lab_technician', 'doctor_lab', 'internal_doctor'])->get();
foreach($users as $u) {
    if ($u->service) {
        echo "U:{$u->name}|R:{$u->role}|S:{$u->service->name}|T:{$u->service->type}\n";
    } else {
        echo "U:{$u->name}|R:{$u->role}|S:NONE|T:NONE\n";
    }
}
$services = App\Models\Service::where('hospital_id', 2)->groupBy('type')->select('type', DB::raw('count(*) as count'))->get();
foreach($services as $s) {
    echo "TYPE:{$s->type}|COUNT:{$s->count}\n";
}
