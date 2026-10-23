<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Each year can have its own banner on the Speakers page (Admin > Years > Edit). */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('editions', 'speakers_banner')) {
            Schema::table('editions', fn (Blueprint $table) => $table->string('speakers_banner')->nullable()->after('schedule_service_id'));
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('editions', 'speakers_banner')) {
            Schema::table('editions', fn (Blueprint $table) => $table->dropColumn('speakers_banner'));
        }
    }
};
