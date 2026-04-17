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

    public function districts($provinceId)
    {
        return response()->json(
            \App\Models\District::where('province_id', $provinceId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    public function wards(Request $request, $provinceId)
    {
        return response()->json(
            Ward::where('province_id', $provinceId)
                ->when(
                    filled($request->input('district_id')),
                    fn ($query) => $query->where('district_id', (int) $request->input('district_id'))
                )
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }
}
