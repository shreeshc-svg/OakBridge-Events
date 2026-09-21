<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Paid tickets: pass types and their prices, the bulk discount slabs,
     * and one order per purchase (Admin > Ticketing).
     * Events with no pass type stay free to register, exactly as before.
     */
    public function up(): void
    {
        Schema::create('pass_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('name', 60);
            $table->string('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('early_price', 10, 2)->nullable();
            $table->date('early_until')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('services')->cascadeOnDelete();
            $table->unsignedSmallInteger('min_quantity');
            $table->enum('discount_type', ['percent', 'flat'])->default('percent');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 20)->unique();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('pass_type_id')->nullable()->constrained('pass_types')->nullOnDelete();
            $table->string('event')->nullable();
            $table->string('pass_name', 60)->nullable();
            $table->string('buyer_name', 100);
            $table->string('buyer_email', 100);
            $table->string('buyer_phone', 20);
            $table->string('buyer_company', 100)->nullable();
            $table->string('buyer_designation', 100)->nullable();
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->string('discount_label')->nullable();
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_total', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('currency', 3)->default('INR');
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('payment_method', 40)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->after('id')->constrained('orders')->nullOnDelete();
            $table->unsignedSmallInteger('attendee_no')->default(1)->after('booking_id');
            $table->decimal('amount', 10, 2)->nullable()->after('designation');
        });
        // attendees other than the buyer have no phone of their own
        Schema::hasColumn('bookings', 'phone') && Schema::getConnection()
            ->statement('ALTER TABLE bookings MODIFY phone VARCHAR(255) NULL');

        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('tax_percent', 5, 2)->nullable();
            $table->boolean('prices_include_tax')->default(true);
            $table->string('tax_label', 30)->nullable();
            $table->text('payment_instructions')->nullable();
            $table->text('invoice_note')->nullable();
            $table->unsignedSmallInteger('max_passes_per_order')->default(10);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn(['order_id', 'attendee_no', 'amount']);
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['tax_percent', 'prices_include_tax', 'tax_label', 'payment_instructions', 'invoice_note', 'max_passes_per_order']);
        });
        Schema::dropIfExists('orders');
        Schema::dropIfExists('pricing_tiers');
        Schema::dropIfExists('pass_types');
    }
};
