<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negara;
use App\Models\SkorRisiko;
use App\Services\PenilaiAncaman;

class AnalisisRisikoController extends Controller
{
    public function __construct(
        private PenilaiAncaman $penilaiAncaman
    ) {}

    public function index(Request $request)
    {
        $query = Negara::with('latestRisk');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        $countries = $query->orderBy('name')->paginate(12);

        return view('analisis.risiko', compact('countries'));
    }

    public function hitung($code)
    {
        $negara = Negara::where('code', strtoupper($code))->firstOrFail();

        try {
            $skor = $this->penilaiAncaman->hitung($negara);
            return redirect()->route('countries.show', $negara->code)
                ->with('success', "Analisis risiko untuk {$negara->name} berhasil diperbarui! Skor: {$skor->total_score} ({$skor->risk_level}).");
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', "Gagal menghitung risiko {$negara->name}: " . $e->getMessage());
        }
    }
}
