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
        Schema::create('vidhi_sammen', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('image')->nullable();
            $table->string('title')->nullable();
            $table->string('body')->nullable();
            $table->string('year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vidhi_sammen');
    }
};