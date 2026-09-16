<?php

use App\Support\BookingNumber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remember the last booking number issued (VD9xxx), so clearing the
     * registrations never makes the site hand out an old number again.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedInteger('booking_last_number')->nullable();
        });

        $highest = BookingNumber::highestInBookings();
        if ($highest !== null) {
            DB::table('settings')->where('id', 1)->update(['booking_last_number' => $highest]);
        }
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('booking_last_number');
        });
    }
};
