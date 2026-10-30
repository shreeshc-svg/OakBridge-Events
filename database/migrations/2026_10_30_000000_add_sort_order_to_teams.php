<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Website order of speakers, advisors and the organizer team (Admin > Speakers / Advisors / Team). */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('teams', 'sort_order')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('edition_id')->index();
            });
            // keep today's order (oldest first) until someone drags
            DB::table('teams')->update(['sort_order' => DB::raw('id')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('teams', 'sort_order')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->dropIndex(['sort_order']);
                $table->dropColumn('sort_order');
            });
        }
    }
};
