<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negara;
use App\Models\EkonomiNegara;
use App\Models\KursMataUang;
use App\Models\SkorRisiko;

class VisualisasiController extends Controller
{
    public function index(Request $request)
    {
        @set_time_limit(60);

        $countries = Negara::orderBy('name')->get();
        $selectedCountry = null;
        
        $economicData = collect();
        $riskData = collect();
        $currencyData = collect();

        if ($request->filled('country_id')) {
            $selectedCountry = Negara::find($request->country_id);
        }
        
        $selectedCountry ??= $countries->first();

        if ($selectedCountry) {
            $economicData = EkonomiNegara::where('country_id', $selectedCountry->id)
                ->orderBy('year', 'asc')
                ->get();

            $riskData = SkorRisiko::where('country_id', $selectedCountry->id)
                ->orderBy('calculated_at', 'asc')
                ->limit(30)
                ->get();

            $cleanCurrency = preg_match('/[A-Z]{3}/i', $selectedCountry->currency_code ?? '', $m) ? strtoupper($m[0]) : null;
            if ($cleanCurrency) {
                $currencyData = KursMataUang::where('target_currency', $cleanCurrency)
                    ->orderBy('rate_date', 'asc')
                    ->limit(30)
                    ->get();
            }
        }

        return view('analisis.visualisasi', compact('countries', 'selectedCountry', 'economicData', 'riskData', 'currencyData'));
    }
}
