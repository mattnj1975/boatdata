<?php

namespace App\Services;

use App\Models\BoatData;

class TripTrackService
{
    public function getTrackData(string $date, string $mac): object
    {
        $dataPoints = $this->baseQuery($date, $mac)
            ->orderBy('datetime', 'asc')
            ->get();

        $points = $dataPoints->map(function ($info) {
            return [
                'time' => $info->utc,
                'lat' => $info->latdec,
                'lon' => $info->londec,
                'sog' => is_null($info->sog) ? '-' : round($info->sog, 1),
                'dog' => ($info->dog == 0) ? '-' : round($info->dog, 1),
                'cog' => is_null($info->cog) ? '-' : $info->cog,
                'dep' => is_null($info->dep) ? '-' : $info->dep,
                'hdg' => is_null($info->hdg) ? '-' : $info->hdg,
                'spd' => is_null($info->spd) ? '-' : $info->spd,
                'rpm1' => is_null($info->rpm1) ? '-' : $info->rpm1,
                'fuelr1' => is_null($info->fuelr1) ? '-' : $info->fuelr1,
                'rpm2' => is_null($info->rpm2) ? '-' : $info->rpm2,
                'fuelr2' => is_null($info->fuelr2) ? '-' : $info->fuelr2,
                'aws' => is_null($info->aws) ? '-' : $info->aws,
                'awa' => is_null($info->awa) ? '-' : $info->awa,
                'tws' => is_null($info->tws) ? '-' : $info->tws,
                'twa' => is_null($info->twa) ? '-' : $info->twa,
            ];
});

        $maxes = [
            'startTime' => $this->baseQuery($date, $mac)->orderBy('datetime', 'asc')->first(),
            'endTime' => $this->baseQuery($date, $mac)->orderBy('datetime', 'desc')->first(),
            'distance' => optional($this->baseQuery($date, $mac)->orderBy('dist', 'desc')->first())->dist,
            'gpsdist' => optional($this->baseQuery($date, $mac)->orderBy('dog', 'desc')->first())->dog,
            'speed' => $this->baseQuery($date, $mac)->orderBy('spd', 'desc')->first(),
            'sog' => $this->baseQuery($date, $mac)->orderBy('sog', 'desc')->first(),
            'minDepth' => $this->baseQuery($date, $mac)->where('dep', '>', 0)->orderBy('dep', 'asc')->first(),
            'awind' => $this->baseQuery($date, $mac)->orderBy('aws', 'desc')->first(),
            'twind' => $this->baseQuery($date, $mac)->orderBy('tws', 'desc')->first(),
        ];

        $output = new \stdClass();
        $output->points = $points;
        $output->maxes = $maxes;

        return $output;
    }

    private function baseQuery(string $date, string $mac)
    {
        return BoatData::where('date', $date)
            ->where('mac', $mac)
            ->where('val', 'A')
            ->where('utc', '!=', '00:00:00')
            ->whereTime('utc', '<=', '24:00:00');
    }
}