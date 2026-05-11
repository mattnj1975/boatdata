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
    Schema::table('boatdata', function (Blueprint $table) {
        $table->index(
            ['mac', 'date', 'val', 'datetime'],
            'idx_boatdata_mac_date_val_datetime'
        );

        $table->index(
            ['mac', 'date', 'utc'],
            'idx_boatdata_mac_date_utc'
        );
    });
}

public function down(): void
{
    Schema::table('boatdata', function (Blueprint $table) {
        $table->dropIndex('idx_boatdata_mac_date_val_datetime');
        $table->dropIndex('idx_boatdata_mac_date_utc');
    });
}
};
