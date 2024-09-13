<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('bname')->nullable();
            $table->string('email')->nullable();
            $table->string('site_title')->nullable();
            $table->mediumText('site_description')->nullable();
            $table->mediumText('site_keywords')->nullable();
            $table->string('logo')->nullable();
            $table->mediumText('gtag')->nullable();
            $table->mediumText('map')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone2')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('address')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
