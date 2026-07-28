<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SkorRisiko;
use Illuminate\Http\JsonResponse;

class RisikoApiController extends Controller
{
    /**
     * GET /api/risk
     */
    public function index(): JsonResponse
    {
        $latestIds = SkorRisiko::selectRaw('MAX(id)')->groupBy('country_id');
        $risks = SkorRisiko::with('negara')->whereIn('id', $latestIds)->get();

        return response()->json([
            'success' => true,
            'data' => $risks
        ]);
    }
}
