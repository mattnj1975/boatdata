<?php

namespace App\Http\Controllers;
use App\Models\Settings;
use Carbon\Carbon;
use App\Models\calendar;
use App\Models\BoatData;
use App\Models\AdminBoats;
use App\Models\UserBoats;
use App\Models\User;
use App\Services\FleetStatusService;
use Illuminate\Http\Request;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function searchPage(Request $request)
    {
        $Settings = '';
        if ($request->id) {
            $user = User::where('email', $request->id)->first();
            if (!empty($user) && $user->role_as == 3) {
                $Settings = BoatData::select(
                    'settings.boatname',
                    'boatdata.mac',
                    'boatdata.date as TripDate',
                    \DB::raw('min(boatdata.utc) as Begin'),
                    \DB::raw('max(boatdata.utc) as Finish'),
                    \DB::raw('MIN(NULLIF(ROUND(boatdata.tdist,1), -1)) as Start'),
                    \DB::raw('MAX(NULLIF(ROUND(boatdata.tdist,1), -1)) as End'),
                    \DB::raw('NULLIF(MAX(ROUND(dog_nm,1)),0) as Trip')
                )
                ->leftJoin('settings', 'settings.mac', '=', 'boatdata.mac')
                ->leftJoin('user_boats', 'user_boats.boat_id', '=', 'settings.id')
                ->leftJoin('users', 'users.id', '=', 'user_boats.user_id')
                ->where('users.email', $request->id)
                ->where('val', 'A')
                ->where('utc', '!=' , '00:00:00')
                ->whereTime('utc', '<=', '24:00:00')
                ->where('settings.public', 1)
                ->whereNotNull('user_boats.id')
                ->groupBy('settings.boatname', 'boatdata.mac', 'boatdata.date')
                ->orderBy('boatdata.date', 'desc')
                ->get();
                return view('filter_home', compact('Settings'));
            } elseif (!empty($user) && $user->role_as == 2) {
                $Settings = BoatData::select(
                    'settings.boatname',
                    'boatdata.mac',
                    'boatdata.date as TripDate',
                    \DB::raw('min(boatdata.utc) as Begin'),
                    \DB::raw('max(boatdata.utc) as Finish'),
                    \DB::raw('MIN(NULLIF(ROUND(boatdata.tdist,1), -1)) as Start'),
                    \DB::raw('MAX(NULLIF(ROUND(boatdata.tdist,1), -1)) as End'),
                    \DB::raw('NULLIF(MAX(ROUND(dog_nm,1)),0) as Trip')
                )
                ->leftJoin('settings', 'settings.mac', '=', 'boatdata.mac')
                ->leftJoin('admin_boats', 'admin_boats.boat_id', '=', 'settings.id')
                ->leftJoin('users', 'users.id', '=', 'admin_boats.user_id')
                ->where('users.email', $request->id)
                ->where('val', 'A')
                ->where('utc', '!=' , '00:00:00')
                ->whereTime('utc', '<=', '24:00:00')
                ->where('settings.public', 1)
                ->whereNotNull('admin_boats.id')
                ->groupBy('settings.boatname', 'boatdata.mac', 'boatdata.date')
                ->orderBy('boatdata.date', 'desc')
                ->get();
                return view('filter_home', compact('Settings'));
            } else {
                return view('filter_home', compact('Settings'));
            }
        } elseif ($request->mac) {
            $Settings = BoatData::select(
                'settings.boatname',
                'boatdata.mac',
                'boatdata.date as TripDate',
                \DB::raw('min(boatdata.utc) as Begin'),
                \DB::raw('max(boatdata.utc) as Finish'),
                \DB::raw('MIN(NULLIF(ROUND(boatdata.tdist,1), -1)) as Start'),
                \DB::raw('MAX(NULLIF(ROUND(boatdata.tdist,1), -1)) as End'),
                \DB::raw('NULLIF(MAX(ROUND(dog_nm,1)),0) as Trip')
            )
            ->leftJoin('settings', 'settings.mac', '=', 'boatdata.mac')
            ->where('val', 'A')
            ->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->where('boatdata.mac', $request->mac) // Filter by the specific MAC address
            ->where('settings.public', 1) // Ensure settings are public
            ->groupBy('settings.boatname', 'boatdata.mac', 'boatdata.date')
            ->orderBy('boatdata.date', 'desc') // Order by date instead of using latest()
            ->get();
            return view('filter_home', compact('Settings'));
        } else {
            $fleetStatus = app(\App\Services\FleetStatusService::class)->getPublicFleetStats();

return view('home', compact('fleetStatus'));
        }
         
    }

    public function searchMac(Request $request)
    {
        $searchTerm = $request->mac;
        if (strpos($searchTerm, '@') == false) {
            $Settings = BoatData::select(
                'settings.boatname',
                'boatdata.mac',
                'boatdata.date as TripDate',
                \DB::raw('min(boatdata.utc) as Begin'),
                \DB::raw('max(boatdata.utc) as Finish'),
                \DB::raw('MIN(NULLIF(ROUND(boatdata.tdist,1), -1)) as Start'),
                \DB::raw('MAX(NULLIF(ROUND(boatdata.tdist,1), -1)) as End'),
                \DB::raw('NULLIF(MAX(ROUND(dog_nm,1)),0) as Trip')
            )
            ->leftJoin('settings', 'settings.mac', '=', 'boatdata.mac')
            ->where('val', 'A')
            ->where('utc', '!=' , '00:00:00')
			->whereTime('utc', '<=', '24:00:00')
            ->where('boatdata.mac', $request->mac) // Filter by the specific MAC address
            ->where('settings.public', 1) // Ensure settings are public
            ->groupBy('settings.boatname', 'boatdata.mac', 'boatdata.date')
            ->orderBy('boatdata.date', 'desc') // Order by date instead of using latest()
            ->get();
        } else {
            $user = User::where('email', $request->mac)->first();
            if ($user->role_as == 3) {
                $Settings = BoatData::select(
                    'settings.boatname',
                    'boatdata.mac',
                    'boatdata.date as TripDate',
                    \DB::raw('min(boatdata.utc) as Begin'),
                    \DB::raw('max(boatdata.utc) as Finish'),
                    \DB::raw('MIN(NULLIF(ROUND(boatdata.tdist,1), -1)) as Start'),
                    \DB::raw('MAX(NULLIF(ROUND(boatdata.tdist,1), -1)) as End'),
                    \DB::raw('NULLIF(MAX(ROUND(dog_nm,1)),0) as Trip')
                )
                ->leftJoin('settings', 'settings.mac', '=', 'boatdata.mac')
                ->leftJoin('user_boats', 'user_boats.boat_id', '=', 'settings.id')
                ->leftJoin('users', 'users.id', '=', 'user_boats.user_id')
                ->where('users.email', $request->mac)
                ->where('val', 'A')
                ->where('utc', '!=' , '00:00:00')
			    ->whereTime('utc', '<=', '24:00:00')
                ->where('settings.public', 1)
                ->whereNotNull('user_boats.id')
                ->groupBy('settings.boatname', 'boatdata.mac', 'boatdata.date')
                ->orderBy('boatdata.date', 'desc')
                ->get();
            } elseif ($user->role_as == 2) {
                $Settings = BoatData::select(
                    'settings.boatname',
                    'boatdata.mac',
                    'boatdata.date as TripDate',
                    \DB::raw('min(boatdata.utc) as Begin'),
                    \DB::raw('max(boatdata.utc) as Finish'),
                    \DB::raw('MIN(NULLIF(ROUND(boatdata.tdist,1), -1)) as Start'),
                    \DB::raw('MAX(NULLIF(ROUND(boatdata.tdist,1), -1)) as End'),
                    \DB::raw('NULLIF(MAX(ROUND(dog_nm,1)),0) as Trip')
                )
                ->leftJoin('settings', 'settings.mac', '=', 'boatdata.mac')
                ->leftJoin('admin_boats', 'admin_boats.boat_id', '=', 'settings.id')
                ->leftJoin('users', 'users.id', '=', 'admin_boats.user_id')
                ->where('users.email', $request->mac)
                ->where('val', 'A')
                ->where('utc', '!=' , '00:00:00')
			    ->whereTime('utc', '<=', '24:00:00')
                ->where('settings.public', 1)
                ->whereNotNull('admin_boats.id')
                ->groupBy('settings.boatname', 'boatdata.mac', 'boatdata.date')
                ->orderBy('boatdata.date', 'desc')
                ->get();
            } else {

            }
            
        }
        

        return response()->json($Settings);
    }
}
