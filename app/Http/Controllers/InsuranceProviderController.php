<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use  App\Models\InsuranceProvider;

class InsuranceProviderController extends Controller
{
    public function index()
    {
        $insurance_providers = InsuranceProvider::all();
        return response()->json($insurance_providers,200);
    }
}
