<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negara;
use App\Models\PerbandinganNegara;

class PerbandinganController extends Controller
{
    public function index(Request $request)
    {
        $countries = Negara::orderBy('name')->get();
        $negaraA = null;
        $negaraB = null;
        $comparison = null;

        if ($request->filled('country_a') && $request->filled('country_b')) {
            $negaraA = Negara::with(['latestEconomic', 'latestWeather', 'latestRisk'])->findOrFail($request->country_a);
            $negaraB = Negara::with(['latestEconomic', 'latestWeather', 'latestRisk'])->findOrFail($request->country_b);

            // Tentukan hasil perbandingan
            $comparison = [
                'gdp_a' => $negaraA->latestEconomic?->gdp,
                'gdp_b' => $negaraB->latestEconomic?->gdp,
                'inflation_a' => $negaraA->latestEconomic?->inflation,
                'inflation_b' => $negaraB->latestEconomic?->inflation,
                'population_a' => $negaraA->latestEconomic?->population ?? $negaraA->population,
                'population_b' => $negaraB->latestEconomic?->population ?? $negaraB->population,
                'risk_a' => $negaraA->latestRisk?->total_score ?? 0,
                'risk_b' => $negaraB->latestRisk?->total_score ?? 0,
                'temp_a' => $negaraA->latestWeather?->temperature,
                'temp_b' => $negaraB->latestWeather?->temperature,
                'wind_a' => $negaraA->latestWeather?->wind_speed,
                'wind_b' => $negaraB->latestWeather?->wind_speed,
            ];

            // Simpan riwayat perbandingan ke DB
            PerbandinganNegara::create([
                'user_id' => auth()->id(),
                'country_a_id' => $negaraA->id,
                'country_b_id' => $negaraB->id,
                'result' => $comparison,
            ]);
        }

        return view('analisis.perbandingan', compact('countries', 'negaraA', 'negaraB', 'comparison'));
    }
}
