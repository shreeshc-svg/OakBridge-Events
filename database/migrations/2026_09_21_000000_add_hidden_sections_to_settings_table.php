<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Page sections an admin has switched off (Admin > Page Content), stored as a JSON list of keys. */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('hidden_sections')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('hidden_sections');
        });
    }
};
