<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class FleetStatusService
{
    public function getPublicFleetStats(): array
    {
        $latest = DB::table('boatdata')
            ->select('boatdata.mac', DB::raw('MAX(boatdata.id) as latest_id'))
            ->join('settings', 'boatdata.mac', '=', 'settings.mac')
            ->where('settings.public', 1)
            ->where('boatdata.val', 'A')
            ->whereNotNull('boatdata.lat')
            ->whereNotNull('boatdata.lon')
            ->whereDate('boatdata.date', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 180 DAY)'))
            ->groupBy('boatdata.mac');

        $boats = DB::table('boatdata')
            ->joinSub($latest, 'latest', function ($join) {
                $join->on('boatdata.id', '=', 'latest.latest_id');
            })
            ->join('settings', 'boatdata.mac', '=', 'settings.mac')
            ->selectRaw('
                settings.boatname,
                boatdata.mac,
                CASE 
    WHEN boatdata.ns = "S" 
        THEN -1 * (FLOOR(boatdata.lat / 100) + ((boatdata.lat - (FLOOR(boatdata.lat / 100) * 100)) / 60))
    ELSE 
        FLOOR(boatdata.lat / 100) + ((boatdata.lat - (FLOOR(boatdata.lat / 100) * 100)) / 60)
END as lat,

CASE 
    WHEN boatdata.ew = "W" 
        THEN -1 * (FLOOR(boatdata.lon / 100) + ((boatdata.lon - (FLOOR(boatdata.lon / 100) * 100)) / 60))
    ELSE 
        FLOOR(boatdata.lon / 100) + ((boatdata.lon - (FLOOR(boatdata.lon / 100) * 100)) / 60)
END as lon,
                TIMESTAMP(boatdata.date, boatdata.utc) as last_seen
            ')
            ->orderBy('settings.boatname')
            ->get();

        return [
            'stats' => (object) [
                'boats' => $boats->count(),
            ],
            'boats' => $boats,
            'topBoats' => $boats, // kept for compatibility
        ];
    }
}