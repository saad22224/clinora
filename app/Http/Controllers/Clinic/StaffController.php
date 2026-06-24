<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    public function index()
    {
        return view('clinic.staff');
    }
}
