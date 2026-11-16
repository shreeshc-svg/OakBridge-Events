<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Admin > Ticketing > Complimentary passes: free passes for special guests (no order, no invoice). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'pass_name')) {
                $table->string('pass_name', 60)->nullable();
            }
            if (! Schema::hasColumn('bookings', 'complimentary_at')) {
                $table->timestamp('complimentary_at')->nullable()->index();
            }
            if (! Schema::hasColumn('bookings', 'complimentary_by')) {
                $table->unsignedBigInteger('complimentary_by')->nullable();
            }
            if (! Schema::hasColumn('bookings', 'emailed_at')) {
                $table->timestamp('emailed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'complimentary_at')) {
                $table->dropIndex(['complimentary_at']);
            }
            foreach (['pass_name', 'complimentary_at', 'complimentary_by', 'emailed_at'] as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
