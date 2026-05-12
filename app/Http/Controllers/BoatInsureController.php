<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BoatInsureController extends Controller
{
    public function show(Request $request, string $mac)
    {
        $range = $request->get('range', '30');

        $rangeOptions = [
            '30' => 'Last 30 Days',
            '60' => 'Last 60 Days',
            '120' => 'Last 120 Days',
            '180' => 'Last 6 Months',
            '365' => 'Last Year',
            'all' => 'All Time',
        ];

        if (!isset($rangeOptions[$range])) {
            $range = '30';
        }

        $sogThreshold = (float) $request->get('sogThreshold', 5);
        $depThreshold = (float) $request->get('depThreshold', 1);
        $awsThreshold = (float) $request->get('awsThreshold', 22);

        $baseQuery = DB::table('boatdata')
            ->where('mac', $mac)
            ->where('val', 'A');

        if ($range !== 'all') {
            $baseQuery->where('datetime', '>=', now()->subDays((int) $range));
        }

        $fields = ['sog', 'aws', 'spd', 'pitch', 'roll', 'yaw', 'xacc', 'yacc', 'zacc'];

        $selects = [];
        foreach ($fields as $field) {
            $selects[] = "MAX(NULLIF(NULLIF($field, -1), -999)) as max_$field";
        }

        $result = (array) (clone $baseQuery)
            ->selectRaw(implode(', ', $selects))
            ->first();

        $bounds = (clone $baseQuery)
            ->selectRaw('
                MIN(latdec) as minLat,
                MAX(latdec) as maxLat,
                MIN(londec) as minLon,
                MAX(londec) as maxLon
            ')
            ->whereNotNull('latdec')
            ->whereNotNull('londec')
            ->first();

        $maxPoints = [];

        foreach ($fields as $field) {
            $max = $result["max_$field"] ?? null;

            if ($max !== null) {
                $point = (clone $baseQuery)
                    ->select('latdec', 'londec', 'datetime')
                    ->where($field, $max)
                    ->whereNotNull('latdec')
                    ->whereNotNull('londec')
                    ->orderByDesc('datetime')
                    ->first();

                if ($point) {
                    $maxPoints[$field] = [
                        'latdec' => (float) $point->latdec,
                        'londec' => (float) $point->londec,
                        'datetime' => $point->datetime,
                        'value' => $max,
                    ];
                }
            }
        }

        $trackRows = (clone $baseQuery)
            ->select('latdec', 'londec', 'datetime', 'sog', 'dep', 'aws')
            ->whereNotNull('latdec')
            ->whereNotNull('londec')
            ->whereNotNull('sog')
            ->orderBy('datetime')
            ->limit(15000)
            ->get();

        $nightTracks = [];
        $sogDepTracks = [];
        $awsTracks = [];

        $currentNightSegment = [];
        $currentSogDepSegment = [];
        $currentAwsSegment = [];

        foreach ($trackRows as $row) {
            $lat = (float) $row->latdec;
            $lon = (float) $row->londec;
            $dt = Carbon::parse($row->datetime);

            $tooltip = "{$dt->format('Y-m-d H:i:s')} | SOG: {$row->sog} | DEP: {$row->dep} | AWS: {$row->aws}";

            $sun = date_sun_info($dt->timestamp, $lat, $lon);
            $isNight = $dt->timestamp < $sun['sunrise'] || $dt->timestamp > $sun['sunset'];

            $isSogDep = $row->sog > $sogThreshold
                && is_numeric($row->dep)
                && $row->dep >= 0
                && $row->dep < $depThreshold;

            $isAws = is_numeric($row->aws) && $row->aws > $awsThreshold;

            $point = ['lat' => $lat, 'lon' => $lon, 'tip' => $tooltip];

            if ($isNight) {
                $currentNightSegment[] = $point;
            } elseif ($currentNightSegment) {
                $nightTracks[] = $currentNightSegment;
                $currentNightSegment = [];
            }

            if ($isSogDep) {
                $currentSogDepSegment[] = $point;
            } elseif ($currentSogDepSegment) {
                $sogDepTracks[] = $currentSogDepSegment;
                $currentSogDepSegment = [];
            }

            if ($isAws) {
                $currentAwsSegment[] = $point;
            } elseif ($currentAwsSegment) {
                $awsTracks[] = $currentAwsSegment;
                $currentAwsSegment = [];
            }
        }

        if ($currentNightSegment) $nightTracks[] = $currentNightSegment;
        if ($currentSogDepSegment) $sogDepTracks[] = $currentSogDepSegment;
        if ($currentAwsSegment) $awsTracks[] = $currentAwsSegment;

        return view('boat_insure', compact(
            'mac',
            'range',
            'rangeOptions',
            'sogThreshold',
            'depThreshold',
            'awsThreshold',
            'result',
            'maxPoints',
            'bounds',
            'nightTracks',
            'sogDepTracks',
            'awsTracks'
        ));
    }
}