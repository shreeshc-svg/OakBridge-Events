<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ticketing > Coupons. A coupon (e.g. "ALITA members – 20% off") has codes: one shared
 * code and/or one-time codes for specific people. Every use is a redemption tied to
 * its order: it counts while the order is awaiting payment or paid, and frees the code
 * again if the order is cancelled, fails or is deleted.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->string('name', 120);
                $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
                $table->foreignId('pass_type_id')->nullable()->constrained('pass_types')->nullOnDelete(); // null = any pass
                $table->string('discount_type', 10)->default('percent');   // percent | amount
                $table->decimal('discount_value', 10, 2);
                $table->unsignedTinyInteger('max_passes')->default(1);     // passes per use at the coupon price
                $table->date('valid_from')->nullable();
                $table->date('valid_until')->nullable();
                $table->boolean('once_per_email')->default(true);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('coupon_codes')) {
            Schema::create('coupon_codes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
                $table->string('code', 40)->unique();
                $table->unsignedInteger('max_uses')->default(1);
                $table->string('assigned_name', 100)->nullable();
                $table->string('assigned_email', 100)->nullable()->index();
                $table->boolean('lock_to_email')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('coupon_redemptions')) {
            Schema::create('coupon_redemptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
                $table->foreignId('coupon_code_id')->constrained('coupon_codes')->cascadeOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->string('order_no', 30)->nullable();
                $table->string('email', 100)->index();
                $table->string('name', 100)->nullable();
                $table->unsignedSmallInteger('quantity');
                $table->decimal('discount', 10, 2);
                $table->timestamps();
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'coupon_code_id')) {
                $table->unsignedBigInteger('coupon_code_id')->nullable()->index();
                $table->string('coupon_code', 40)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'coupon_code_id')) {
                $table->dropIndex(['coupon_code_id']);
                $table->dropColumn(['coupon_code_id', 'coupon_code']);
            }
        });
        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('coupon_codes');
        Schema::dropIfExists('coupons');
    }
};
