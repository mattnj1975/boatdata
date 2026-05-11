<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\BoatData;
use App\Models\calendar;
use App\Services\TripSpeedService;
use App\Services\TripTrackService;
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
    public function getLogData(Request $request)
    {
        $start = $request->input('start');
        $timestamp = strtotime(str_replace('/', '-', $start));
        $start = date('Y-m-d', $timestamp);
    
        $end = $request->input('end');
        $timestamp = strtotime(str_replace('/', '-', $end));
        $end = date('Y-m-d', $timestamp);
    
        $mac = $request->input('mac');
    
        // Fetch data using Eloquent ORM
        $boatdata = BoatData::select(DB::raw('id, mac, val, sog, latdec, londec, cog, dep, hdg, spd, rpm1, fuelr1, rpm2, fuelr2, awa, HOUR(utc) AS hour, MINUTE(utc) AS minute'))
            ->whereBetween('date', [$start, $end])
            ->where('mac', $mac)
            ->where('val', 'A')
            ->whereRaw('MINUTE(utc) % 15 = 0')
            ->groupBy('hour', 'minute', 'id', 'mac', 'val', 'utc', 'sog', 'latdec', 'londec', 'cog', 'dep', 'hdg', 'spd', 'rpm1', 'fuelr1', 'rpm2', 'fuelr2', 'awa')
            ->orderBy('utc', 'asc')
            ->get();
    
        // Initialize variables to track previous hour and minute
        $prevHour = null;
        $prevMinute = null;
    
        // Initialize the HTML string
        $html = '';
    
        foreach ($boatdata as $info) {
            // Check if the current hour and minute are different from the previous ones
            if ($info->hour != $prevHour || $info->minute != $prevMinute) {
                // Set the current hour and minute as the previous ones for the next iteration
                $prevHour = $info->hour;
                $prevMinute = $info->minute;
    
                $html .= "<tr>";
                if ($info->minute == '0') {
                    $info->minute = '00';
                }
                $cols = [];
                $cols[0] = $info->hour . ':' . $info->minute;
                $cols[1] = number_format($info->latdec, 6, '.', ',') . $info->ns . ', ' . number_format($info->londec, 6, '.', ',') . $info->ew;
                $cols[2] = ($info->sog == NULL) ? "-" : round($info->sog, 1) . "kts";
                $cols[3] = ($info->cog == NULL) ? "-" : $info->cog . "&deg";
                $cols[4] = ($info->dep == NULL) ? "-" : $info->dep . "m";
                $cols[5] = ($info->hdg == NULL) ? "-" : $info->hdg . "&deg";
                $cols[6] = ($info->spd == NULL) ? "-" : $info->spd . "kts";
                $cols[7] = ($info->aws == NULL) ? "-" : $info->aws . "kts @" . $info->awa . "&deg";
                $cols[8] = ($info->rpm1 == NULL) ? "-" : $info->rpm1;
                $cols[9] = ($info->fuelr1 == NULL) ? "-" : $info->fuelr1 . "l/hr";
                $cols[10] = ($info->rpm2 == NULL) ? "-" : $info->rpm2;
                $cols[11] = ($info->fuelr2 == NULL) ? "-" : $info->fuelr2 . "l/hr";
    
                foreach ($cols as $col) {
                    $html .= "<td>" . $col . "</td>";
                }
                if ($request->front == 1) {
                    
                } else {
                    if (Auth::user() && Auth::user()->role_as != 3) {
                     //   $html .= "<td><div style='margin-right:10px'><a href='javascript:void(0)' data-toggle='tooltip' data-id='" . $info->id . "' data-original-title='Delete' class='mr-3 btn btn-outline-danger btn-sm deleteData'>Delete</a></div></td>";
                        $html .= "<td><div style='margin-right:10px'></div></td>";
                    }
                }
    
                $html .= "</tr>";
            }
        }
    
        return $html;
    }

    public function getTableData(Request $request)
    {
        $date = $request->input('date');
        $timestamp = strtotime(str_replace('/', '-', $date));
        $date = date('Y-m-d', $timestamp);
        $mac = $request->input('mac');
    
        // Fetch data using Eloquent ORM
        $dataPoints = BoatData::where('date', $date)
                              ->where('mac', $mac)
							  ->orderBy('datetime', 'asc')
                              ->get();
    
        $jsonData = [];
        foreach ($dataPoints as $info) {
            $rowData = [
                'utc' => $info->utc,
                //'location' => number_format($info->lat, 4, '.', ',') . $info->ns . ', ' . number_format($info->lon, 4, '.', ',') . $info->ew,
                'location' => $info->latdec .  ' ' . $info->londec,
                'sog' => ($info->sog == NULL) ? "-" : round($info->sog, 1) . "kts",
                'cog' => ($info->cog == NULL) ? "-" : $info->cog . "&deg",
                'depth' => ($info->dep == NULL) ? "-" : $info->dep . "m",
                'heading' => ($info->hdg == NULL) ? "-" : $info->hdg . "&deg",
                'pitch' => ($info->pitch == NULL) ? "-" : $info->pitch . "&deg",
                'roll' => ($info->roll == NULL) ? "-" : $info->roll . "&deg",
                'speed' => ($info->spd == NULL) ? "-" : $info->spd . "kts",
                'tank1' => ($info->tank1 == NULL) ? "-" : $info->tank1 . "%",
                'tank2' => ($info->tank2 == NULL) ? "-" : $info->tank2 . "%",
                'tank3' => ($info->tank3 == NULL) ? "-" : $info->tank3 . "%",
                'tank4' => ($info->tank4 == NULL) ? "-" : $info->tank4 . "%",

                'rpm1' => ($info->rpm1 == NULL) ? "-" : $info->rpm1,
                'fuelr1' => ($info->fuelr1 == NULL) ? "-" : $info->fuelr1 . "l/hr",
                'rpm2' => ($info->rpm2 == NULL) ? "-" : $info->rpm2,
                'fuelr2' => ($info->fuelr2 == NULL) ? "-" : $info->fuelr2 . "l/hr",
                'awa' => ($info->aws == NULL || $info->awa == NULL) ? "-" : $info->aws . "kts @" . $info->awa . "&deg",
                'delete_button' => '<a href="javascript:void(0)" class="mr-3 btn btn-outline-danger btn-sm deleteData" data-id="' . $info->id . '">Delete</a>',
            ];
    
            // Encode each value in UTF-8 to ensure proper encoding
            foreach ($rowData as &$value) {
                if (!mb_check_encoding($value, 'UTF-8')) {
                    $value = mb_convert_encoding($value, 'UTF-8');
                }
            }
    
            $jsonData[] = $rowData;
        }
        
        return Response::json($jsonData, 200, [], JSON_UNESCAPED_UNICODE);
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