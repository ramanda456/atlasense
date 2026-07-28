<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Pelabuhan;
use App\Models\KataPositif;
use App\Models\KataNegatif;
use App\Models\Artikel;
use App\Models\LogApi;
use App\Models\PengaturanSistem;
use Illuminate\Http\Request;

class DasborAdminController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'ports' => Pelabuhan::count(),
            'positive_words' => KataPositif::count(),
            'negative_words' => KataNegatif::count(),
            'articles' => Artikel::count(),
            'api_logs' => LogApi::count(),
            'api_success' => LogApi::count() > 0 ? round((LogApi::where('success', true)->count() / LogApi::count()) * 100, 2) : 0,
        ];

        // Ambil bobot risiko saat ini
        $settings = [
            'risk_weather_weight' => PengaturanSistem::where('key', 'risk_weather_weight')->value('value') ?: 25,
            'risk_inflation_weight' => PengaturanSistem::where('key', 'risk_inflation_weight')->value('value') ?: 25,
            'risk_news_weight' => PengaturanSistem::where('key', 'risk_news_weight')->value('value') ?: 30,
            'risk_currency_weight' => PengaturanSistem::where('key', 'risk_currency_weight')->value('value') ?: 20,
        ];

        return view('admin.dasbor', compact('stats', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'risk_weather_weight' => 'required|numeric|min:0|max:100',
            'risk_inflation_weight' => 'required|numeric|min:0|max:100',
            'risk_news_weight' => 'required|numeric|min:0|max:100',
            'risk_currency_weight' => 'required|numeric|min:0|max:100',
        ]);

        $total = $request->risk_weather_weight + $request->risk_inflation_weight + $request->risk_news_weight + $request->risk_currency_weight;
        
        if ($total != 100) {
            return redirect()->back()->with('error', "Gagal! Total persentase bobot harus sama dengan 100%. Total input Anda: {$total}%.");
        }

        foreach (['risk_weather_weight', 'risk_inflation_weight', 'risk_news_weight', 'risk_currency_weight'] as $key) {
            PengaturanSistem::updateOrCreate(
                ['key' => $key],
                ['value' => $request->$key, 'type' => 'number', 'description' => 'Bobot perhitungan risiko']
            );
        }

        return redirect()->back()->with('success', 'Bobot perhitungan risiko berhasil diperbarui.');
    }
}
