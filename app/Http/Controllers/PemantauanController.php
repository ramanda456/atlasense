<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DaftarPantauan;
use App\Models\Negara;

class PemantauanController extends Controller
{
    public function index()
    {
        $watchlists = DaftarPantauan::with(['negara.latestRisk', 'negara.latestWeather'])
            ->where('user_id', auth()->id())
            ->get();

        return view('pemantauan.indeks', compact('watchlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $userId = auth()->id();
        $countryId = $request->country_id;

        $exists = DaftarPantauan::where('user_id', $userId)
            ->where('country_id', $countryId)
            ->first();

        if ($exists) {
            $exists->delete();
            return response()->json([
                'success' => true,
                'status' => 'removed',
                'message' => 'Negara berhasil dihapus dari daftar pantauan.'
            ]);
        }

        DaftarPantauan::create([
            'user_id' => $userId,
            'country_id' => $countryId,
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'status' => 'added',
            'message' => 'Negara berhasil ditambahkan ke daftar pantauan.'
        ]);
    }
}
