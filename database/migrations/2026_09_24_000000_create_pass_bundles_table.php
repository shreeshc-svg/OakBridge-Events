<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bundle prices: a fixed total for a given number of passes
     * (Admin > Ticketing). Quantities with no row fall back to the
     * per-pass price, and anything above the table adds the extra-pass price.
     */
    public function up(): void
    {
        Schema::create('pass_bundles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pass_type_id')->constrained('pass_types')->cascadeOnDelete();
            $table->unsignedSmallInteger('quantity');
            $table->decimal('total', 10, 2)->nullable();        // list price total
            $table->decimal('early_total', 10, 2)->nullable();  // early bird total
            $table->timestamps();
            $table->unique(['pass_type_id', 'quantity']);
        });

        Schema::table('pass_types', function (Blueprint $table) {
            // cost of each pass beyond the largest bundle row
            $table->decimal('extra_price', 10, 2)->nullable();
            $table->decimal('early_extra_price', 10, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pass_types', function (Blueprint $table) {
            $table->dropColumn(['extra_price', 'early_extra_price']);
        });
        Schema::dropIfExists('pass_bundles');
    }
};
