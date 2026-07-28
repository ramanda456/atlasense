<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelabuhan;
use Illuminate\Http\JsonResponse;

class PelabuhanApiController extends Controller
{
    /**
     * GET /api/ports
     */
    public function index(): JsonResponse
    {
        $ports = Pelabuhan::with('negara')->orderBy('name')->get();
        return response()->json([
            'success' => true,
            'data' => $ports
        ]);
    }
}
