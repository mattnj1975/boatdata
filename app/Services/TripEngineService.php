<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TripEngineService
{
    public function getEngineSeries(string $uid, string $start, string $end): array
    {
        return [
            'myRPM1' => $this->getSeries($uid, $start, $end, 'rpm1'),
            'myRPM2' => $this->getSeries($uid, $start, $end, 'rpm2'),
            'myBOOST1' => $this->getSeries($uid, $start, $end, 'boost1'),
            'myBOOST2' => $this->getSeries($uid, $start, $end, 'boost2'),
            'myFUELR1' => $this->getSeries($uid, $start, $end, 'fuelr1'),
            'myFUELR2' => $this->getSeries($uid, $start, $end, 'fuelr2'),
            'myLOAD1' => $this->getSeries($uid, $start, $end, 'load1'),
            'myLOAD2' => $this->getSeries($uid, $start, $end, 'load2'),
            'mySOG' => $this->getSogSeries($uid, $start, $end),
            'myCOOLT1' => $this->getSeries($uid, $start, $end, 'coolt1'),
            'myCOOLT2' => $this->getSeries($uid, $start, $end, 'coolt2'),
            'myECON1' => $this->getEconomySeries($uid, $start, $end, 'eng1_econ'),
            'myECON2' => $this->getEconomySeries($uid, $start, $end, 'eng2_econ'),
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
            ->where('utc', '!=', '00:00:00')
            ->whereTime('utc', '<=', '24:00:00')
            ->whereNotNull($column)
            ->whereBetween('date', [$start, $end])
            ->groupBy('ep_utc')
            ->limit(2000)
            ->get()
            ->map(fn ($item) => [(int) round($item->ep_utc), (float) round($item->{$column}, 2)]);
    }

    private function getSogSeries(string $uid, string $start, string $end)
    {
        return DB::table('boatdata')
            ->select(
                DB::raw('AVG(sog) as sog'),
                DB::raw('FLOOR(UNIX_TIMESTAMP(CONCAT(date, " ", utc)) / 5) * 5000 as ep_utc')
            )
            ->where('mac', $uid)
            ->whereNotNull('sog')
            ->where('utc', '!=', '00:00:00')
            ->whereTime('utc', '<=', '24:00:00')
            ->whereBetween('date', [$start, $end])
            ->groupBy('ep_utc')
            ->limit(2000)
            ->get()
            ->map(fn ($item) => [(int) $item->ep_utc, round((float) $item->sog, 2)]);
    }

    private function getEconomySeries(string $uid, string $start, string $end, string $column)
    {
        return DB::table('boatdata')
            ->select(
                DB::raw("AVG({$column}) as {$column}"),
                DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc')
            )
            ->where('mac', $uid)
            ->where('utc', '!=', '00:00:00')
            ->whereTime('utc', '<=', '24:00:00')
            ->where('sog', '>', 2)
            ->where($column, '>', 0)
            ->whereBetween('date', [$start, $end])
            ->groupBy('ep_utc')
            ->limit(2000)
            ->get()
            ->map(fn ($item) => [(int) round($item->ep_utc), (float) round($item->{$column}, 2)]);
    }
}