<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** A record of every payment reminder sent for an order, so admin can see what the buyer got. */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_reminders')) {
            return;
        }

        Schema::create('order_reminders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('kind', 10)->default('manual');      // auto | manual
            $table->unsignedTinyInteger('stage')->nullable();   // 1, 2, 3 for the automatic ones
            $table->string('sent_to');
            $table->string('subject');
            $table->string('sent_by')->nullable();              // the admin who pressed the button
            $table->boolean('failed')->default(0);
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_reminders');
    }
};
