<?php

namespace App\Http\Controllers;

use App\Models\TripDetectionConfig;
use Illuminate\Http\Request;

class TripDetectionConfigController extends Controller
{
    public function edit()
    {
        $config = TripDetectionConfig::whereNull('mac')->first();

        if (!$config) {
            $config = TripDetectionConfig::create([
                'mac' => null,
                'min_sog' => 1.5,
                'min_spd' => 1.5,
                'min_moving_minutes' => 5,
                'min_stopped_minutes' => 10,
                'start_rewind_minutes' => 5,
                'end_extend_minutes' => 5,
                'max_gap_minutes' => 20,
                'use_engine_rpm' => false,
                'min_rpm' => 500,
                'enabled' => true,
            ]);
        }

        return view('trips.settings', compact('config'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'min_sog' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_spd' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_moving_minutes' => ['required', 'integer', 'min:1', 'max:120'],
            'min_stopped_minutes' => ['required', 'integer', 'min:1', 'max:240'],
            'start_rewind_minutes' => ['required', 'integer', 'min:0', 'max:120'],
            'end_extend_minutes' => ['required', 'integer', 'min:0', 'max:120'],
            'max_gap_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'use_engine_rpm' => ['nullable', 'boolean'],
            'min_rpm' => ['required', 'integer', 'min:0', 'max:5000'],
            'enabled' => ['nullable', 'boolean'],
        ]);

        $data['use_engine_rpm'] = $request->boolean('use_engine_rpm');
        $data['enabled'] = $request->boolean('enabled');

        TripDetectionConfig::whereNull('mac')->firstOrFail()->update($data);

        return back()->with('success', 'Trip detection settings updated.');
    }
}