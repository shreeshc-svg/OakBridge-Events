<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** The order homepage sections appear in, set in Admin > Page Content. */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('settings', 'home_section_order')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->text('home_section_order')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'home_section_order')) {
            Schema::table('settings', fn (Blueprint $table) => $table->dropColumn('home_section_order'));
        }
    }
};
