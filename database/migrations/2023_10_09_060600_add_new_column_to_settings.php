<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('captcha_site_key')->nullable()->after('youtube');
            $table->text('captch_secret_key')->nullable()->after('captcha_site_key');
            $table->boolean('captcha_contact_form')
                    ->default(0)->after('captch_secret_key')->nullable()
                    ->comment('0 for false 1 for true');
            $table->boolean('captcha_login_form')
                  ->default(0)->after('captcha_contact_form')->nullable()
                  ->comment('0 for false 1 for true');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
