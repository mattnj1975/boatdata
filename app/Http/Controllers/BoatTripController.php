<?php

namespace App\Http\Controllers;

use App\Models\BoatTrip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoatTripController extends Controller
{
    public function index(Request $request)
    {
        $trips = BoatTrip::query()
            ->when($request->mac, fn ($q) => $q->where('mac', $request->mac))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('start_time')
            ->paginate(50)
            ->withQueryString();

        $macs = BoatTrip::query()
            ->select('mac')
            ->distinct()
            ->orderBy('mac')
            ->pluck('mac');

        return view('trips.index', compact('trips', 'macs'));
    }

public function show(BoatTrip $trip)
{
    $startCentre = \Carbon\Carbon::parse($trip->start_time);
    $endCentre = \Carbon\Carbon::parse($trip->end_time);

    $startPoints = $this->boundaryPoints($trip->mac, $startCentre);
    $endPoints = $this->boundaryPoints($trip->mac, $endCentre);

    $startMapPoints = DB::table('boatdata')
        ->select('id', 'datetime', 'latdec', 'londec', 'sog', 'spd', 'cog')
        ->where('mac', $trip->mac)
        ->whereBetween('datetime', [
            $startCentre->copy()->subMinutes(5),
            $startCentre->copy()->addMinutes(5),
        ])
        ->whereNotNull('latdec')
        ->whereNotNull('londec')
        ->orderBy('datetime')
        ->get();

    $endMapPoints = DB::table('boatdata')
        ->select('id', 'datetime', 'latdec', 'londec', 'sog', 'spd', 'cog')
        ->where('mac', $trip->mac)
        ->whereBetween('datetime', [
            $endCentre->copy()->subMinutes(5),
            $endCentre->copy()->addMinutes(5),
        ])
        ->whereNotNull('latdec')
        ->whereNotNull('londec')
        ->orderBy('datetime')
        ->get();

    return view('trips.show', compact(
        'trip',
        'startPoints',
        'endPoints',
        'startMapPoints',
        'endMapPoints'
    ));
}
public function moveStart(Request $request, BoatTrip $trip)
{
    $minutes = (int) $request->input('minutes');

    $targetTime = \Carbon\Carbon::parse($trip->start_time)->addMinutes($minutes);

    $record = DB::table('boatdata')
        ->where('mac', $trip->mac)
        ->where('datetime', '<=', $targetTime)
        ->orderByDesc('datetime')
        ->first();

    if (!$record || $record->id >= $trip->end_boatdata_id) {
        return back()->withErrors('Could not move start point.');
    }

    $this->updateTripBoundary($trip, $record->id, $trip->end_boatdata_id);

    return back()->with('success', 'Trip start moved.');
}

public function moveEnd(Request $request, BoatTrip $trip)
{
    $minutes = (int) $request->input('minutes');

    $targetTime = \Carbon\Carbon::parse($trip->end_time)->addMinutes($minutes);

    $record = DB::table('boatdata')
        ->where('mac', $trip->mac)
        ->where('datetime', '>=', $targetTime)
        ->orderBy('datetime')
        ->first();

    if (!$record || $record->id <= $trip->start_boatdata_id) {
        return back()->withErrors('Could not move end point.');
    }

    $this->updateTripBoundary($trip, $trip->start_boatdata_id, $record->id);

    return back()->with('success', 'Trip end moved.');
}

public function boundaryData(Request $request, BoatTrip $trip)
{
    $type = $request->input('type');

    if (!in_array($type, ['start', 'end'])) {
        return response()->json(['error' => 'Invalid boundary type'], 422);
    }

    $currentId = (int) $request->input('record_id');

    $centre = DB::table('boatdata')
        ->where('mac', $trip->mac)
        ->where('id', $currentId)
        ->first();

    if (!$centre) {
        return response()->json(['error' => 'Record not found'], 404);
    }

    $before = DB::table('boatdata')
        ->select('id', 'datetime', 'latdec', 'londec', 'sog', 'spd', 'cog')
        ->where('mac', $trip->mac)
        ->where('datetime', '<', $centre->datetime)
        ->orderByDesc('datetime')
        ->limit(12)
        ->get()
        ->reverse()
        ->values();

    $after = DB::table('boatdata')
        ->select('id', 'datetime', 'latdec', 'londec', 'sog', 'spd', 'cog')
        ->where('mac', $trip->mac)
        ->where('datetime', '>', $centre->datetime)
        ->orderBy('datetime')
        ->limit(12)
        ->get()
        ->values();

    $centreRow = collect([[
        'id' => $centre->id,
        'datetime' => $centre->datetime,
        'latdec' => $centre->latdec,
        'londec' => $centre->londec,
        'sog' => $centre->sog,
        'spd' => $centre->spd,
        'cog' => $centre->cog,
    ]]);

    $tableRows = $before
        ->concat($centreRow)
        ->concat($after)
        ->values();

    $mapStartTime = \Carbon\Carbon::parse($centre->datetime)->subMinutes(6);
    $mapEndTime = \Carbon\Carbon::parse($centre->datetime)->addMinutes(6);

    $mapPoints = DB::table('boatdata')
        ->select('id', 'datetime', 'latdec', 'londec', 'sog', 'spd', 'cog')
        ->where('mac', $trip->mac)
        ->whereBetween('datetime', [$mapStartTime, $mapEndTime])
        ->whereNotNull('latdec')
        ->whereNotNull('londec')
        ->orderBy('datetime')
        ->get();

    return response()->json([
        'centre' => [
            'id' => $centre->id,
            'datetime' => $centre->datetime,
            'lat' => $centre->latdec,
            'lon' => $centre->londec,
        ],
        'table' => $tableRows,
        'map' => $mapPoints,
    ]);
}

public function saveBoundary(Request $request, BoatTrip $trip)
{
    $data = $request->validate([
        'type' => ['required', 'in:start,end'],
        'record_id' => ['required', 'integer'],
    ]);

    if ($data['type'] === 'start') {
        if ($data['record_id'] >= $trip->end_boatdata_id) {
            return response()->json(['error' => 'Start must be before end'], 422);
        }

        $this->updateTripBoundary($trip, $data['record_id'], $trip->end_boatdata_id);
    }

    if ($data['type'] === 'end') {
        if ($data['record_id'] <= $trip->start_boatdata_id) {
            return response()->json(['error' => 'End must be after start'], 422);
        }

        $this->updateTripBoundary($trip, $trip->start_boatdata_id, $data['record_id']);
    }

    return response()->json([
        'success' => true,
        'message' => 'Trip boundary saved',
    ]);
}

public function nudgeRecord(Request $request, BoatTrip $trip)
{
    $data = $request->validate([
        'record_id' => ['required', 'integer'],
        'minutes' => ['required', 'integer'],
    ]);

    $current = DB::table('boatdata')
        ->where('mac', $trip->mac)
        ->where('id', $data['record_id'])
        ->first();

    if (!$current) {
        return response()->json(['error' => 'Current record not found'], 404);
    }

    $targetTime = \Carbon\Carbon::parse($current->datetime)->addMinutes($data['minutes']);

    if ($data['minutes'] < 0) {
        $record = DB::table('boatdata')
            ->where('mac', $trip->mac)
            ->where('datetime', '<=', $targetTime)
            ->orderByDesc('datetime')
            ->first();
    } else {
        $record = DB::table('boatdata')
            ->where('mac', $trip->mac)
            ->where('datetime', '>=', $targetTime)
            ->orderBy('datetime')
            ->first();
    }

    if (!$record) {
        return response()->json(['error' => 'No more records in that direction'], 422);
    }

    return response()->json([
        'record_id' => $record->id,
    ]);
}

private function updateTripBoundary(BoatTrip $trip, int $startId, int $endId): void
{
    $start = DB::table('boatdata')->where('id', $startId)->first();
    $end = DB::table('boatdata')->where('id', $endId)->first();
    $stats = $this->calculateStats($trip->mac, $startId, $endId);

    $trip->update([
        'start_boatdata_id' => $start->id,
        'end_boatdata_id' => $end->id,
        'start_time' => $start->datetime,
        'end_time' => $end->datetime,
        'start_lat' => $start->latdec,
        'start_lon' => $start->londec,
        'end_lat' => $end->latdec,
        'end_lon' => $end->londec,
        'duration_minutes' => $stats->duration_minutes,
        'distance_nm' => $stats->distance_nm,
        'max_sog' => $stats->max_sog,
        'avg_sog' => $stats->avg_sog,
        'max_spd' => $stats->max_spd,
        'avg_spd' => $stats->avg_spd,
        'status' => 'edited',
    ]);
}


private function boundaryPoints(string $mac, \Carbon\Carbon $centre)
{
    return DB::table('boatdata')
        ->select('id', 'datetime', 'sog', 'spd', 'cog', 'latdec', 'londec')
        ->where('mac', $mac)
        ->whereBetween('datetime', [
            $centre->copy()->subMinutes(5),
            $centre->copy()->addMinutes(5),
        ])
        ->whereRaw('MOD(UNIX_TIMESTAMP(datetime), 30) < 5')
        ->orderBy('datetime')
        ->get();
}

    public function edit(BoatTrip $trip)
    {
        return view('trips.edit', compact('trip'));
    }

    public function update(Request $request, BoatTrip $trip)
    {
        $data = $request->validate([
            'start_boatdata_id' => ['required', 'integer'],
            'end_boatdata_id' => ['required', 'integer', 'gt:start_boatdata_id'],
            'status' => ['required', 'in:auto,confirmed,edited,ignored'],
            'notes' => ['nullable', 'string'],
        ]);

        $start = DB::table('boatdata')->where('id', $data['start_boatdata_id'])->first();
        $end = DB::table('boatdata')->where('id', $data['end_boatdata_id'])->first();

        if (!$start || !$end) {
            return back()->withErrors('Start or end boatdata ID not found.');
        }

        $stats = $this->calculateStats($trip->mac, $start->id, $end->id);

        $trip->update([
            'start_boatdata_id' => $start->id,
            'end_boatdata_id' => $end->id,
            'start_time' => $start->datetime,
            'end_time' => $end->datetime,
            'start_lat' => $start->latdec,
            'start_lon' => $start->londec,
            'end_lat' => $end->latdec,
            'end_lon' => $end->londec,
            'duration_minutes' => $stats->duration_minutes,
            'distance_nm' => $stats->distance_nm,
            'max_sog' => $stats->max_sog,
            'avg_sog' => $stats->avg_sog,
            'max_spd' => $stats->max_spd,
            'avg_spd' => $stats->avg_spd,
            'status' => $data['status'] === 'auto' ? 'edited' : $data['status'],
            'notes' => $data['notes'],
        ]);

        return redirect()->route('trips.show', $trip)->with('success', 'Trip updated.');
    }

    public function confirm(BoatTrip $trip)
    {
        $trip->update(['status' => 'confirmed']);

        return back()->with('success', 'Trip confirmed.');
    }

    public function ignore(BoatTrip $trip)
    {
        $trip->update(['status' => 'ignored']);

        return back()->with('success', 'Trip ignored.');
    }

    public function mergeNext(BoatTrip $trip)
    {
        $next = BoatTrip::where('mac', $trip->mac)
            ->where('start_time', '>', $trip->start_time)
            ->orderBy('start_time')
            ->first();

        if (!$next) {
            return back()->withErrors('No next trip found to merge.');
        }

        $stats = $this->calculateStats($trip->mac, $trip->start_boatdata_id, $next->end_boatdata_id);

        $trip->update([
            'end_boatdata_id' => $next->end_boatdata_id,
            'end_time' => $next->end_time,
            'end_lat' => $next->end_lat,
            'end_lon' => $next->end_lon,
            'duration_minutes' => $stats->duration_minutes,
            'distance_nm' => $stats->distance_nm,
            'max_sog' => $stats->max_sog,
            'avg_sog' => $stats->avg_sog,
            'max_spd' => $stats->max_spd,
            'avg_spd' => $stats->avg_spd,
            'status' => 'edited',
            'notes' => trim(($trip->notes ?? '') . "\nMerged with trip ID {$next->id}"),
        ]);

        $next->delete();

        return redirect()->route('trips.show', $trip)->with('success', 'Trips merged.');
    }

    public function destroy(BoatTrip $trip)
    {
        $trip->delete();

        return redirect()->route('trips.index')->with('success', 'Trip deleted.');
    }

    private function calculateStats(string $mac, int $startId, int $endId)
    {
        return DB::table('boatdata')
            ->where('mac', $mac)
            ->whereBetween('id', [$startId, $endId])
            ->selectRaw('
                TIMESTAMPDIFF(MINUTE, MIN(datetime), MAX(datetime)) as duration_minutes,
                MAX(dog_nm) - MIN(dog_nm) as distance_nm,
                MAX(sog) as max_sog,
                AVG(NULLIF(sog, 0)) as avg_sog,
                MAX(spd) as max_spd,
                AVG(NULLIF(spd, 0)) as avg_spd
            ')
            ->first();
    }
}