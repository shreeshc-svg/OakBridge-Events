<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Online payment (Razorpay): the attendees are held on the order until
     * the payment is confirmed, and only then become bookings.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('attendees')->nullable()->after('quantity');
            $table->string('gateway_order_id')->nullable()->after('payment_reference');
            $table->string('gateway')->nullable()->after('payment_method');
            $table->timestamp('reminded_at')->nullable()->after('paid_at');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('online_payment')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['attendees', 'gateway_order_id', 'gateway', 'reminded_at']);
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('online_payment');
        });
    }
};
