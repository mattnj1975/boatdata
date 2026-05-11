<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\BoatData;
use App\Models\calendar;
use App\Services\TripSpeedService;
use App\Services\TripTrackService;
use App\Services\TripTableService;
use App\Services\TripLogService;
use Carbon\Carbon;
use Auth;
use DB;
use Illuminate\Support\Facades\Response;
class TripsController extends Controller
{
    public function myTrips(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));

        // BUILD CALENDAR
        $calendar = new Calendar($date);

        // GET TRIPS FOR CURRENT MONTH AND ADD TO CALENDAR
        $tripDays = BoatData::select('mac', 'date')
            ->where('val', 'A')
            ->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->distinct()
            ->get();

        foreach ($tripDays as $tripDay) {
            $calendar->add_event($tripDay->mac, $tripDay->date, 1, 'blue');
        }

        $monthStart = date("Y-m-01", strtotime($date));
        $monthEnd = date("Y-m-t", strtotime($date));

        $results = BoatData::select('settings.boatname', 'boatdata.mac', 'boatdata.date as TripDate')
            ->selectRaw('min(boatdata.utc) AS Begin, max(boatdata.utc) AS Finish')
            ->selectRaw('MIN(NULLIF(ROUND(boatdata.tdist,1), NULL)) as Start, MAX(NULLIF(ROUND(boatdata.tdist,1), NULL)) as End')
            ->selectRaw('NULLIF(MAX(ROUND(dog_nm,1)),0) as Trip')
            ->leftJoin('settings', 'settings.mac', '=', 'boatdata.mac')
            ->whereBetween('boatdata.date', [$monthStart, $monthEnd])
            ->where('val', 'A')
            ->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->groupBy('settings.boatname', 'boatdata.mac', 'boatdata.date') // Include Settings.boatname in GROUP BY
            ->get();

        $trips = $results->toArray();
        

       return view('admin.trips.index', compact('calendar', 'trips', 'date'));
    }
    public function loadCalendar(Request $request)
    {
        $date = $request->date ? date('Y-m-d', strtotime($request->date)) : date('Y-m-d');

        $calendar = new Calendar($date);
        $tripDays = BoatData::where('val', 'A')
        ->distinct()
        ->get(['mac', 'date']);
        foreach ($tripDays as $info) {
            $calendar->add_event($info->mac, $info->date, 1, 'blue');
        }

        return response()->json($calendar->__toString());
    }
    public function masterTrips(Request $request)
    {
        
        $date = $request->input('date', date('Y-m-d'));

        // BUILD CALENDAR
        $calendar = new Calendar($date);

        // GET TRIPS FOR CURRENT MONTH AND ADD TO CALENDAR
        $tripDays = BoatData::select('date', 'settings.boatname')
            ->leftJoin('settings', 'settings.mac', '=', 'boatdata.mac')
            ->join('admin_boats', 'admin_boats.boat_id', '=', 'settings.id')
            ->where('admin_boats.user_id', '=', Auth::id())
            ->where('val', 'A')
            ->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->distinct()
            ->get();

        foreach ($tripDays as $tripDay) {
            $calendar->add_event($tripDay->mac, $tripDay->date, 1, 'blue');
        }

        $monthStart = date("Y-m-01", strtotime($date));
        $monthEnd = date("Y-m-t", strtotime($date));

        $results = BoatData::select('settings.boatname', 'boatdata.mac', 'boatdata.date as TripDate')
            ->selectRaw('min(boatdata.utc) AS Begin, max(boatdata.utc) AS Finish')
            ->selectRaw('MIN(NULLIF(ROUND(boatdata.tdist,1), NULL)) as Start, MAX(NULLIF(ROUND(boatdata.tdist,1), NULL)) as End')
            ->selectRaw('NULLIF(MAX(ROUND(dog_nm,1)),0) as Trip')
            ->leftJoin('settings', 'settings.mac', '=', 'boatdata.mac')
            ->join('admin_boats', 'admin_boats.boat_id', '=', 'settings.id')
            ->where('admin_boats.user_id', '=', Auth::id())
            ->whereBetween('boatdata.date', [$monthStart, $monthEnd])
            ->where('val', 'A')
            ->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->groupBy('settings.boatname', 'boatdata.mac', 'boatdata.date')
            ->get();

        $trips = $results->toArray();

       return view('master.trips.index', compact('calendar', 'trips', 'date'));
    }
    public function userTrips(Request $request)
    {
        
        $date = $request->input('date', date('Y-m-d'));

        // BUILD CALENDAR
        $calendar = new Calendar($date);

        // GET TRIPS FOR CURRENT MONTH AND ADD TO CALENDAR
        $tripDays = BoatData::select('date', 'settings.boatname')
            ->leftJoin('settings', 'settings.mac', '=', 'boatdata.mac')
            ->join('user_boats', 'user_boats.boat_id', '=', 'settings.id')
            ->where('user_boats.user_id', '=', Auth::id())
            ->where('val', 'A')
            ->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            
            ->distinct()
            ->get();

        foreach ($tripDays as $tripDay) {
            $calendar->add_event($tripDay->mac, $tripDay->date, 1, 'blue');
        }

        $monthStart = date("Y-m-01", strtotime($date));
        $monthEnd = date("Y-m-t", strtotime($date));

        $results = BoatData::select('settings.boatname', 'boatdata.mac', 'boatdata.date as TripDate')
            ->selectRaw('min(boatdata.utc) AS Begin, max(boatdata.utc) AS Finish')
            ->selectRaw('MIN(NULLIF(ROUND(boatdata.tdist,1), NULL)) as Start, MAX(NULLIF(ROUND(boatdata.tdist,1), NULL)) as End')
            ->selectRaw('NULLIF(MAX(ROUND(dog_nm,1)),0) as Trip')
            ->leftJoin('settings', 'settings.mac', '=', 'boatdata.mac')
            ->join('user_boats', 'user_boats.boat_id', '=', 'settings.id')
            ->where('user_boats.user_id', '=', Auth::id())
            ->whereBetween('boatdata.date', [$monthStart, $monthEnd])
            ->where('val', 'A')
            ->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->groupBy('settings.boatname', 'boatdata.mac', 'boatdata.date')
            ->get();

        $trips = $results->toArray();

       return view('user.trips.index', compact('calendar', 'trips', 'date'));
    }
