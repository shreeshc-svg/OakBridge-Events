<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Homepage hero banner, managed from Admin > Hero Banner.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('hero_enabled')->default(1);
            $table->string('hero_image')->nullable();
            $table->string('hero_image_mobile')->nullable();
            $table->string('hero_alt')->nullable();
            // register = open the registration pop-up, link = go to hero_link, none = not clickable
            $table->string('hero_click', 20)->default('register');
            $table->string('hero_link', 500)->nullable();
            $table->boolean('hero_new_tab')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_enabled',
                'hero_image',
                'hero_image_mobile',
                'hero_alt',
                'hero_click',
                'hero_link',
                'hero_new_tab',
            ]);
        });
    }
};
