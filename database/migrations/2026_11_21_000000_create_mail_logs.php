<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Admin > Emails sent: every email the website sends, searchable by person. */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mail_logs')) {
            return;
        }
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('to_email', 191)->index();
            $table->string('to_name', 191)->nullable()->index();
            $table->string('phone', 30)->nullable()->index();
            $table->string('cc', 500)->nullable();
            $table->string('subject', 300)->nullable();
            $table->string('type', 60)->index();            // Pass, Complimentary pass, Payment reminder…
            $table->string('refs', 500)->nullable();         // pass and order numbers found in the email
            $table->string('search', 1200)->nullable();      // one compact, lower-case field for fast searching
            $table->string('status', 10)->default('sending')->index(); // sending -> sent; still "sending" = failed
            $table->string('message_id', 191)->nullable();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamps();
        });

        // the content lives apart, so searching the list never has to read it
        Schema::create('mail_log_bodies', function (Blueprint $table) {
            $table->unsignedBigInteger('mail_log_id')->primary();
            $table->longText('body')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_log_bodies');
        Schema::dropIfExists('mail_logs');
    }
};
