<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class FleetStatusService
{
    public function getPublicFleetStats(): array
    {
        return Cache::remember('public_fleet_stats_home', 600, function () {

$boats = DB::select("
    SELECT
        s.boatname,
        b.mac,

        CASE
            WHEN b.ns = 'S'
                THEN -1 * (FLOOR(b.lat / 100) + ((b.lat - (FLOOR(b.lat / 100) * 100)) / 60))
            ELSE
                FLOOR(b.lat / 100) + ((b.lat - (FLOOR(b.lat / 100) * 100)) / 60)
        END AS lat,

        CASE
            WHEN b.ew = 'W'
                THEN -1 * (FLOOR(b.lon / 100) + ((b.lon - (FLOOR(b.lon / 100) * 100)) / 60))
            ELSE
                FLOOR(b.lon / 100) + ((b.lon - (FLOOR(b.lon / 100) * 100)) / 60)
        END AS lon,

        TIMESTAMP(b.date, b.utc) AS last_seen

    FROM settings s

    INNER JOIN (
        SELECT mac, MAX(id) AS max_id
        FROM boatdata
        WHERE val = 'A'
        AND lat IS NOT NULL
        AND lon IS NOT NULL
        AND date >= CURDATE() - INTERVAL 365 DAY
        GROUP BY mac
    ) latest
        ON latest.mac = s.mac

    INNER JOIN boatdata b
        ON b.id = latest.max_id

    WHERE s.public = 1

    ORDER BY s.boatname
");

            $boats = collect($boats);

            return [
                'stats' => (object) [
                    'boats' => $boats->count(),
                ],
                'boats' => $boats,
                'topBoats' => $boats,
            ];
        });
    }
}