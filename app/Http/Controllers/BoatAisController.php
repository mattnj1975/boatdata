<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BoatAisController extends Controller
{
public function show($mac, $date = null)
{
    if (!$date) {
        $date = now()->toDateString();
    }

    return view('boat-map.boat_ais', [
        'mac' => $mac,
        'defaultDate' => $date,
    ]);
}

    public function data($mac, $date)
    {
        $day = Carbon::parse($date)->toDateString();

        $boatRows = DB::table('boatdata')
            ->select('latdec', 'londec', 'utc', 'date')
            ->where('mac', $mac)
            ->whereDate('date', $day)
            ->whereNotNull('latdec')
            ->whereNotNull('londec')
            ->orderBy('date')
            ->orderBy('utc')
            ->get();

        $aisRows = DB::table('boatdata')
            ->select(
                'ais_mmsi',
                'ais_lat',
                'ais_lon',
                'ais_cog',
                'ais_sog',
                'utc',
                'date',
                'latdec',
                'londec'
            )
            ->where('mac', $mac)
            ->whereDate('date', $day)
            ->whereNotNull('ais_mmsi')
            ->whereNotNull('ais_lat')
            ->whereNotNull('ais_lon')
            ->orderBy('ais_mmsi')
            ->orderBy('date')
            ->orderBy('utc')
            ->get();

        $boatTrack = $boatRows->map(function ($row) {
            return [
                'lat' => (float) $row->latdec,
                'lon' => (float) $row->londec,
                'time' => trim($row->date . ' ' . $row->utc),
            ];
        })->values();

$aisTargets = $aisRows
    ->groupBy('ais_mmsi')
    ->map(function ($rows, $mmsi) {
        $track = [];
        $closest = null;

        foreach ($rows as $row) {
            $lat = (float) $row->ais_lat;
            $lon = (float) $row->ais_lon;
            $boatLat = (float) $row->latdec;
            $boatLon = (float) $row->londec;

            if (
                $lat < -90 || $lat > 90 ||
                $lon < -180 || $lon > 180 ||
                abs($lat) < 0.001 ||
                abs($lon) < 0.001
            ) {
                continue;
            }

            $rangeNm = null;

            if ($boatLat && $boatLon) {
                $rangeNm = $this->distanceMetres($boatLat, $boatLon, $lat, $lon) / 1852;

                if ($closest === null || $rangeNm < $closest['range_nm']) {
$closest = [
    'lat' => $lat,
    'lon' => $lon,
    'time' => trim($row->date . ' ' . $row->utc),
    'range_nm' => round($rangeNm, 2),

    'boat_lat' => $boatLat,
    'boat_lon' => $boatLon,

    'ais_lat' => $lat,
    'ais_lon' => $lon,
    'ais_cog' => $row->ais_cog !== null ? round((float) $row->ais_cog, 1) : null,
    'ais_sog' => $row->ais_sog !== null ? round((float) $row->ais_sog, 1) : null,
];
                }
            }

            $track[] = [
                'lat' => $lat,
                'lon' => $lon,
                'time' => trim($row->date . ' ' . $row->utc),
                'cog' => $row->ais_cog !== null ? (float) $row->ais_cog : null,
                'sog' => $row->ais_sog !== null ? (float) $row->ais_sog : null,
                'range_nm' => $rangeNm !== null ? round($rangeNm, 2) : null,
            ];
        }

        $thinTrack = $this->thinTrack($track);

        return [
            'mmsi' => $mmsi,
            'point_count' => count($track),
            'track' => $thinTrack,
            'closest' => $closest,
            'min_range_nm' => $closest['range_nm'] ?? null,
        ];
    })
    ->filter(fn ($target) => count($target['track']) > 0)
    ->sortBy('min_range_nm')
    ->take(30)
    ->values();

        return response()->json([
            'mac' => $mac,
            'date' => $day,
            'boatTrack' => $boatTrack,
            'aisTargets' => $aisTargets,
        ]);
    }

    private function thinTrack(array $track): array
    {
        $kept = [];
        $last = null;

        foreach ($track as $point) {
            if ($last === null) {
                $kept[] = $point;
                $last = $point;
                continue;
            }

            $distanceMetres = $this->distanceMetres(
                $last['lat'],
                $last['lon'],
                $point['lat'],
                $point['lon']
            );

            if ($distanceMetres >= 300) {
                $kept[] = $point;
                $last = $point;
            }
        }

        return $kept;
    }

    private function distanceMetres($lat1, $lon1, $lat2, $lon2): float
    {
        $earth = 6371000;

        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $dLat = $lat2 - $lat1;
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;

        return $earth * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}