<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FleetMapController extends Controller
{
    public function index()
    {
        return view('fleet-map.index');
    }

public function boats($days = 365)
{
    $days = in_array((int)$days, [7,30,90,365]) ? (int)$days : 365;

    return Cache::remember("fleet-map-boats-{$days}", 300, function () use ($days) {

        return DB::table('settings')
            ->select('mac', 'boatname', 'lastseen')
            ->where('public', 1)
            ->whereNotNull('mac')
            ->whereNotNull('lastseen')
            ->where('lastseen', '>=', now()->subDays($days))
            ->orderByDesc('lastseen')
            ->get();
    });
}

public function boatData(string $mac, int $days = 365)
{
    $days = in_array($days, [7, 30, 90, 365]) ? $days : 365;

    $cacheKey = "fleet-map-track-{$mac}-{$days}";

    $data = Cache::remember($cacheKey, 600, function () use ($mac, $days) {

$bucketSeconds = match ($days) {
    7 => 120,      // 2 mins
    30 => 300,     // 5 mins
    90 => 900,     // 15 mins
    365 => 1800,   // 30mins
    default => 900,
};

$points = DB::table('boatdata')
    ->leftJoin('settings', 'settings.mac', '=', 'boatdata.mac')
    ->selectRaw("
        AVG(boatdata.latdec) AS latdec,
        AVG(boatdata.londec) AS londec,
        boatdata.mac,
        settings.boatname,
        MIN(boatdata.datetime) AS datetime
    ")
    ->where('settings.public', 1)
    ->where('boatdata.mac', $mac)
    ->where('boatdata.val', 'A')
    ->where('boatdata.utc', '!=', '00:00:00')
    ->where('boatdata.utc', '<', '24:00:00')
    ->whereNotNull('boatdata.latdec')
->whereNotNull('boatdata.londec')
->where('boatdata.latdec', '!=', 0)
->where('boatdata.londec', '!=', 0)
->where('boatdata.datetime', '>=', DB::raw("DATE_SUB(NOW(), INTERVAL {$days} DAY)"))
->groupByRaw("
    boatdata.mac,
    settings.boatname,
    FLOOR(UNIX_TIMESTAMP(boatdata.datetime) / {$bucketSeconds})
")
    ->orderBy('datetime')
    ->limit(30000)
    ->get();

        if ($points->count() < 2) {
            return null;
        }

        $coords = $points->map(function ($point) {
            return [
                (float) $point->londec,
                (float) $point->latdec,
            ];
        })->values()->toArray();

        $last = $points->last();

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'LineString',
                'coordinates' => $coords,
            ],
            'properties' => [
                'mac' => $mac,
                'boatname' => $last->boatname ?? null,
                'last_lat' => (float) $last->latdec,
                'last_lon' => (float) $last->londec,
                'last_seen' => $last->datetime,
            ],
        ];
    });

    return response()->json($data);
}
}