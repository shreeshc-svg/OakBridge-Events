<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('settings', 'seller_pan')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('seller_pan', 10)->nullable()->after('seller_gstin');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'seller_pan')) {
            Schema::table('settings', fn (Blueprint $table) => $table->dropColumn('seller_pan'));
        }
    }
};
