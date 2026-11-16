<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Complimentary passes in groups: several passes for one contact share a group
 * (one email lists them all), and the pass email can be copied to more addresses.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'comp_group')) {
                $table->string('comp_group', 36)->nullable()->index();
            }
            if (! Schema::hasColumn('bookings', 'comp_cc')) {
                $table->string('comp_cc', 500)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'comp_group')) {
                $table->dropIndex(['comp_group']);
                $table->dropColumn('comp_group');
            }
            if (Schema::hasColumn('bookings', 'comp_cc')) {
                $table->dropColumn('comp_cc');
            }
        });
    }
};
