<?php

namespace App\Services;

use App\Models\BoatData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TripLogService
{
    public function getLogHtml(string $start, string $end, string $mac, bool $front = false): string
    {
        $boatdata = BoatData::select(DB::raw('id, mac, val, sog, latdec, londec, cog, dep, hdg, spd, rpm1, fuelr1, rpm2, fuelr2, aws, awa, HOUR(utc) AS hour, MINUTE(utc) AS minute'))
            ->whereBetween('date', [$start, $end])
            ->forBoat($mac)
            ->where('val', 'A')
            ->whereRaw('MINUTE(utc) % 15 = 0')
            ->groupBy('hour', 'minute', 'id', 'mac', 'val', 'utc', 'sog', 'latdec', 'londec', 'cog', 'dep', 'hdg', 'spd', 'rpm1', 'fuelr1', 'rpm2', 'fuelr2', 'aws', 'awa')
            ->orderBy('utc', 'asc')
            ->get();

        $prevHour = null;
        $prevMinute = null;
        $html = '';

        foreach ($boatdata as $info) {
            if ($info->hour == $prevHour && $info->minute == $prevMinute) {
                continue;
            }

            $prevHour = $info->hour;
            $prevMinute = $info->minute;

            $html .= '<tr>';

            $minute = ((int) $info->minute === 0) ? '00' : $info->minute;

            $cols = [
                $info->hour . ':' . $minute,
                number_format($info->latdec, 6, '.', ',') . ', ' . number_format($info->londec, 6, '.', ','),
                is_null($info->sog) ? '-' : round($info->sog, 1) . 'kts',
                is_null($info->cog) ? '-' : $info->cog . '&deg',
                is_null($info->dep) ? '-' : $info->dep . 'm',
                is_null($info->hdg) ? '-' : $info->hdg . '&deg',
                is_null($info->spd) ? '-' : $info->spd . 'kts',
                is_null($info->aws) ? '-' : $info->aws . 'kts @' . $info->awa . '&deg',
                is_null($info->rpm1) ? '-' : $info->rpm1,
                is_null($info->fuelr1) ? '-' : $info->fuelr1 . 'l/hr',
                is_null($info->rpm2) ? '-' : $info->rpm2,
                is_null($info->fuelr2) ? '-' : $info->fuelr2 . 'l/hr',
            ];

            foreach ($cols as $col) {
                $html .= '<td>' . $col . '</td>';
            }

            if (!$front && Auth::user() && Auth::user()->role_as != 3) {
                $html .= "<td><div style='margin-right:10px'></div></td>";
            }

            $html .= '</tr>';
        }

        return $html;
    }
}