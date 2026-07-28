<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelabuhan;

class PantauPelabuhanController extends Controller
{
    public function index(Request $request)
    {
        if (Pelabuhan::count() < 100) {
            try {
                $countries = \App\Models\Negara::all();
                foreach ($countries as $n) {
                    if ($n->latitude !== null && $n->longitude !== null) {
                        Pelabuhan::firstOrCreate(
                            ['unlocode' => $n->code . 'PRT'],
                            [
                                'country_id' => $n->id,
                                'name' => 'Pelabuhan Utama ' . ($n->capital ?: $n->name),
                                'city' => $n->capital ?: $n->name,
                                'country_name' => $n->name,
                                'latitude' => $n->latitude,
                                'longitude' => $n->longitude,
                                'port_type' => 'Pelabuhan Laut',
                                'status' => 'Aktif',
                                'data_source' => 'World Port Network'
                            ]
                        );
                    }
                }
            } catch (\Throwable $e) {}
        }

        $query = Pelabuhan::with('negara');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('country_name', 'like', "%{$search}%")
                  ->orWhere('unlocode', 'like', "%{$search}%");
        }

        $ports = $query->orderBy('name', 'asc')->paginate(15);
        
        // Seluruh data pelabuhan untuk pins di peta
        $allPorts = Pelabuhan::all();

        return view('pantau.pelabuhan', compact('ports', 'allPorts'));
    }
}
