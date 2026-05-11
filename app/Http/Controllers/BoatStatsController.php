<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BoatStatsController extends Controller
{
    public function index(Request $request, ?string $mac = null)
    {
        $year = (int) $request->get('year', now()->year);

        $boats = DB::table('boatdata')
            ->select('mac')
            ->distinct()
            ->orderBy('mac')
            ->pluck('mac');

        $mac = $mac ?: $boats->first();

        if (!$mac) {
            return view('boat_stats', [
                'boats' => collect(),
                'mac' => null,
                'year' => $year,
                'summary' => null,
                'daily' => collect(),
                'monthly' => collect(),
                'latest' => null,
                'track' => collect(),
                'totalMiles' => 0,
                'status' => 'Offline',
                'lastSeenAge' => 'No data',
            ]);
        }

        $summary = DB::table('boatdata')
            ->where('mac', $mac)
            ->whereYear('date', $year)
            ->selectRaw('
                MAX(NULLIF(sog, 0)) as top_speed,
                AVG(NULLIF(sog, 0)) as avg_speed,
                MAX(NULLIF(aws, 0)) as top_wind,
                AVG(NULLIF(aws, 0)) as avg_wind,
                COUNT(DISTINCT date) as days_used,
                COUNT(*) as records,
                MIN(CONCAT(date, " ", utc)) as first_seen,
                MAX(CONCAT(date, " ", utc)) as last_seen
            ')
            ->first();

        $daily = DB::table('boatdata')
            ->where('mac', $mac)
            ->whereYear('date', $year)
            ->groupBy('date')
            ->orderBy('date')
            ->selectRaw('
                date,
                MAX(NULLIF(sog, 0)) as max_sog,
                AVG(NULLIF(sog, 0)) as avg_sog,
                MAX(NULLIF(aws, 0)) as max_aws,
                AVG(NULLIF(aws, 0)) as avg_aws,
                COUNT(*) as records
            ')
            ->get();

$monthly = DB::table('boatdata')
    ->where('mac', $mac)
    ->whereYear('date', $year)
    ->groupByRaw('MONTH(date)')
    ->orderByRaw('MONTH(date)')
    ->selectRaw('
        MONTH(date) as month_num,
        DATE_FORMAT(MIN(date), "%b") as month_name,
        COUNT(DISTINCT date) as days_used,
        MAX(NULLIF(sog, 0)) as max_sog,
        AVG(NULLIF(sog, 0)) as avg_sog,
        MAX(NULLIF(aws, 0)) as max_aws,
        COUNT(*) as records
    ')
    ->get();

        $latest = DB::table('boatdata')
            ->where('mac', $mac)
            ->whereNotNull('latdec')
            ->whereNotNull('londec')
            ->where('latdec', '!=', 0)
            ->whereRaw('ABS(londec) > 0')
            ->orderByDesc('date')
            ->orderByDesc('utc')
            ->select('latdec', 'londec', 'sog', 'cog', 'aws', 'date', 'utc')
            ->first();

        $track = DB::table('boatdata')
            ->where('mac', $mac)
            ->whereNotNull('latdec')
            ->whereNotNull('londec')
            ->where('latdec', '!=', 0)
            ->whereRaw('ABS(londec) > 0')
            ->whereRaw("CONCAT(date, ' ', utc) >= DATE_SUB(NOW(), INTERVAL 7 DAY)")
            ->orderBy('date')
            ->orderBy('utc')
            ->select('latdec', 'londec', 'sog', 'cog', 'date', 'utc')
            ->limit(1500)
            ->get();

        $totalMiles = DB::table(DB::raw("
            (
                SELECT 
                    date,
                    utc,
                    sog,
                    TIMESTAMPDIFF(
                        SECOND,
                        LAG(CONCAT(date, ' ', utc)) OVER (ORDER BY date, utc),
                        CONCAT(date, ' ', utc)
                    ) / 3600 AS hours_gap
                FROM boatdata
                WHERE mac = " . DB::getPdo()->quote($mac) . "
                AND YEAR(date) = " . (int)$year . "
                AND sog IS NOT NULL
                AND sog BETWEEN 0.2 AND 50
            ) x
        "))
        ->whereRaw('hours_gap > 0 AND hours_gap < 0.25')
        ->selectRaw('SUM(sog * hours_gap) as miles')
        ->value('miles');

        $status = 'Offline';
        $lastSeenAge = 'No data';

        if ($latest) {
            try {
                $lastSeen = Carbon::parse($latest->date . ' ' . $latest->utc);
                $minutesAgo = $lastSeen->diffInMinutes(now());
                $lastSeenAge = $lastSeen->diffForHumans();

                if ($minutesAgo <= 30) {
                    $status = 'Online';
                } elseif ($minutesAgo <= 360) {
                    $status = 'Stale';
                } else {
                    $status = 'Offline';
                }
            } catch (\Exception $e) {
                $status = 'Unknown';
            }
        }

        return view('boat_stats', [
            'boats' => $boats,
            'mac' => $mac,
            'year' => $year,
            'summary' => $summary,
            'daily' => $daily,
            'monthly' => $monthly,
            'latest' => $latest,
            'track' => $track,
            'totalMiles' => $totalMiles ?? 0,
            'status' => $status,
            'lastSeenAge' => $lastSeenAge,
        ]);
    }
}