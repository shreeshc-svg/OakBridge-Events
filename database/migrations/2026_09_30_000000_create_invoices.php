<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GST tax invoices for paid orders.
 *
 * An invoice keeps a frozen copy of everything printed on it, so editing the
 * seller details or an order later never changes an invoice already issued.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->unique();
                $table->string('number', 16)->unique();   // GST allows 16 characters at most
                $table->string('fy', 7);                  // 2026-27
                $table->unsignedInteger('seq');
                $table->timestamp('issued_at');
                $table->json('snapshot');
                $table->unsignedSmallInteger('sent_count')->default(0);
                $table->timestamp('last_sent_at')->nullable();
                $table->timestamps();

                $table->unique(['fy', 'seq']);            // one unbroken series per year
            });
        }

        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'invoice_prefix')) {
                $table->string('invoice_prefix', 6)->default('OBE');
                $table->boolean('invoice_auto_send')->default(1);
                $table->string('seller_name')->nullable();
                $table->text('seller_address')->nullable();
                $table->string('seller_gstin', 15)->nullable();
                $table->string('seller_state', 60)->nullable();
                $table->string('seller_phone', 60)->nullable();
                $table->string('seller_email')->nullable();
                $table->string('invoice_sac', 8)->nullable();
                $table->string('bank_name')->nullable();
                $table->string('bank_account', 40)->nullable();
                $table->string('bank_branch_ifsc')->nullable();
                $table->text('invoice_declaration')->nullable();
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'buyer_gstin')) {
                $table->string('buyer_gstin', 15)->nullable();
                $table->string('billing_address', 500)->nullable();
                $table->string('billing_state', 60)->nullable();
                $table->string('billing_pin', 6)->nullable();
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'place_of_supply')) {
                // where the event is held; decides IGST against CGST + SGST
                $table->string('place_of_supply', 60)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');

        $drop = function (string $table, array $columns) {
            $present = array_values(array_filter($columns, fn ($c) => Schema::hasColumn($table, $c)));
            if ($present) {
                Schema::table($table, fn (Blueprint $t) => $t->dropColumn($present));
            }
        };

        $drop('settings', ['invoice_prefix', 'invoice_auto_send', 'seller_name', 'seller_address', 'seller_gstin',
            'seller_state', 'seller_phone', 'seller_email', 'invoice_sac', 'bank_name', 'bank_account',
            'bank_branch_ifsc', 'invoice_declaration']);
        $drop('orders', ['buyer_gstin', 'billing_address', 'billing_state', 'billing_pin']);
        $drop('services', ['place_of_supply']);
    }
};
