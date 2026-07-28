<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Pelabuhan;
use App\Models\Negara;
use App\Services\PengelolaPelabuhan;

class KelolaPelabuhanController extends Controller
{
    public function __construct(
        private PengelolaPelabuhan $pengelolaPelabuhan
    ) {}

    public function index()
    {
        $ports = Pelabuhan::with('negara')->orderBy('name', 'asc')->paginate(15);
        $countries = Negara::orderBy('name')->get();
        return view('admin.pelabuhan.indeks', compact('ports', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'unlocode' => 'nullable|string|max:10|unique:ports,unlocode',
            'wpi_number' => 'nullable|string|max:50',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'port_type' => 'required|string',
        ]);

        $country = Negara::findOrFail($request->country_id);

        Pelabuhan::create([
            'country_id' => $country->id,
            'name' => $request->name,
            'city' => $request->city,
            'country_name' => $country->name,
            'unlocode' => $request->unlocode ? strtoupper($request->unlocode) : null,
            'wpi_number' => $request->wpi_number,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'port_type' => $request->port_type,
            'status' => 'Aktif',
            'data_source' => 'Manual',
        ]);

        return redirect()->back()->with('success', 'Pelabuhan baru berhasil ditambahkan.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('csv_file');
            $batch = $this->pengelolaPelabuhan->impor($file, auth()->id());
            
            return redirect()->back()->with('success', "Impor berhasil! {$batch->imported_rows} data masuk, {$batch->skipped_rows} data dilewati.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', "Gagal mengimpor CSV: " . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        Pelabuhan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Pelabuhan berhasil dihapus.');
    }
}
