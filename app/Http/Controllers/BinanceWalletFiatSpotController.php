<?php

namespace App\Http\Controllers;

use Http;

class BinanceWalletFiatSpotController extends BinanceController
{
    public function index()
    {
        $validated = request()->validate([
            'password' => 'required'
        ]);

        if(env('PASSWORD') === request()->password) {
            
            $publicKey = $this->binance_api_key;
            $secretKey = $this->binance_secret_key;

            $timestamp = round(microtime(true) * 1000);

            $parameters['timestamp'] = $timestamp;
            $query = $this->buildQuery($parameters);
            $signature = $this->signature($query, $secretKey);

            $response = Http::withHeaders([
                'X-MBX-APIKEY' => $publicKey
            ])->get("https://api.binance.com/api/v3/account?timestamp=$timestamp&signature=$signature");

            return $response->json();

        } else {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }        
    }
}
