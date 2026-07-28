<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KursMataUang;
use Illuminate\Http\JsonResponse;

class KursApiController extends Controller
{
    /**
     * GET /api/currency
     */
    public function index(): JsonResponse
    {
        $latestIds = KursMataUang::selectRaw('MAX(id)')->groupBy(['base_currency', 'target_currency']);
        $rates = KursMataUang::whereIn('id', $latestIds)->get();

        return response()->json([
            'success' => true,
            'data' => $rates
        ]);
    }
}
