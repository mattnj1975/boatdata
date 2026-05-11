<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('boat_trips', function (Blueprint $table) {

            // Boat identifier
            $table->string('mac', 15)->after('id')->index();

            // Auto-detected boundaries
            $table->unsignedInteger('detected_start_boatdata_id')
                ->nullable()
                ->after('mac');

            $table->unsignedInteger('detected_end_boatdata_id')
                ->nullable()
                ->after('detected_start_boatdata_id');

            // Editable trip boundaries
            $table->unsignedInteger('start_boatdata_id')
                ->after('detected_end_boatdata_id');

            $table->unsignedInteger('end_boatdata_id')
                ->after('start_boatdata_id');

            // Detection times
            $table->dateTime('detected_start_time')
                ->nullable()
                ->after('end_boatdata_id');

            $table->dateTime('detected_end_time')
                ->nullable()
                ->after('detected_start_time');

            // Actual trip times
            $table->dateTime('start_time')
                ->after('detected_end_time')
                ->index();

            $table->dateTime('end_time')
                ->after('start_time')
                ->index();

            // Start/end positions
            $table->decimal('start_lat', 12, 8)
                ->nullable()
                ->after('end_time');

            $table->decimal('start_lon', 12, 8)
                ->nullable()
                ->after('start_lat');

            $table->decimal('end_lat', 12, 8)
                ->nullable()
                ->after('start_lon');

            $table->decimal('end_lon', 12, 8)
                ->nullable()
                ->after('end_lat');

            // Stats
            $table->integer('duration_minutes')
                ->nullable()
                ->after('end_lon');

            $table->decimal('distance_nm', 8, 3)
                ->nullable()
                ->after('duration_minutes');

            $table->decimal('max_sog', 6, 2)
                ->nullable()
                ->after('distance_nm');

            $table->decimal('avg_sog', 6, 2)
                ->nullable()
                ->after('max_sog');

            $table->decimal('max_spd', 6, 2)
                ->nullable()
                ->after('avg_sog');

            $table->decimal('avg_spd', 6, 2)
                ->nullable()
                ->after('max_spd');

            // Status
            $table->enum('status', [
                'auto',
                'confirmed',
                'edited',
                'ignored'
            ])
                ->default('auto')
                ->after('avg_spd');

            // Notes
            $table->text('notes')
                ->nullable()
                ->after('status');

            // Extra indexes
            $table->index(['mac', 'start_time']);
            $table->index(['mac', 'end_time']);

            $table->index([
                'start_boatdata_id',
                'end_boatdata_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boat_trips', function (Blueprint $table) {

            $table->dropIndex(['mac', 'start_time']);
            $table->dropIndex(['mac', 'end_time']);
            $table->dropIndex([
                'start_boatdata_id',
                'end_boatdata_id'
            ]);

            $table->dropColumn([
                'mac',
                'detected_start_boatdata_id',
                'detected_end_boatdata_id',
                'start_boatdata_id',
                'end_boatdata_id',
                'detected_start_time',
                'detected_end_time',
                'start_time',
                'end_time',
                'start_lat',
                'start_lon',
                'end_lat',
                'end_lon',
                'duration_minutes',
                'distance_nm',
                'max_sog',
                'avg_sog',
                'max_spd',
                'avg_spd',
                'status',
                'notes',
            ]);
        });
    }
};