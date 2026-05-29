<?php

namespace App\Console\Commands;

use App\Models\BoatTrip;
use App\Models\TripDetectionConfig;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DetectBoatTrips extends Command
{
    protected $signature = 'trips:detect {--mac=} {--from=} {--to=} {--dry-run}';

    protected $description = 'Detect boat trips from boatdata';

    public function handle()
    {
        $config = TripDetectionConfig::whereNull('mac')
            ->where('enabled', true)
            ->first();

        if (!$config) {
            $this->error('No enabled default trip detection config found.');
            return self::FAILURE;
        }

        $macs = DB::table('boatdata')
            ->select('mac')
            ->when($this->option('mac'), fn ($q) => $q->where('mac', $this->option('mac')))
            ->distinct()
            ->pluck('mac');

        foreach ($macs as $mac) {
            $this->info("Detecting trips for {$mac}");
            $this->detectForMac($mac, $config);
        }

        return self::SUCCESS;
    }

private function detectForMac(string $mac, TripDetectionConfig $config): void
{
    $lastProcessedId = BoatTrip::where('mac', $mac)
        ->max('end_boatdata_id');

    $records = DB::table('boatdata')
            ->select([
                'id',
                'mac',
                'datetime',
                'latdec',
                'londec',
                'sog',
                'spd',
                'dog_nm',
                'rpm1',
                'rpm2',
            ])
            ->where('mac', $mac)
->whereNotNull('datetime')
->whereNotNull('latdec')
->whereNotNull('londec')
->when(
    !$this->option('from') && $lastProcessedId,
    fn ($q) => $q->where('id', '>', $lastProcessedId)
)
            ->when($this->option('from'), fn ($q) => $q->where('datetime', '>=', $this->option('from')))
            ->when($this->option('to'), fn ($q) => $q->where('datetime', '<=', $this->option('to')))
->orderBy('datetime')
->orderBy('id')
->lazy(500);

        $trip = null;
        $movingSince = null;
        $stoppedSince = null;
        $lastRecord = null;

        foreach ($records as $record) {
            $time = Carbon::parse($record->datetime);

            $isMoving =
                ($record->sog !== null && $record->sog >= $config->min_sog) ||
                ($record->spd !== null && $record->spd >= $config->min_spd);

            if ($config->use_engine_rpm) {
                $isMoving = $isMoving ||
                    ($record->rpm1 !== null && $record->rpm1 >= $config->min_rpm) ||
                    ($record->rpm2 !== null && $record->rpm2 >= $config->min_rpm);
            }

            if ($lastRecord) {
                $lastTime = Carbon::parse($lastRecord->datetime);
                $gapMinutes = $lastTime->diffInMinutes($time);

                if ($trip && $gapMinutes > $config->max_gap_minutes) {
                    $this->saveTrip($mac, $trip, $lastRecord);
                    $trip = null;
                    $movingSince = null;
                    $stoppedSince = null;
                }
            }

            if ($isMoving) {
                if (!$movingSince) {
                    $movingSince = $record;
                }

                $movingMinutes = Carbon::parse($movingSince->datetime)->diffInMinutes($time);

                if (!$trip && $movingMinutes >= $config->min_moving_minutes) {
                    $startRecord = $this->findRewindStart($mac, $movingSince, $config);

                    $trip = [
                        'detected_start' => $movingSince,
                        'start' => $startRecord,
                    ];
                }

                $stoppedSince = null;
            } else {
                $movingSince = null;

                if ($trip) {
                    if (!$stoppedSince) {
                        $stoppedSince = $record;
                    }

                    $stoppedMinutes = Carbon::parse($stoppedSince->datetime)->diffInMinutes($time);

                    if ($stoppedMinutes >= $config->min_stopped_minutes) {
                        $endRecord = $this->findExtendedEnd($mac, $stoppedSince, $config);
                        $this->saveTrip($mac, $trip, $endRecord);

                        $trip = null;
                        $stoppedSince = null;
                    }
                }
            }

            $lastRecord = $record;
        }

        if ($trip && $lastRecord) {
            // Don't save trips still in progress
        }
    }

    private function findRewindStart(string $mac, object $movingSince, TripDetectionConfig $config): object
    {
        $from = Carbon::parse($movingSince->datetime)->subMinutes($config->start_rewind_minutes);

        return DB::table('boatdata')
            ->select('id', 'datetime', 'latdec', 'londec')
            ->where('mac', $mac)
            ->where('datetime', '>=', $from)
            ->where('datetime', '<=', $movingSince->datetime)
            ->orderBy('datetime')
            ->orderBy('id')
            ->first() ?: $movingSince;
    }

    private function findExtendedEnd(string $mac, object $stoppedSince, TripDetectionConfig $config): object
    {
        $to = Carbon::parse($stoppedSince->datetime)->addMinutes($config->end_extend_minutes);

        return DB::table('boatdata')
            ->select('id', 'datetime', 'latdec', 'londec')
            ->where('mac', $mac)
            ->where('datetime', '>=', $stoppedSince->datetime)
            ->where('datetime', '<=', $to)
            ->orderByDesc('datetime')
            ->orderByDesc('id')
            ->first() ?: $stoppedSince;
    }

    private function saveTrip(string $mac, array $trip, object $endRecord): void
    {
        $start = $trip['start'];
        $detectedStart = $trip['detected_start'];

		if (Carbon::parse($endRecord->datetime)->lessThanOrEqualTo(Carbon::parse($start->datetime))) {
    $this->warn("Skipped bad trip: {$mac} {$start->datetime} to {$endRecord->datetime}");
    return;
}

        $exists = BoatTrip::where('mac', $mac)
            ->where('detected_start_boatdata_id', $detectedStart->id)
            ->exists();

        if ($exists) {
            return;
        }

        $stats = DB::table('boatdata')
            ->where('mac', $mac)
            ->whereBetween('id', [$start->id, $endRecord->id])
            ->selectRaw('
                TIMESTAMPDIFF(MINUTE, MIN(datetime), MAX(datetime)) as duration_minutes,
                MAX(sog) as max_sog,
                AVG(NULLIF(sog, 0)) as avg_sog,
                MAX(spd) as max_spd,
                AVG(NULLIF(spd, 0)) as avg_spd,
                MAX(dog_nm) - MIN(dog_nm) as distance_nm
            ')
            ->first();

        if ($this->option('dry-run')) {
            $this->info("Trip: {$mac} {$start->datetime} to {$endRecord->datetime}");
            return;
        }

        BoatTrip::create([
            'mac' => $mac,

            'detected_start_boatdata_id' => $detectedStart->id,
            'detected_end_boatdata_id' => $endRecord->id,

            'start_boatdata_id' => $start->id,
            'end_boatdata_id' => $endRecord->id,

            'detected_start_time' => $detectedStart->datetime,
            'detected_end_time' => $endRecord->datetime,

            'start_time' => $start->datetime,
            'end_time' => $endRecord->datetime,

            'start_lat' => $start->latdec,
            'start_lon' => $start->londec,
            'end_lat' => $endRecord->latdec,
            'end_lon' => $endRecord->londec,

            'duration_minutes' => $stats->duration_minutes,
            'distance_nm' => $stats->distance_nm,
            'max_sog' => $stats->max_sog,
            'avg_sog' => $stats->avg_sog,
            'max_spd' => $stats->max_spd,
            'avg_spd' => $stats->avg_spd,

            'status' => 'auto',
        ]);

        $this->info("Saved trip: {$mac} {$start->datetime} to {$endRecord->datetime}");
    }
}