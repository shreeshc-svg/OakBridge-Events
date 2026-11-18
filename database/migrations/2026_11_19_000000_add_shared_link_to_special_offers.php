<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * One link per offer (/offer/o/{code}) to paste into a bulk-mail campaign, instead of
 * a personal link per person. open_to_anyone: anyone with the link may use it; when
 * off, only emails on the offer's list.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('special_offers', function (Blueprint $table) {
            if (! Schema::hasColumn('special_offers', 'code')) {
                $table->string('code', 16)->nullable()->unique();
            }
            if (! Schema::hasColumn('special_offers', 'open_to_anyone')) {
                $table->boolean('open_to_anyone')->default(true);
            }
            if (! Schema::hasColumn('special_offers', 'views')) {
                $table->unsignedInteger('views')->default(0);
            }
        });

        // offers made before this get their link too
        foreach (DB::table('special_offers')->whereNull('code')->pluck('id') as $id) {
            DB::table('special_offers')->where('id', $id)->update(['code' => Str::random(10)]);
        }
    }

    public function down(): void
    {
        Schema::table('special_offers', function (Blueprint $table) {
            if (Schema::hasColumn('special_offers', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }
            foreach (['open_to_anyone', 'views'] as $column) {
                if (Schema::hasColumn('special_offers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
