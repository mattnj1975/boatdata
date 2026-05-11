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
use App\Services\TripEngineService;
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
public function fetchEngine(Request $request, TripEngineService $tripEngineService)
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
        $tripEngineService->getEngineSeries($uid, $start, $end)
    );
}



    public function deleteBoatData($data_id)
    {
        BoatData::find($data_id)->delete();
        return response()->json(['success' => 'Data deleted successfully.']);
    }
    
}