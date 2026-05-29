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

        $boats = DB::table('settings')
            ->where('public', 1)
            ->whereNotNull('mac')
            ->orderBy('boatname')
            ->select('mac', 'boatname')
            ->get();

        $mac = $mac ?: optional($boats->first())->mac;

        if (!$mac) {
            abort(404);
        }

        $summary = DB::table('boatdata')
            ->where('mac', $mac)
            ->whereYear('date', $year)
            ->selectRaw('
                MAX(NULLIF(sog,0)) as top_speed,
                AVG(NULLIF(sog,0)) as avg_speed,
                MAX(NULLIF(aws,0)) as top_wind,
                AVG(NULLIF(aws,0)) as avg_wind,
                COUNT(DISTINCT date) as days_used
            ')
            ->first();

        $daily = DB::table('boatdata')
            ->where('mac', $mac)
            ->whereYear('date', $year)
            ->groupBy('date')
            ->orderBy('date')
            ->selectRaw('
                date,
                MAX(NULLIF(sog,0)) as max_sog,
                AVG(NULLIF(sog,0)) as avg_sog,
                MAX(NULLIF(aws,0)) as max_aws,
                AVG(NULLIF(aws,0)) as avg_aws,
                SUM(NULLIF(fuelr1,0)) as fuel1_total,
                AVG(NULLIF(fuelr1,0)) as fuel1_avg,
                SUM(NULLIF(fuelr2,0)) as fuel2_total,
                AVG(NULLIF(fuelr2,0)) as fuel2_avg,
                MAX(NULLIF(rpm1,0)) as max_rpm1,
                MAX(NULLIF(rpm2,0)) as max_rpm2,
                AVG(NULLIF(rpm1,0)) as avg_rpm1,
                AVG(NULLIF(rpm2,0)) as avg_rpm2,
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
                MAX(NULLIF(sog,0)) as max_sog,
                AVG(NULLIF(sog,0)) as avg_sog
            ')
            ->get();

        /*
         * NEW: latest/live data now comes from boat_latest,
         * not from the big boatdata table.
         */
$latest = DB::table('boat_latest')
    ->where('mac', $mac)
    ->select(
        'mac',
        'last_seen',
        'latdec',
        'londec',
        'sog',
        'cog',
        'hdg',
        'depth',
        'aws'
    )
    ->first();

$statusLastSeen = $latest->last_seen ?? $deviceSettings->lastseen ?? null;

$status = 'Offline';
$lastSeenAge = 'Unknown';

if ($statusLastSeen) {
    try {
        $lastSeen = Carbon::parse($statusLastSeen);
        $mins = max(0, $lastSeen->diffInMinutes(now(), false));

        $lastSeenAge = $mins < 1
            ? 'just now'
            : $lastSeen->diffForHumans(null, true) . ' ago';

        if ($mins <= 15) {
            $status = 'Online';
        } elseif ($mins <= 120) {
            $status = 'Idle';
        } elseif ($mins <= 1440) {
            $status = 'Stale';
        }
    } catch (\Exception $e) {
        //
    }
}

        $totalMiles = DB::table(DB::raw("
            (
                SELECT
                    sog,
                    TIMESTAMPDIFF(
                        SECOND,
                        LAG(CONCAT(date,' ',utc)) OVER (ORDER BY date,utc),
                        CONCAT(date,' ',utc)
                    ) / 3600 AS hours_gap
                FROM boatdata
                WHERE mac = " . DB::getPdo()->quote($mac) . "
                AND YEAR(date) = " . (int)$year . "
                AND sog BETWEEN 0.2 AND 50
            ) x
        "))
        ->whereRaw('hours_gap > 0 AND hours_gap < 0.25')
        ->selectRaw('SUM(sog * hours_gap) as miles')
        ->value('miles');

        $deviceSettings = DB::table('settings')
            ->where('mac', $mac)
            ->select(
                'boatname',
                'serial',
                'gprsuser',
                'update_to',
                'lastseen',
                'version',
                'plan'
            )
            ->first();

        /*
         * Prefer boat_latest.last_seen.
         * Fall back to settings.lastseen if boat_latest has no row yet.
         */
        $statusLastSeen = $latest->last_seen ?? $deviceSettings->lastseen ?? null;

        $status = 'Offline';
        $lastSeenAge = 'Unknown';

        if ($statusLastSeen) {
            try {
                $lastSeen = Carbon::parse($statusLastSeen);
                $mins = max(0, $lastSeen->diffInMinutes(now(), false));

                $lastSeenAge = $mins === 0
                    ? 'just now'
                    : $lastSeen->diffForHumans();

                if ($mins <= 15) {
                    $status = 'Online';
                } elseif ($mins <= 120) {
                    $status = 'Idle';
                } elseif ($mins <= 1440) {
                    $status = 'Stale';
                }
            } catch (\Exception $e) {
            }
        }

        $uploadLogs = DB::table('uploadlog')
            ->where('device_id', $mac)
            ->orderByDesc('uload_time')
            ->limit(50)
            ->select(
                'uload_time',
                'upload_status',
                'ip_address',
                'sd_space',
                'sd_used',
                'db_ok',
                'db_err'
            )
            ->get();

        $uploadStatusCodes = [
            0 => 'Success',
            1 => 'UnknownErr',
            2 => 'FileSmall',
            3 => 'NoServer',
            4 => 'NoWiFi',
            5 => 'APIwrong',
            6 => 'noEOF',
            7 => 'Timeout',
            8 => 'Disabled',
            9 => 'Ready',
            10 => 'Booting',
            11 => 'NoDate',
            12 => 'NoSDCard',
            13 => 'GPRSPostErr',
            14 => 'NoGPRS',
            15 => 'Reboot',
            16 => 'GPSReboot',
        ];

        $boatPlan = (int) ($deviceSettings->plan ?? 100);

        return view('boat_stats', [
            'boats' => $boats,
            'mac' => $mac,
            'year' => $year,
            'summary' => $summary,
            'daily' => $daily,
            'monthly' => $monthly,
            'latest' => $latest,
            'totalMiles' => $totalMiles ?? 0,
            'status' => $status,
            'lastSeenAge' => $lastSeenAge,
            'statusLastSeen' => $statusLastSeen,
            'deviceSettings' => $deviceSettings,
            'uploadLogs' => $uploadLogs,
            'uploadStatusCodes' => $uploadStatusCodes,
            'boatPlan' => $boatPlan,
        ]);
    }
}