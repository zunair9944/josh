<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class StoreController extends Controller
{
    public function verify_shopify_keys(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'access_token' => 'required|string',
            'store_url' => 'required|string'
        ]);

        $buildUrl = 'https://' . $request->input('api_key') . ':' . $request->input('access_token') . '@'  . $request->input('store_url') . '/admin/api/2023-04' . '/products.json';
        $response = Http::get($buildUrl);
        if($response->successfull()){
            return response()->json([
                'message' => 'Connected Successfully',
                'data' => $response->body()
            ]);
        }
    }
}
