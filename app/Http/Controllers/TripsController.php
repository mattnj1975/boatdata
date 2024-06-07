<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\BoatData;
use App\Models\calendar;
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
            ->groupBy('settings.boatname', 'boatdata.mac', 'boatdata.date') // Include Settings.boatname in GROUP BY
            ->get();

        $trips = $results->toArray();
        

       return view('admin.trips.index', compact('calendar', 'trips', 'date'));
    }
    public function loadCalendar(Request $request)
    {
        $date = $request->date ? date('Y-m-d', strtotime($request->date)) : date('Y-m-d');

        $calendar = new Calendar($date);
        $tripDays = BoatData::where('val', 'A')->distinct()->get(['mac', 'date']);
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
            ->groupBy('settings.boatname', 'boatdata.mac', 'boatdata.date')
            ->get();

        $trips = $results->toArray();

       return view('user.trips.index', compact('calendar', 'trips', 'date'));
    }
    public function getTrackData(Request $request)
    {
        $date = $request->input('date');
        $timestamp = strtotime(str_replace('/', '-', $date));

        $date = date('Y-m-d', $timestamp);

        $mac = $request->input('mac');

        $dataPoints = BoatData::where('date', $date)
            ->where('mac', $mac)
            ->where('val', 'A')
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->orderBy('datetime', 'asc')
            ->get();

        $data = [];

        foreach ($dataPoints as $info) {
            // Process your data
            $cols = [
                'time' => $info->utc,
                'lat' => $info->latdec,
                'lon' => $info->londec,
                'sog' => ($info->sog == NULL) ? "-" : round($info->sog, 1),
                'dog' => ($info->dog == 0) ? "-" : round($info->dog, 1),
                'cog' => ($info->cog == NULL) ? "-" : $info->cog,
                'dep' => ($info->dep == NULL) ? "-" : $info->dep,
                'hdg' => ($info->hdg == NULL) ? "-" : $info->hdg,
                'spd' => ($info->spd == NULL) ? "-" : $info->spd,
                'dep' => ($info->dep == NULL) ? "-" : $info->dep,
                'rpm1' => ($info->rpm1 == NULL) ? "-" : $info->rpm1,
                'fuelr1' => ($info->fuelr1 == NULL) ? "-" : $info->fuelr1,
                'rpm2' => ($info->rpm2 == NULL) ? "-" : $info->rpm2,
                'fuelr2' => ($info->fuelr2 == NULL) ? "-" : $info->fuelr2,
                'aws' => ($info->aws == NULL) ? "-" : $info->aws,
                'awa' => ($info->awa == NULL) ? "-" : $info->awa,
                'tws' => ($info->tws == NULL) ? "-" : $info->tws,
                'twa' => ($info->twa == NULL) ? "-" : $info->twa,
            ];

            $data[] = $cols;
        }

        // Prepare max values
        $maxes = [
            'startTime' => BoatData::where('date', $date)
                ->where('mac', $mac)
                ->where('val', 'A')
                ->orderBy('datetime', 'asc')
                ->first(),
            'endTime' => BoatData::where('date', $date)
                ->where('mac', $mac)
                ->where('val', 'A')
                ->orderBy('datetime', 'desc')
                ->first(),
            'distance' => BoatData::where('date', $date)
                ->where('mac', $mac)
                ->where('val', 'A')
                ->orderBy('dist', 'desc')
                ->first()->dist,
            'gpsdist' => BoatData::where('date', $date)
                ->where('mac', $mac)
                ->where('val', 'A')
                ->orderBy('dog', 'desc')
                ->first()->dog,
            'speed' => BoatData::where('date', $date)
                ->where('mac', $mac)
                ->where('val', 'A')
                ->orderBy('spd', 'desc')
                ->first(),
            'sog' => BoatData::where('date', $date)
                ->where('mac', $mac)
                ->where('val', 'A')
                ->orderBy('sog', 'desc')
                ->first(),
            'minDepth' => BoatData::where('date', $date)
                ->where('mac', $mac)
                ->where('dep', '>', 0)
                ->where('val', 'A')
                ->orderBy('dep', 'asc')
                ->first(),
            'awind' => BoatData::where('date', $date)
                ->where('mac', $mac)
                ->where('val', 'A')
                ->orderBy('aws', 'desc')
                ->first(),
            'twind' => BoatData::where('date', $date)
                ->where('mac', $mac)
                ->where('val', 'A')
                ->orderBy('tws', 'desc')
                ->first(),
        ];

        $output = new \stdClass();
        $output->points = $data;
        $output->maxes = $maxes;

        return response()->json($output);
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
                'location' => number_format($info->lat, 4, '.', ',') . $info->ns . ', ' . number_format($info->lon, 4, '.', ',') . $info->ew,
                'sog' => ($info->sog == NULL) ? "-" : round($info->sog, 1) . "kts",
                'cog' => ($info->cog == NULL) ? "-" : $info->cog . "&deg",
                'depth' => ($info->dep == NULL) ? "-" : $info->dep . "m",
                'heading' => ($info->hdg == NULL) ? "-" : $info->hdg . "&deg",
                'speed' => ($info->spd == NULL) ? "-" : $info->spd . "kts",
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
    public function fetchSpeed(Request $request)
    {
        $uid = $request->input('uid');
        $start = $request->input('start');
        $timestamp = strtotime(str_replace('/', '-', $start));

        $start = date('Y-m-d', $timestamp);

        $end = $request->input('end');
        $timestamp = strtotime(str_replace('/', '-', $end));

        $end = date('Y-m-d', $timestamp);
        

        // Validate input
        if (!$uid || !$start || !$end) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        // Fetch data from the database
        $mySOG = DB::table('boatdata')
            ->select(DB::raw('AVG(sog) as sog'), 
                    DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
            ->where('mac', $uid)
            ->where('sog', '!=', NULL)
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
	        ->whereBetween('date', [$start, $end])
            ->groupBy('ep_utc')
            ->limit(2000)
            ->get()
            ->map(function ($item) {
                return [round($item->ep_utc), (float) $item->sog];
            });

        $mySPD = DB::table('boatdata')
            ->select(DB::raw('AVG(spd) as spd'), 
                    DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
            ->where('mac', $uid)
            ->where('spd', '!=', NULL)
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->whereBetween('date', [$start, $end])
            ->groupBy('ep_utc')
            ->limit(2000)
            ->get()
            ->map(function ($item) {
                return [round($item->ep_utc), (float) $item->spd];
            });

        $myAWS = DB::table('boatdata')
            ->select(DB::raw('AVG(aws) as aws'), 
                    DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
            ->where('mac', $uid)
            ->where('aws', '!=', NULL)
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->whereBetween('date', [$start, $end])
            ->groupBy('ep_utc')
            ->limit(2000)
            ->get()
            ->map(function ($item) {
                return [round($item->ep_utc), (float) $item->aws];
            });

        $myTWS = DB::table('boatdata')
            ->select(DB::raw('AVG(tws) as tws'), 
                    DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
            ->where('mac', $uid)
            ->where('tws', '!=', NULL)
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->whereBetween('date', [$start, $end])
            ->groupBy('ep_utc')
            ->limit(2000)
            ->get()
            ->map(function ($item) {
                return [round($item->ep_utc), (float) $item->tws];
            });

        $myGWS = DB::table('boatdata')
            ->select(DB::raw('AVG(gws) as gws'), 
                    DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
            ->where('mac', $uid)
            ->where('gws', '!=', NULL)
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->whereBetween('date', [$start, $end])
            ->groupBy('ep_utc')
            ->limit(2000)
            ->get()
            ->map(function ($item) {
                return [round($item->ep_utc), (float) $item->gws];
            });

        $myVMG = DB::table('boatdata')
            ->select(DB::raw('AVG(vmg) as vmg'), 
                    DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
            ->where('mac', $uid)
            ->where('vmg', '!=', NULL)
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->whereBetween('date', [$start, $end])
            ->groupBy('ep_utc')
            ->limit(2000)
            ->get()
            ->map(function ($item) {
                return [round($item->ep_utc), (float) $item->vmg];
            });

        // Prepare data for JSON response
        $data = [
            'mySOG' => $mySOG,
            'mySPD' => $mySPD,
            'myAWS' => $myAWS,
            'myTWS' => $myTWS,
            'myGWS' => $myGWS,
            'myVMG' => $myVMG
        ];

        return response()->json($data);
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
        
        // Validate input
        if (!$uid || !$start || !$end) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }
        
        // Fetch data from the database for myRPM1
       
        $myRPM1 = DB::table('boatdata')
            ->select(DB::raw('AVG(rpm1) as rpm1'), 
                    DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
            ->where('mac', $uid)
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->whereNotNull('rpm1')
            ->whereBetween('date', [$start, $end])
            ->groupBy('ep_utc')
            ->limit(2000)
            ->get()
            ->map(function ($item) {
                return [round($item->ep_utc), (float) $item->rpm1];
            });
        
        // Fetch data from the database for myBOOST1
        $myBOOST1 = DB::table('boatdata')
            ->select(DB::raw('AVG(boost1) as boost1'), 
                     DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
            ->where('mac', $uid)
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->whereNotNull('boost1')
            ->groupBy('ep_utc')
            ->whereBetween('date', [$start, $end])
            ->limit(2000)
            ->get()
            ->map(function ($item) {
                return [round($item->ep_utc), $item->boost1];
            });
         
        $myFUELR1 = DB::table('boatdata')
            ->select(DB::raw('AVG(fuelr1) as fuelr1'), 
                     DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
            ->where('mac', $uid)
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->where('fuelr1', '!=', NULL)
            ->groupBy('ep_utc')
            ->whereBetween('date', [$start, $end])
            ->limit(2000)
            ->get()
            ->map(function ($item) {
                //return [round($item->ep_utc), number_format($item->fuelr1, 2)];
				return [round($item->ep_utc), (float) $item->fuelr1];
            });
            // dd($myFUELR1);
        // Fetch data from the database for myLOAD1
        $myLOAD1 = DB::table('boatdata')
            ->select(DB::raw('AVG(load1) as load1'), 
                    DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
            ->where('mac', $uid)
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->whereNotNull('load1')
            ->groupBy('ep_utc')
            ->whereBetween('date', [$start, $end])
            ->limit(2000)
            ->get()
            ->map(function ($item) {
                return [round($item->ep_utc), number_format($item->load1, 1)];
            });

   
		$mySOG = DB::table('boatdata')
            ->select(DB::raw('AVG(sog) as sog'), 
                    DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
            ->where('mac', $uid)
            ->where('sog', '!=', NULL)
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->whereBetween('date', [$start, $end])
            ->groupBy('ep_utc')
            ->limit(2000)
            ->get()
            ->map(function ($item) {
                return [round($item->ep_utc), (float) $item->sog];
            });

        // Fetch data from the database for myCOOLT1
        $myCOOLT1 = DB::table('boatdata')
            ->select(DB::raw('AVG(coolt1) as coolt1'), 
                     DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
            ->where('mac', $uid)
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
			->whereNotNull('coolt1')
            ->groupBy('ep_utc')
            ->whereBetween('date', [$start, $end])
            ->limit(2000)
            ->get()
            ->map(function ($item) {
                return [round($item->ep_utc), number_format($item->coolt1, 2)];
            });

        // Fetch data from the database for myECON1
        $myECON1 = DB::table('boatdata')
            ->select(DB::raw('AVG(eng1_econ) as eng1_econ'),  DB::raw('(UNIX_TIMESTAMP(CONCAT(date, " ", utc))*1000) as ep_utc'))
            ->where('mac', $uid)
			->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->where('sog', '>', 2)
            ->where('eng1_econ', '>', 0)
            ->groupBy('ep_utc')
            ->whereBetween('date', [$start, $end])
            ->limit(2000)
            ->get()
            ->map(function ($item) {
                //return [round($item->ep_utc), number_format($item->eng1_econ, 2)];
				return [round($item->ep_utc), (float) $item->eng1_econ];
            });
        // Repeat the same process for other variables...
        
        // Prepare data for JSON response
        $data = [
            'myRPM1' => $myRPM1,
            'myBOOST1' => $myBOOST1,
            'myFUELR1' => $myFUELR1,
            'myLOAD1' => $myLOAD1,
            'mySOG' => $mySOG,
            'myCOOLT1' => $myCOOLT1,
            'myECON1' => $myECON1,
            // Add other variables here
        ];
        
        return response()->json($data);
    
    }


    public function deleteBoatData($data_id)
    {
        BoatData::find($data_id)->delete();
        return response()->json(['success' => 'Data deleted successfully.']);
    }
    
}