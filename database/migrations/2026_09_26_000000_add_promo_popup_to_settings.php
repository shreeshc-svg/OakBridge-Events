<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Admin-managed marketing popup shown on the home page. */
return new class extends Migration
{
    private const COLUMNS = [
        'promo_enabled', 'promo_media', 'promo_media_mobile', 'promo_alt', 'promo_link',
        'promo_new_tab', 'promo_delay', 'promo_starts_at', 'promo_ends_at',
        'promo_dismissible', 'promo_version', 'promo_width', 'promo_height',
    ];

    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'promo_enabled')) {
                $table->boolean('promo_enabled')->default(0);
            }
            if (! Schema::hasColumn('settings', 'promo_media')) {
                $table->string('promo_media')->nullable();              // desktop image / GIF / video
                $table->string('promo_media_mobile')->nullable();       // optional portrait version for phones
                $table->string('promo_alt')->nullable();                // alt text for screen readers
                $table->string('promo_link', 500)->nullable();          // where a click goes; null = not clickable
                $table->boolean('promo_new_tab')->default(1);
                $table->unsignedSmallInteger('promo_delay')->default(3);   // seconds after page load
                $table->date('promo_starts_at')->nullable();
                $table->date('promo_ends_at')->nullable();
                $table->boolean('promo_dismissible')->default(1);       // show the "don't show me again" tick box
                $table->unsignedInteger('promo_version')->default(1);   // bumped on save so dismissals reset
                $table->unsignedSmallInteger('promo_width')->nullable();  // natural pixel size of the desktop file,
                $table->unsignedSmallInteger('promo_height')->nullable(); // so the popup never upscales it
            }
        });
    }

    public function down(): void
    {
        $existing = array_values(array_filter(
            self::COLUMNS,
            fn ($column) => Schema::hasColumn('settings', $column)
        ));

        if ($existing) {
            Schema::table('settings', fn (Blueprint $table) => $table->dropColumn($existing));
        }
    }
};
