<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    if (Schema::hasTable('boat_trips')) {
        return;
    }

    Schema::create('boat_trips', function (Blueprint $table) {
        $table->id();
        $table->string('mac', 15);
        $table->unsignedInteger('detected_start_boatdata_id')->nullable();
        $table->unsignedInteger('detected_end_boatdata_id')->nullable();
        $table->unsignedInteger('start_boatdata_id');
        $table->unsignedInteger('end_boatdata_id');
        $table->dateTime('detected_start_time')->nullable();
        $table->dateTime('detected_end_time')->nullable();
        $table->dateTime('start_time');
        $table->dateTime('end_time');
        $table->decimal('start_lat', 12, 8)->nullable();
        $table->decimal('start_lon', 12, 8)->nullable();
        $table->decimal('end_lat', 12, 8)->nullable();
        $table->decimal('end_lon', 12, 8)->nullable();
        $table->integer('duration_minutes')->nullable();
        $table->decimal('distance_nm', 8, 3)->nullable();
        $table->decimal('max_sog', 6, 2)->nullable();
        $table->decimal('avg_sog', 6, 2)->nullable();
        $table->decimal('max_spd', 6, 2)->nullable();
        $table->decimal('avg_spd', 6, 2)->nullable();
        $table->enum('status', ['auto', 'confirmed', 'edited', 'ignored'])->default('auto');
        $table->text('notes')->nullable();
        $table->timestamps();

        $table->index('mac');
        $table->index('start_time');
        $table->index('end_time');
        $table->index(['mac', 'start_time']);
        $table->index(['mac', 'end_time']);
        $table->index(['start_boatdata_id', 'end_boatdata_id']);
    });
}

    public function down(): void
    {
        Schema::dropIfExists('boat_trips');
    }
};