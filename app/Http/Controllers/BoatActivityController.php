<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoatActivityController extends Controller
{
    public function show(Request $request)
    {
        $mac = $request->query('mac');
        if (!$mac) {
            abort(400, "Missing ?mac parameter.");
        }

        $range = $request->query('range', '30');
        $rangeOptions = [
            '30' => 'Last 30 Days',
            '60' => 'Last 60 Days',
            '120' => 'Last 120 Days',
            '180' => 'Last 6 Months',
            '365' => 'Last Year',
            'all' => 'All Time'
        ];

        $dateFilter = $range !== 'all'
            ? "AND datetime >= NOW() - INTERVAL ? DAY"
            : "";

        // Thresholds
        $sogThreshold = (float) $request->query('sogThreshold', 5);
        $depThreshold = (float) $request->query('depThreshold', 1);
        $awsThreshold = (float) $request->query('awsThreshold', 20);

        // Helper to get max values
        $fields = ['sog', 'aws', 'spd', 'pitch', 'roll', 'yaw', 'xacc', 'yacc', 'zacc'];
        $result = [];
        $maxPoints = [];

        foreach ($fields as $field) {
            $maxSql = "SELECT MAX($field) as maxval FROM boatdata WHERE mac = ? AND val = 'A' AND $field NOT IN (-1, -999) $dateFilter";
            $maxVal = $range !== 'all' 
                ? DB::selectOne($maxSql, [$mac, $range])->maxval
                : DB::selectOne($maxSql, [$mac])->maxval;

            $result["max_$field"] = $maxVal;

            if ($maxVal !== null) {
                $posSql = "SELECT latdec, londec, datetime FROM boatdata WHERE mac = ? AND val = 'A' AND $field = ? AND $field NOT IN (-1, -999) $dateFilter LIMIT 1";
                $pos = $range !== 'all'
                    ? DB::selectOne($posSql, [$mac, $maxVal, $range])
                    : DB::selectOne($posSql, [$mac, $maxVal]);
                if ($pos) {
                    $maxPoints[$field] = [
                        'latdec' => $pos->latdec,
                        'londec' => $pos->londec,
                        'datetime' => $pos->datetime,
                        'value' => $maxVal,
                    ];
                }
            }
        }

        // Bounding box
        $bboxSql = "SELECT MIN(latdec) as minLat, MAX(latdec) as maxLat, MIN(londec) as minLon, MAX(londec) as maxLon 
                    FROM boatdata WHERE mac = ? AND val = 'A' $dateFilter";
        $bounds = $range !== 'all' 
            ? DB::selectOne($bboxSql, [$mac, $range])
            : DB::selectOne($bboxSql, [$mac]);

        // Track points
        $trackSql = "SELECT latdec, londec, datetime, sog, dep, aws FROM boatdata WHERE mac = ? AND val = 'A' AND sog IS NOT NULL $dateFilter ORDER BY datetime ASC";
        $rows = $range !== 'all' 
            ? DB::select($trackSql, [$mac, $range])
            : DB::select($trackSql, [$mac]);

        // Process track segments
        $nightTracks = [];
        $sogDepTracks = [];
        $awsTracks = [];

        $currentNightSegment = [];
        $currentSogDepSegment = [];
        $currentAwsSegment = [];

        foreach ($rows as $row) {
            $lat = (float) $row->latdec;
            $lon = (float) $row->londec;
            $dt = new \DateTime($row->datetime);
            $tooltip = $dt->format('Y-m-d H:i:s') . " | SOG: {$row->sog} | DEP: {$row->dep} | AWS: {$row->aws}";

            // Calculate sunrise/sunset times based on lat/lon & date
            $sun = date_sun_info($dt->getTimestamp(), $lat, $lon);
            $isNight = ($dt < new \DateTime("@{$sun['sunrise']}") || $dt > new \DateTime("@{$sun['sunset']}"));

            $isSogDep = ($row->sog > $sogThreshold
                && is_numeric($row->dep) && $row->dep !== null
                && $row->dep >= 0 && $row->dep < $depThreshold);

            $isAws = ($row->aws > $awsThreshold);

            if ($isNight) {
                $currentNightSegment[] = ['lat' => $lat, 'lon' => $lon, 'tip' => $tooltip];
            } elseif (!empty($currentNightSegment)) {
                $nightTracks[] = $currentNightSegment;
                $currentNightSegment = [];
            }

            if ($isSogDep) {
                $currentSogDepSegment[] = ['lat' => $lat, 'lon' => $lon, 'tip' => $tooltip];
            } elseif (!empty($currentSogDepSegment)) {
                $sogDepTracks[] = $currentSogDepSegment;
                $currentSogDepSegment = [];
            }

            if ($isAws) {
                $currentAwsSegment[] = ['lat' => $lat, 'lon' => $lon, 'tip' => $tooltip];
            } elseif (!empty($currentAwsSegment)) {
                $awsTracks[] = $currentAwsSegment;
                $currentAwsSegment = [];
            }
        }

        if (!empty($currentNightSegment)) $nightTracks[] = $currentNightSegment;
        if (!empty($currentSogDepSegment)) $sogDepTracks[] = $currentSogDepSegment;
        if (!empty($currentAwsSegment)) $awsTracks[] = $currentAwsSegment;

        return view('boat-activity', compact(
            'mac', 'range', 'rangeOptions', 'result', 'maxPoints', 'bounds', 
            'nightTracks', 'sogDepTracks', 'awsTracks',
            'sogThreshold', 'depThreshold', 'awsThreshold'
        ));
    }
}
