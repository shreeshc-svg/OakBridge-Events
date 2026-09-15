<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * - Event (services) fields edited in Admin > Events > Schedules
     * - Which event each booking was made for (bookings.service_id / event)
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('end_time', 10)->nullable();       // HH:MM, same day as `date`
            $table->string('venue')->nullable();              // falls back to the address in Settings
            $table->string('agenda_file')->nullable();        // falls back to Page Content > Schedule
            $table->unsignedInteger('seat_target')->nullable();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable()->index();
            $table->string('event')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['service_id']);
            $table->dropColumn(['service_id', 'event']);
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['end_time', 'venue', 'agenda_file', 'seat_target']);
        });
    }
};
