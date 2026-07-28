<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\LogApi;

class KelolaLogController extends Controller
{
    public function index(Request $request)
    {
        $query = LogApi::query();

        if ($request->filled('service')) {
            $query->where('service', $request->service);
        }

        if ($request->filled('status')) {
            $success = $request->status === 'success';
            $query->where('success', $success);
        }

        $logs = $query->latest('requested_at')->paginate(20);
        
        // Ambil nama layanan unik untuk drop-down filter
        $services = LogApi::distinct()->pluck('service')->toArray();

        return view('admin.log-api.indeks', compact('logs', 'services'));
    }

    public function clear()
    {
        LogApi::truncate();
        return redirect()->back()->with('success', 'Seluruh data log pemanggilan API berhasil dibersihkan.');
    }
}
