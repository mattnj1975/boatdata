<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class BoatRawDataController extends Controller
{
    public function show(string $mac)
    {
        $deviceSettings = DB::table('settings')
            ->where('mac', $mac)
            ->select('boatname')
            ->first();

        $columns = DB::getSchemaBuilder()->getColumnListing('boatdata');

        $rows = DB::table('boatdata')
            ->where('mac', $mac)
            ->orderByDesc('date')
            ->orderByDesc('utc')
            ->limit(20)
            ->get();

        return view('boat_raw_data', [
            'mac' => $mac,
            'deviceSettings' => $deviceSettings,
            'columns' => $columns,
            'rows' => $rows,
        ]);
    }
}