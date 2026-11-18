<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ticketing > Special price offers: a pass at a special price for a list of people.
 * Each person on the list gets a personal link (offer_invites.token); an order is
 * only created when they check out from that link.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('special_offers')) {
            Schema::create('special_offers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
                $table->foreignId('pass_type_id')->nullable()->constrained('pass_types')->nullOnDelete();
                $table->string('title', 120);
                $table->decimal('unit_price', 10, 2);
                $table->boolean('charge_tax')->default(false);
                $table->unsignedTinyInteger('max_per_person')->default(1);
                $table->date('expires_on')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('offer_invites')) {
            Schema::create('offer_invites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('special_offer_id')->constrained('special_offers')->cascadeOnDelete();
                $table->string('token', 40)->unique();
                $table->string('name', 100);
                $table->string('email', 100);
                $table->string('phone', 20)->nullable();
                $table->boolean('is_test')->default(false);
                $table->timestamp('clicked_at')->nullable();
                $table->timestamps();
                $table->unique(['special_offer_id', 'email']);
            });
        }

        if (! Schema::hasColumn('orders', 'offer_invite_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('offer_invite_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'offer_invite_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['offer_invite_id']);
                $table->dropColumn('offer_invite_id');
            });
        }
        Schema::dropIfExists('offer_invites');
        Schema::dropIfExists('special_offers');
    }
};
