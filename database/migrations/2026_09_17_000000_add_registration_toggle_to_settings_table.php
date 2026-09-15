<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Open/close event registration from Admin > Registration.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('registration_enabled')->default(1);
            $table->string('registration_button_text', 40)->nullable();
            $table->string('registration_closed_message', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['registration_enabled', 'registration_button_text', 'registration_closed_message']);
        });
    }
};
