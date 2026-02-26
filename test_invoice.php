<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Invoice;

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "--- DATA CHECK ---\n";

$p = Patient::where('email', 'Gaston@gmail.com')->first();
if ($p) {
    echo "Patient found: ID={$p->id}, Name={$p->full_name}\n";
    
    $i = Invoice::find(21);
    if ($i) {
        echo "Invoice 21 found: ID={$i->id}, Number={$i->invoice_number}, Owner_ID={$i->patient_id}\n";
        if ($i->patient_id == $p->id) {
            echo "MATCH: Invoice belongs to this patient.\n";
        } else {
            echo "MISMATCH: Invoice belongs to patient ID {$i->patient_id}.\n";
        }
    } else {
        echo "Invoice 21 NOT FOUND in database.\n";
        
        echo "Searching for any invoice belonging to patient...\n";
        $lastInv = Invoice::where('patient_id', $p->id)->latest()->first();
        if ($lastInv) {
            echo "Latest invoice for patient: ID={$lastInv->id}, Number={$lastInv->invoice_number}\n";
        } else {
            echo "No invoices found for this patient.\n";
        }
    }
} else {
    echo "Patient 'Gaston@gmail.com' NOT FOUND.\n";
}

echo "--- END CHECK ---\n";
