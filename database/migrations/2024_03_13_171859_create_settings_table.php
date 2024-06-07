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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->integer('device_id')->nullable();
            $table->string('mac', 12)->nullable();
            $table->text('serial')->nullable()->comment('Tamper Seal Barcode');
            $table->integer('rev')->nullable();
            $table->tinyInteger('public')->default(1);
            $table->tinyInteger('plan')->default(0);
            $table->string('boatname')->default('WebBoatName');
            $table->integer('default_interval')->default(120000);
            $table->tinyInteger('gsm_enabled')->default(0)->comment('SIM Enabled');
            $table->tinyInteger('upload_enabled')->default(1);
            $table->tinyInteger('upload_underway')->default(0);
            $table->integer('default_upload_interval')->default(120000);
            $table->integer('default_upload_timeout')->default(60000);
            $table->integer('sog_average')->default(5);
            $table->integer('portal_timeout')->default(240);
            $table->tinyInteger('nmea_in1_enable')->default(0);
            $table->integer('nmea_in1_baud')->default(38400);
            $table->tinyInteger('nmea_in2_enable')->default(0);
            $table->integer('nmea_in2_baud')->default(38400);
            $table->tinyInteger('debug')->default(0);
            $table->tinyInteger('nmea_debug')->default(0);
            $table->tinyInteger('telnet_debug')->default(0);
            $table->tinyInteger('enable_n2k')->default(0);
            $table->tinyInteger('nmea2k_debug')->default(0);
            $table->tinyInteger('telnet_nmea2k_debug')->default(0);
            $table->tinyInteger('http_debug')->default(0);
            $table->integer('gsm_debug')->default(0);
            $table->integer('mpu_debug')->default(0);
            $table->tinyInteger('buzz_on')->default(1);
            $table->tinyInteger('fix_req')->default(0);
            $table->tinyInteger('int_gps')->default(0);
            $table->tinyInteger('gps_filter')->default(1);
            $table->integer('gps_filter_hdop')->default(5);
            $table->integer('gps_filter_sats')->default(4);
            $table->tinyInteger('telnet_client_enable')->default(0);
            $table->string('telnet_client')->default('10.1.1.1');
            $table->integer('telnet_client_port')->default(20220);
            $table->integer('telnet_server_port')->default(23);
            $table->string('upload_server')->default('boatdata.co.uk/app');
            $table->smallInteger('upload_port')->default(80);
            $table->string('upload_path')->default('upload_data.php');
            $table->string('api_key')->default('api_key=tPmAT5Ab3j7F9,');
            $table->char('settings_server', 120)->default('boatdata.co.uk/app');
            $table->string('settings_api_key')->default('1');
            $table->string('apn')->nullable()->comment('APN');
            $table->string('gprsuser')->nullable()->comment('GPRS Username');
            $table->string('gprspass')->nullable()->comment('GPRS Password');
            $table->tinyInteger('fast_gps')->default(1);
            $table->tinyInteger('test_data')->default(0);
            $table->tinyInteger('reset_wifi')->default(0);
            $table->tinyInteger('power_save')->default(0)->comment('Power Save Mode');
            $table->tinyInteger('beep_log')->default(0);
            $table->integer('update_to')->default(0);
            $table->dateTime('lastseen')->default(now());
            $table->integer('version')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
