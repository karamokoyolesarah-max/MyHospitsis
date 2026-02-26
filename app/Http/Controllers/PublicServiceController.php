<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicServiceController extends Controller
{
    /**
     * View for Maternity service
     */
    public function maternity()
    {
        return view('public.services.maternity');
    }

    /**
     * View for Cardiology service
     */
    public function cardiology()
    {
        return view('public.services.cardiology');
    }

    /**
     * View for Pediatrics service
     */
    public function pediatrics()
    {
        return view('public.services.pediatrics');
    }

    /**
     * View for Nutrition service
     */
    public function nutrition()
    {
        return view('public.services.nutrition');
    }

    /**
     * View for Psychology service
     */
    public function psychology()
    {
        return view('public.services.psychology');
    }

    /**
     * View for Emergencies service
     */
    public function emergencies()
    {
        return view('public.services.emergencies');
    }
}
