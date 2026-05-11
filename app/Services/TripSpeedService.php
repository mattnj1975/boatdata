<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TripSpeedService
{
    public function getSpeedSeries(string $uid, string $start, string $end): array
    {
        return [
            'mySOG' => $this->getSeries($uid, $start, $end, 'sog'),
            'mySPD' => $this->getSeries($uid, $start, $end, 'spd'),
            'myAWS' => $this->getSeries($uid, $start, $end, 'aws'),
            'myTWS' => $this->getSeries($uid, $start, $end, 'tws'),
            'myGWS' => $this->getSeries($uid, $start, $end, 'gws'),
            'myVMG' => $this->getSeries($uid, $start, $end, 'vmg'),
        ];
    }

    private function getSeries(string $uid, string $start, string $end, string $column)
    {
        return DB::table('boatdata')
            ->select(
                DB::raw("AVG({$column}) as {$column}"),
                DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc')
            )
            ->where('mac', $uid)
            ->whereNotNull($column)
            ->where('utc', '!=', '00:00:00')
            ->whereTime('utc', '<=', '24:00:00')
            ->whereBetween('date', [$start, $end])
            ->groupBy('ep_utc')
            ->limit(2000)
            ->get()
            ->map(function ($item) use ($column) {
                return [round($item->ep_utc), (float) $item->{$column}];
            });
    }
}