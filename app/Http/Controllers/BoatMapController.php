<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class BoatMapController extends Controller
{
    public function show(string $mac, int $days = 30)
    {
       $days = in_array($days, [0, 7, 30, 90, 365]) ? $days : 30;

        $bucketSeconds = match ($days) {
    0 => 60,       // all time: 1 min
    7 => 30,       // 7 days: 30 sec
    30 => 60,      // 30 days: 1 min
    90 => 180,     // 90 days: 3 min
    365 => 300,    // 1 year: 5 min
    default => 60,
};

$deviceSettings = DB::table('settings')
    ->where('mac', $mac)
    ->select('boatname', 'mac')
    ->first();

$boatName = $deviceSettings->boatname ?? null;

$query = DB::table('boatdata')
    ->selectRaw("
        AVG(latdec) AS latdec,
        AVG(londec) AS londec,
        DATE(datetime) AS day,
        mac,
        MIN(datetime) AS datetime
    ")
    ->where('mac', $mac)
    ->where('val', 'A')
    ->where('utc', '!=', '00:00:00')
    ->where('utc', '<', '24:00:00')
    ->whereNotNull('latdec')
    ->whereNotNull('londec')
    ->where('latdec', '!=', 0)
    ->where('londec', '!=', 0);

if ($days > 0) {
    $query->where(
        'datetime',
        '>=',
        DB::raw("DATE_SUB(NOW(), INTERVAL {$days} DAY)")
    );
}

$points = $query
    ->groupByRaw("
        mac,
        DATE(datetime),
        FLOOR(UNIX_TIMESTAMP(datetime) / {$bucketSeconds})
    ")
    ->orderBy('datetime')
    ->limit(200000)
    ->get();

        $tracks = [];

        foreach ($points as $point) {

            $key = $point->mac . '_' . $point->day;

            if (!isset($tracks[$key])) {

                $tracks[$key] = [
                    'mac' => $point->mac,
                    'day' => $point->day,
                    'coords' => [],
                ];
            }

            $tracks[$key]['coords'][] = [
                (float) $point->londec,
                (float) $point->latdec,
            ];
        }

        $features = [];
        $i = 0;

        foreach ($tracks as $track) {

            if (count($track['coords']) < 2) {
                continue;
            }

            $hue = ($i * 47) % 360;

            $color = "hsl({$hue}, 85%, 50%)";

            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => $track['coords'],
                ],
                'properties' => [
                    'date' => $track['day'],
                    'mac' => $track['mac'],
                    'color' => $color,
                ],
            ];

            $i++;
        }

return view('boat-map.show', [
    'mac' => $mac,
    'days' => $days,
    'boatName' => $boatName,
    'deviceSettings' => $deviceSettings,
    'geojson' => [
        'type' => 'FeatureCollection',
        'features' => $features,
    ],
]);
    }
}