<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;
use App\Models\User;
use App\Models\Appointment;
use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\Auth;

$hospitalId = 2;
$controller = new CashierController();

// 1. Test Lab Cashier
$labCashierService = Service::where('hospital_id', $hospitalId)->where('name', 'Caisse Laboratoire')->first();
$labUser = User::where('service_id', $labCashierService->id)->first();

if ($labUser) {
    Auth::login($labUser);
    $query = Appointment::where('hospital_id', $hospitalId);
    
    // Use reflection to call private method applyCashierScope
    $reflection = new \ReflectionClass(CashierController::class);
    $method = $reflection->getMethod('applyCashierScope');
    $method->setAccessible(true);
    $method->invokeArgs($controller, [&$query, $labUser]);
    
    echo "--- LAB CASHIER SCOPE ---\n";
    echo "SQL: " . $query->toSql() . "\n";
    foreach($query->get() as $apt) {
        echo "Apt ID: {$apt->id} | Service: {$apt->service->name} (Service ID: {$apt->service_id})\n";
    }
}

// 2. Test Urgences Cashier
$urgCashierService = Service::where('hospital_id', $hospitalId)->where('name', 'Caisse Urgences')->first();
$urgUser = User::where('service_id', $urgCashierService->id)->first();

if ($urgUser) {
    Auth::login($urgUser);
    $query = Appointment::where('hospital_id', $hospitalId);
    
    $method->invokeArgs($controller, [&$query, $urgUser]);
    
    echo "\n--- URGENCE CASHIER SCOPE ---\n";
    echo "SQL: " . $query->toSql() . "\n";
    foreach($query->get() as $apt) {
        echo "Apt ID: {$apt->id} | Service: {$apt->service->name} (Service ID: {$apt->service_id})\n";
    }
}

// 3. Test Global Cashier
$accueilCaisse = Service::where('hospital_id', $hospitalId)->where('name', 'like', 'Caisse Accueil%')->first();
$accueilUser = User::where('service_id', $accueilCaisse->id)->first();

if ($accueilUser) {
    Auth::login($accueilUser);
    $query = Appointment::where('hospital_id', $hospitalId);
    
    $method->invokeArgs($controller, [&$query, $accueilUser]);
    
    echo "\n--- ACCUEIL CASHIER SCOPE ---\n";
    echo "SQL: " . $query->toSql() . "\n";
    foreach($query->get() as $apt) {
        echo "Apt ID: {$apt->id} | Service: {$apt->service->name} (Service ID: {$apt->service_id})\n";
    }
}
