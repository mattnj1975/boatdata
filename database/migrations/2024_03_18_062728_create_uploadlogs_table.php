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
        Schema::create('uploadlog', function (Blueprint $table) {
            $table->id();
            $table->integer('upload_id')->nullable();
            $table->string('device_id', 12)->nullable();
            $table->dateTime('uload_time')->default(now());
            $table->tinyInteger('upload_status')->nullable();
            $table->integer('connection_status')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('sd_space')->nullable();
            $table->integer('sd_used')->nullable();
            $table->integer('db_ok')->nullable();
            $table->integer('db_err')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploadlogs');
    }
};
