<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Scrolling marketing strip under the homepage hero banner,
     * managed from Admin > Marketing Strip.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('strip_enabled')->default(0);
            // one phrase per line
            $table->text('strip_items')->nullable();
            $table->string('strip_bg', 7)->default('#D3181F');
            $table->string('strip_color', 7)->default('#FFFFFF');
            // slow | normal | fast
            $table->string('strip_speed', 10)->default('normal');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['strip_enabled', 'strip_items', 'strip_bg', 'strip_color', 'strip_speed']);
        });
    }
};
