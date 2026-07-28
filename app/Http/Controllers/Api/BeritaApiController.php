<?php

namespace App\Http\Controllers; // wait, let's make sure namespace is App\Http\Controllers\Api!

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CacheBerita;
use Illuminate\Http\JsonResponse;

class BeritaApiController extends Controller
{
    /**
     * GET /api/news
     */
    public function index(): JsonResponse
    {
        $news = CacheBerita::with(['negara', 'analysis'])->latest('published_at')->get();
        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }
}
