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
        Schema::create('boatdata', function (Blueprint $table) {
            $table->id();
            $table->string('mac', 15)->nullable()->comment('Node Mac Address');
            $table->text('val')->nullable()->comment('GPS Fix Valid');
            $table->decimal('lat', 20, 7)->nullable()->comment('Latitude');
            $table->char('ns', 1)->nullable();
            $table->decimal('lon', 20, 7)->nullable()->comment('Logitude');
            $table->char('ew', 1)->nullable();
            $table->decimal('sog', 20, 6)->nullable()->comment('Speed over ground');
            $table->smallInteger('cog')->nullable()->comment('Course over ground');
            $table->decimal('dog', 20, 6)->nullable()->default(-1)->comment('Distance Over Ground (meters)');
            $table->decimal('altitude', 20, 6)->nullable()->default(-1)->comment('Altitude Above Sea');
            $table->time('utc')->nullable()->comment('UTC Time');
            $table->date('date')->nullable()->comment('GPS Date');
            $table->decimal('aws', 20, 6)->nullable()->comment('Apparent Wind Speed');
            $table->smallInteger('awa')->nullable()->comment('Apparent Wind Dir');
            $table->decimal('dep', 20, 6)->nullable()->comment('Depth');
            $table->smallInteger('hdg')->nullable()->comment('Heading');
            $table->decimal('spd', 20, 6)->nullable()->comment('Boat Speed');
            $table->decimal('mtw', 20, 6)->nullable()->comment('Water Temp');
            $table->decimal('dist', 20, 6)->nullable()->comment('Distance Travelled');
            $table->decimal('tdist', 20, 6)->nullable()->comment('Total Distance');
            $table->decimal('wprange', 20, 6)->nullable()->default(-1)->comment('Range to WP');
            $table->decimal('wpbearing', 20, 6)->nullable()->default(-1)->comment('Bearing to WP');
            $table->decimal('wpvelocity', 20, 6)->nullable()->default(-1)->comment('Speed to WP');
            $table->decimal('wpxte', 20, 6)->nullable()->default(-1)->comment('Cross Track Error');
            $table->integer('wpid')->nullable();
            $table->decimal('wplat', 20, 6)->nullable();
            $table->decimal('wplon', 20, 6)->nullable();
            $table->smallInteger('rpm1')->nullable()->comment('Engine1 RPM');
            $table->smallInteger('boost1')->nullable()->default(-1)->comment('Engine1 Boost');
            $table->smallInteger('tilt1')->nullable()->default(-1)->comment('Engine1 Tilt');
            $table->smallInteger('oilp1')->nullable()->default(-1)->comment('Engine1 Oil Pressure');
            $table->smallInteger('oilt1')->nullable()->default(-1)->comment('Engine1 Oil Temp');
            $table->smallInteger('coolt1')->nullable()->default(-1)->comment('Engine1 Coolant Temp');
            $table->smallInteger('altv1')->nullable()->default(-1)->comment('Engine1 Alt Voltage');
            $table->decimal('fuelr1', 20, 6)->nullable()->default(-1)->comment('Engine1 Fuel Rate');
            $table->smallInteger('coolp1')->nullable()->default(-1)->comment('Engine1 Cool Press');
            $table->smallInteger('load1')->nullable()->default(-1)->comment('Engine1 Load');
            $table->smallInteger('torq1')->nullable()->default(-1)->comment('Engine1 Torq');
            $table->smallInteger('rpm2')->nullable()->default(-1)->comment('Engine2 RPM');
            $table->smallInteger('boost2')->nullable()->default(-1)->comment('Engine2 Boost');
            $table->smallInteger('tilt2')->nullable()->default(-1)->comment('Engine2 Tilt');
            $table->smallInteger('oilp2')->nullable()->default(-1)->comment('Engine2 Oil Pressure');
            $table->smallInteger('oilt2')->nullable()->default(-1)->comment('Engine2 Oil Temp');
            $table->smallInteger('coolt2')->nullable()->default(-1)->comment('Engine2 Coolant Temp');
            $table->smallInteger('altv2')->nullable()->default(-1)->comment('Engine2 Alt Voltage');
            $table->decimal('fuelr2', 20, 6)->nullable()->default(-1)->comment('Engine2 Fuel Rate');
            $table->smallInteger('coolp2')->nullable()->default(-1)->comment('Engine2 Cool Press');
            $table->smallInteger('load2')->nullable()->default(-1)->comment('Engine2 Load');
            $table->smallInteger('torq2')->nullable()->default(-1)->comment('Engine2 Torq');
            $table->integer('rudder')->nullable()->default(-1)->comment('Rudder Angle');
            $table->decimal('pitch', 20, 6)->nullable()->comment('Pitch');
            $table->decimal('roll', 20, 6)->nullable()->comment('Roll');
            $table->decimal('yaw', 20, 6)->nullable()->comment('Yaw');
            $table->integer('xacc')->nullable()->comment('X Acc');
            $table->integer('yacc')->nullable()->comment('Y Acc');
            $table->integer('zacc')->nullable()->comment('Z Acc');
            $table->decimal('voltint1', 20, 6)->nullable()->comment('VoltageInt1');
            $table->decimal('voltint2', 20, 6)->nullable()->default(-1)->comment('Voltint2');
            $table->decimal('voltnmea', 20, 6)->nullable()->default(-1)->comment('Volt NMEA');
            $table->decimal('voltn2k', 20, 6)->nullable()->default(-1)->comment('Volt N2K');
            $table->smallInteger('bar')->nullable()->comment('Atmospheric Pressure');
            $table->decimal('temp', 20, 6)->nullable()->comment('Board Temperature');
            $table->decimal('tempext', 20, 6)->nullable()->default(-1)->comment('Temp External');
            $table->decimal('tank1', 20, 6)->nullable()->comment('Tank 1');
            $table->decimal('tank2', 20, 6)->nullable()->comment('Tank 2');
            $table->decimal('tank3', 20, 6)->nullable()->comment('Tank 3');
            $table->decimal('tank4', 20, 6)->nullable()->comment('Tank 4');
            $table->decimal('f1', 20, 6)->nullable()->default(-1)->comment('Free Field1');
            $table->decimal('f2', 20, 6)->nullable()->default(-1)->comment('Free Field2');
            $table->decimal('f3', 20, 6)->nullable()->default(-1)->comment('Free Field3');
            $table->decimal('f4', 20, 6)->nullable()->default(-1)->comment('Free Field4');
            $table->decimal('f5', 20, 6)->nullable()->default(-1)->comment('Free Field5');
            $table->decimal('f6', 20, 6)->nullable()->default(-1)->comment('Free Field6');
            $table->tinyInteger('sats')->nullable()->comment('Satellites');
            $table->decimal('hdop', 20, 6)->nullable()->comment('HDOP');
            $table->tinyInteger('gps')->nullable()->comment('GPS Source');
            $table->integer('uptime')->nullable()->comment('Uptime in Seconds');
            $table->timestamp('dbtime')->nullable()->useCurrent()->comment('Time of Upload');
            $table->decimal('dog_nm', 20, 6)->nullable()->comment('Distance Over Ground (nm)');
            $table->decimal('eng1_econ', 20, 6)->nullable()->comment('Engine1 Litres per nm');
            $table->decimal('eng2_econ', 20, 6)->nullable()->comment('Engine2 Litres per nm');
            $table->decimal('tws', 20, 6)->nullable()->comment('True Wind Speed');
            $table->decimal('twa', 20, 6)->nullable()->comment('True Wind Angle');
            $table->decimal('vmg', 20, 6)->nullable()->comment('Velocity Made Good');
            $table->decimal('gws', 20, 6)->nullable()->comment('Ground Wind Speed');
            $table->decimal('gwa', 20, 6)->nullable()->comment('Ground Wind Angle');
            $table->decimal('gvmg', 20, 6)->nullable()->comment('Ground VMG');
            $table->decimal('latdec', 20, 8)->nullable()->comment('Latitude Decimal');
            $table->decimal('londec', 20, 8)->nullable()->comment('Longitude Decimal');
            $table->datetime('datetime')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boatdata');
    }
};
