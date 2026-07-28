<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Negara;
use Illuminate\Http\JsonResponse;

class NegaraApiController extends Controller
{
    /**
     * GET /api/countries
     */
    public function index(): JsonResponse
    {
        $countries = Negara::orderBy('name')->get();
        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }
}
