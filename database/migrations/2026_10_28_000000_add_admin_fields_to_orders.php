<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Orders an admin creates by hand: who created it, and the list price when a special price was used. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->index();
            }
            if (! Schema::hasColumn('orders', 'list_price')) {
                $table->decimal('list_price', 10, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'created_by')) {
                $table->dropIndex(['created_by']);
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('orders', 'list_price')) {
                $table->dropColumn('list_price');
            }
        });
    }
};
