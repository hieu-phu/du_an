<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function provinces()
    {
        return response()->json(Province::where('is_active', true)->orderBy('name')->get(['id', 'name']));
    }

    public function wards($provinceId)
    {
        return response()->json(Ward::where('province_id', $provinceId)->where('is_active', true)->orderBy('name')->get(['id', 'name']));
    }
}
