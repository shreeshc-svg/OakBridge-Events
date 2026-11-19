<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Admin > Cancelled passes: every pass that was cancelled or deleted, who did it and when. */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cancelled_passes')) {
            return;
        }
        Schema::create('cancelled_passes', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id', 30)->index();       // the pass number, e.g. VD9828
            $table->string('name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone', 30)->nullable();
            $table->string('company')->nullable();
            $table->string('kind', 30);                      // Complimentary / Paid order / Free registration
            $table->string('pass_name', 60)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->unsignedBigInteger('service_id')->nullable()->index();
            $table->string('event')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('order_no', 30)->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->string('source', 80);                    // where it was removed from
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->string('cancelled_by_name', 120)->nullable();
            $table->timestamp('cancelled_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cancelled_passes');
    }
};
