<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stores;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function index(): JsonResponse
    {
        $stores = Stores::all();
        return $this->successResponse('Stores retrieved successfully', $stores);
    }
}