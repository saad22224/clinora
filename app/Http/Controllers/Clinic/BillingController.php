<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;

class BillingController extends Controller
{
    public function index()
    {
        return view('clinic.billing');
    }
}
