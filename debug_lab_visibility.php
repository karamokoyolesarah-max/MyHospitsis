<?php

use App\Models\User;
use App\Models\LabRequest;
use App\Models\Service;
use App\Models\Prestation;

function logDebug($msg) {
    file_put_contents('debug_lab_visibility.log', $msg . "\n", FILE_APPEND);
}

function debugLabVisibility() {
    try {
        logDebug("--- Debugging Lab Visibility (Retry) ---");
        
        $count = User::count();
        logDebug("Total Users: " . $count);

        // 1. List all users with service info to find our technician manually if needed
        $users = User::with('service')->get();
        foreach($users as $u) {
            if (stripos($u->name, 'dao') !== false || stripos($u->role, 'lab') !== false || stripos($u->role, 'tech') !== false) {
                 logDebug("User MATCH: {$u->name} | Role: {$u->role} | ServiceID: {$u->service_id} | ServiceName: " . ($u->service->name ?? 'N/A'));
            }
        }

        logDebug("\n--- Recent Lab Requests ---");
        $requests = LabRequest::latest()->take(5)->get();
        if ($requests->isEmpty()) {
            logDebug("No Lab Requests found.");
        } else {
            foreach ($requests as $req) {
                 $reqService = Service::find($req->service_id);
                 logDebug("Req ID: {$req->id} | Patient: {$req->patient_name} | Test: {$req->test_name} | ServiceID: {$req->service_id} (" . ($reqService->name ?? 'Unk') . ")");
            }
        }
    } catch (\Exception $e) {
        logDebug("EXCEPTION: " . $e->getMessage());
    }
}

debugLabVisibility();
