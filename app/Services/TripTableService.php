<?php

namespace App\Services;

use App\Models\BoatData;

class TripTableService
{
    public function getTableRows(string $date, string $mac): array
    {
        return BoatData::forBoat($mac)
            ->onTripDate($date)
            ->orderBy('datetime', 'asc')
            ->get()
            ->map(fn ($info) => $this->formatRow($info))
            ->values()
            ->all();
    }

    private function formatRow($info): array
    {
        $row = [
            'utc' => $info->utc,
            'location' => $info->latdec . ' ' . $info->londec,
            'sog' => is_null($info->sog) ? '-' : round($info->sog, 1) . 'kts',
            'cog' => is_null($info->cog) ? '-' : $info->cog . '&deg',
            'depth' => is_null($info->dep) ? '-' : $info->dep . 'm',
            'heading' => is_null($info->hdg) ? '-' : $info->hdg . '&deg',
            'pitch' => is_null($info->pitch) ? '-' : $info->pitch . '&deg',
            'roll' => is_null($info->roll) ? '-' : $info->roll . '&deg',
            'speed' => is_null($info->spd) ? '-' : $info->spd . 'kts',
            'tank1' => is_null($info->tank1) ? '-' : $info->tank1 . '%',
            'tank2' => is_null($info->tank2) ? '-' : $info->tank2 . '%',
            'tank3' => is_null($info->tank3) ? '-' : $info->tank3 . '%',
            'tank4' => is_null($info->tank4) ? '-' : $info->tank4 . '%',
            'rpm1' => is_null($info->rpm1) ? '-' : $info->rpm1,
            'fuelr1' => is_null($info->fuelr1) ? '-' : $info->fuelr1 . 'l/hr',
            'rpm2' => is_null($info->rpm2) ? '-' : $info->rpm2,
            'fuelr2' => is_null($info->fuelr2) ? '-' : $info->fuelr2 . 'l/hr',
            'awa' => (is_null($info->aws) || is_null($info->awa)) ? '-' : $info->aws . 'kts @' . $info->awa . '&deg',
            'delete_button' => '<a href="javascript:void(0)" class="mr-3 btn btn-outline-danger btn-sm deleteData" data-id="' . $info->id . '">Delete</a>',
        ];

        foreach ($row as &$value) {
            if (!mb_check_encoding($value, 'UTF-8')) {
                $value = mb_convert_encoding($value, 'UTF-8');
            }
        }

        return $row;
    }
}