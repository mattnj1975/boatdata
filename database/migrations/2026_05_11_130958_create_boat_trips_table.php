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
        Schema::create('boat_trips', function (Blueprint $table) {

            $table->id();

            // Boat identifier
            $table->string('mac', 15)->index();

            // Auto-detected trip boundaries
            $table->unsignedInteger('detected_start_boatdata_id')->nullable();
            $table->unsignedInteger('detected_end_boatdata_id')->nullable();

            // Actual/editable trip boundaries
            $table->unsignedInteger('start_boatdata_id');
            $table->unsignedInteger('end_boatdata_id');

            // Detection times
            $table->dateTime('detected_start_time')->nullable();
            $table->dateTime('detected_end_time')->nullable();

            // Actual trip times
            $table->dateTime('start_time')->index();
            $table->dateTime('end_time')->index();

            // Start/end positions
            $table->decimal('start_lat', 12, 8)->nullable();
            $table->decimal('start_lon', 12, 8)->nullable();

            $table->decimal('end_lat', 12, 8)->nullable();
            $table->decimal('end_lon', 12, 8)->nullable();

            // Trip stats
            $table->integer('duration_minutes')->nullable();

            $table->decimal('distance_nm', 8, 3)->nullable();

            $table->decimal('max_sog', 6, 2)->nullable();
            $table->decimal('avg_sog', 6, 2)->nullable();

            $table->decimal('max_spd', 6, 2)->nullable();
            $table->decimal('avg_spd', 6, 2)->nullable();

            // Status
            $table->enum('status', [
                'auto',
                'confirmed',
                'edited',
                'ignored'
            ])->default('auto');

            // Admin notes
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
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
        Schema::dropIfExists('boat_trips');
    }
};