<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Special offers: the least number of passes someone must buy to get the offer price. */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('special_offers', 'min_per_person')) {
            Schema::table('special_offers', function (Blueprint $table) {
                $table->unsignedTinyInteger('min_per_person')->default(1);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('special_offers', 'min_per_person')) {
            Schema::table('special_offers', function (Blueprint $table) {
                $table->dropColumn('min_per_person');
            });
        }
    }
};
