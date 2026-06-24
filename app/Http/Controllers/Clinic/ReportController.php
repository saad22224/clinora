<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return view('clinic.reports');
    }
}
