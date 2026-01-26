<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function checkout()
    {
        return "Checkout placeholder";
    }

    public function initiatePayment()
    {
        return "Payment placeholder";
    }

    public function handleCinetpayWebhook()
    {
        return response()->json(['status' => 'received']);
    }
}
