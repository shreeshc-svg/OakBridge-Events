<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Cover image shown while a video hero banner loads (Admin > Hero Banner). */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('hero_poster')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('hero_poster');
        });
    }
};