public function getTrackData(Request $request, TripTrackService $tripTrackService)
{
    $dateInput = $request->input('date');
    $mac = $request->input('mac');

    if (!$dateInput || !$mac) {
        return response()->json(['error' => 'Missing required parameters'], 400);
    }

    $date = date('Y-m-d', strtotime(str_replace('/', '-', $dateInput)));

    return response()->json(
        $tripTrackService->getTrackData($date, $mac)
    );
}
public function getLogData(Request $request, TripLogService $tripLogService)
{
    $startInput = $request->input('start');
    $endInput = $request->input('end');
    $mac = $request->input('mac');

    if (!$startInput || !$endInput || !$mac) {
        return response('Missing required parameters', 400);
    }

    $start = date('Y-m-d', strtotime(str_replace('/', '-', $startInput)));
    $end = date('Y-m-d', strtotime(str_replace('/', '-', $endInput)));

    return $tripLogService->getLogHtml(
        $start,
        $end,
        $mac,
        (bool) $request->input('front')
    );
}

public function getTableData(Request $request, TripTableService $tripTableService)
{
    $dateInput = $request->input('date');
    $mac = $request->input('mac');

    if (!$dateInput || !$mac) {
        return response()->json(['error' => 'Missing required parameters'], 400);
    }

    $date = date('Y-m-d', strtotime(str_replace('/', '-', $dateInput)));

    return Response::json(
        $tripTableService->getTableRows($date, $mac),
        200,
        [],
        JSON_UNESCAPED_UNICODE
    );
}
public function fetchSpeed(Request $request, TripSpeedService $tripSpeedService)
{
    $uid = $request->input('uid');
    $startInput = $request->input('start');
    $endInput = $request->input('end');

    if (!$uid || !$startInput || !$endInput) {
        return response()->json(['error' => 'Missing required parameters'], 400);
    }

    $start = date('Y-m-d', strtotime(str_replace('/', '-', $startInput)));
    $end = date('Y-m-d', strtotime(str_replace('/', '-', $endInput)));

    return response()->json(
        $tripSpeedService->getSpeedSeries($uid, $start, $end)
    );
}
 public function fetchEngine(Request $request)
{
    $uid = $request->input('uid');
    $start = $request->input('start');
    $timestamp = strtotime(str_replace('/', '-', $start));
    $start = date('Y-m-d', $timestamp);

    $end = $request->input('end');
    $timestamp = strtotime(str_replace('/', '-', $end));
    $end = date('Y-m-d', $timestamp);

    if (!$uid || !$start || !$end) {
        return response()->json(['error' => 'Missing required parameters'], 400);
    }

    $myRPM1 = DB::table('boatdata')
        ->select(DB::raw('AVG(rpm1) as rpm1'), DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
        ->where('mac', $uid)
        ->where('utc', '!=', '00:00:00')
        ->whereTime('utc', '<=', '24:00:00')
        ->whereNotNull('rpm1')
        ->whereBetween('date', [$start, $end])
        ->groupBy('ep_utc')
        ->limit(2000)
        ->get()
        ->map(fn($item) => [(int) round($item->ep_utc), (float) round($item->rpm1, 2)]);

    $myRPM2 = DB::table('boatdata')
        ->select(DB::raw('AVG(rpm2) as rpm2'), DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
        ->where('mac', $uid)
        ->where('utc', '!=', '00:00:00')
        ->whereTime('utc', '<=', '24:00:00')
        ->whereNotNull('rpm2')
        ->whereBetween('date', [$start, $end])
        ->groupBy('ep_utc')
        ->limit(2000)
        ->get()
        ->map(fn($item) => [(int) round($item->ep_utc), (float) round($item->rpm2, 2)]);

    $myBOOST1 = DB::table('boatdata')
        ->select(DB::raw('AVG(boost1) as boost1'), DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
        ->where('mac', $uid)
        ->where('utc', '!=', '00:00:00')
        ->whereTime('utc', '<=', '24:00:00')
        ->whereNotNull('boost1')
        ->groupBy('ep_utc')
        ->whereBetween('date', [$start, $end])
        ->limit(2000)
        ->get()
        ->map(fn($item) => [(int) round($item->ep_utc), (float) round($item->boost1, 2)]);

    $myBOOST2 = DB::table('boatdata')
        ->select(DB::raw('AVG(boost2) as boost2'), DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
        ->where('mac', $uid)
        ->where('utc', '!=', '00:00:00')
        ->whereTime('utc', '<=', '24:00:00')
        ->whereNotNull('boost2')
        ->groupBy('ep_utc')
        ->whereBetween('date', [$start, $end])
        ->limit(2000)
        ->get()
        ->map(fn($item) => [(int) round($item->ep_utc), (float) round($item->boost2, 2)]);

    $myFUELR1 = DB::table('boatdata')
        ->select(DB::raw('AVG(fuelr1) as fuelr1'), DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
        ->where('mac', $uid)
        ->where('utc', '!=', '00:00:00')
        ->whereTime('utc', '<=', '24:00:00')
        ->whereNotNull('fuelr1')
        ->whereBetween('date', [$start, $end])
        ->groupBy('ep_utc')
        ->limit(2000)
        ->get()
        ->map(fn($item) => [(int) round($item->ep_utc), (float) round($item->fuelr1, 2)]);

    $myFUELR2 = DB::table('boatdata')
        ->select(DB::raw('AVG(fuelr2) as fuelr2'), DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
        ->where('mac', $uid)
        ->where('utc', '!=', '00:00:00')
        ->whereTime('utc', '<=', '24:00:00')
        ->whereNotNull('fuelr2')
        ->whereBetween('date', [$start, $end])
        ->groupBy('ep_utc')
        ->limit(2000)
        ->get()
        ->map(fn($item) => [(int) round($item->ep_utc), (float) round($item->fuelr2, 2)]);

    $myLOAD1 = DB::table('boatdata')
        ->select(DB::raw('AVG(load1) as load1'), DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
        ->where('mac', $uid)
        ->where('utc', '!=', '00:00:00')
        ->whereTime('utc', '<=', '24:00:00')
        ->whereNotNull('load1')
        ->whereBetween('date', [$start, $end])
        ->groupBy('ep_utc')
        ->limit(2000)
        ->get()
        ->map(fn($item) => [(int) round($item->ep_utc), (float) round($item->load1, 2)]);

    $myLOAD2 = DB::table('boatdata')
        ->select(DB::raw('AVG(load2) as load2'), DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
        ->where('mac', $uid)
        ->where('utc', '!=', '00:00:00')
        ->whereTime('utc', '<=', '24:00:00')
        ->whereNotNull('load2')
        ->whereBetween('date', [$start, $end])
        ->groupBy('ep_utc')
        ->limit(2000)
        ->get()
        ->map(fn($item) => [(int) round($item->ep_utc), (float) round($item->load2, 2)]);

$mySOG = DB::table('boatdata')
    ->select(
        DB::raw('AVG(sog) as sog'),
        DB::raw('FLOOR(UNIX_TIMESTAMP(CONCAT(date, " ", utc)) / 5) * 5000 as ep_utc') // 5s buckets
    )
    ->where('mac', $uid)
    ->whereNotNull('sog')
    ->where('utc', '!=', '00:00:00')
    ->whereTime('utc', '<=', '24:00:00')
    ->whereBetween('date', [$start, $end])
    ->groupBy('ep_utc')
    ->limit(2000)
    ->get()
    ->map(fn($item) => [(int) $item->ep_utc, round((float) $item->sog, 2)]);


    $myCOOLT1 = DB::table('boatdata')
        ->select(DB::raw('AVG(coolt1) as coolt1'), DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
        ->where('mac', $uid)
        ->where('utc', '!=', '00:00:00')
        ->whereTime('utc', '<=', '24:00:00')
        ->whereNotNull('coolt1')
        ->whereBetween('date', [$start, $end])
        ->groupBy('ep_utc')
        ->limit(2000)
        ->get()
        ->map(fn($item) => [(int) round($item->ep_utc), (float) round($item->coolt1, 2)]);

    $myCOOLT2 = DB::table('boatdata')
        ->select(DB::raw('AVG(coolt2) as coolt2'), DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
        ->where('mac', $uid)
        ->where('utc', '!=', '00:00:00')
        ->whereTime('utc', '<=', '24:00:00')
        ->whereNotNull('coolt2')
        ->whereBetween('date', [$start, $end])
        ->groupBy('ep_utc')
        ->limit(2000)
        ->get()
        ->map(fn($item) => [(int) round($item->ep_utc), (float) round($item->coolt2, 2)]);

    $myECON1 = DB::table('boatdata')
        ->select(DB::raw('AVG(eng1_econ) as eng1_econ'), DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
        ->where('mac', $uid)
        ->where('utc', '!=', '00:00:00')
        ->whereTime('utc', '<=', '24:00:00')
        ->where('sog', '>', 2)
        ->where('eng1_econ', '>', 0)
        ->whereBetween('date', [$start, $end])
        ->groupBy('ep_utc')
        ->limit(2000)
        ->get()
        ->map(fn($item) => [(int) round($item->ep_utc), (float) round($item->eng1_econ, 2)]);

    $myECON2 = DB::table('boatdata')
        ->select(DB::raw('AVG(eng2_econ) as eng2_econ'), DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
        ->where('mac', $uid)
        ->where('utc', '!=', '00:00:00')
        ->whereTime('utc', '<=', '24:00:00')
        ->where('sog', '>', 2)
        ->where('eng2_econ', '>', 0)
        ->whereBetween('date', [$start, $end])
        ->groupBy('ep_utc')
        ->limit(2000)
        ->get()
        ->map(fn($item) => [(int) round($item->ep_utc), (float) round($item->eng2_econ, 2)]);

    $data = [
        'myRPM1' => $myRPM1,
        'myRPM2' => $myRPM2,
        'myBOOST1' => $myBOOST1,
        'myBOOST2' => $myBOOST2,
        'myFUELR1' => $myFUELR1,
        'myFUELR2' => $myFUELR2,
        'myLOAD1' => $myLOAD1,
        'myLOAD2' => $myLOAD2,
        'mySOG' => $mySOG,
        'myCOOLT1' => $myCOOLT1,
        'myCOOLT2' => $myCOOLT2,
        'myECON1' => $myECON1,
        'myECON2' => $myECON2,
    ];




    return response()->json($data);
}



    public function deleteBoatData($data_id)
    {
        BoatData::find($data_id)->delete();
        return response()->json(['success' => 'Data deleted successfully.']);
    }
    
}