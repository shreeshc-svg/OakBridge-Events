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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->foreignId('user_id')->contrained('users');
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keyword')->nullable();

            //Is featured
            $table->enum('featured',[0,1])
                  ->default(0)
                  ->comment('0 for false and 1 for true');

            // is published
            $table->enum('published',[0, 1])
                  ->default(0)
                  ->comment('0 for false and 1 for true');

            // is comment
            $table->enum('disable_comment',[0, 1])
                  ->default(0)
                  ->comment('0 for false and 1 for true');

            // views
            $table->bigInteger('views')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};