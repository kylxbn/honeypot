<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('canary_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique()->index();
            $table->string('label');           // Human-readable name, e.g. ".env APP_URL"
            $table->string('trap_source', 60); // Which fake file/page embeds this token
            $table->string('description')->nullable();
            $table->unsignedInteger('trigger_count')->default(0);
            $table->timestamp('first_triggered_at')->nullable();
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('canary_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('canary_token_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45);
            $table->string('country_code', 2)->nullable();
            $table->string('country_name', 100)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referer', 2000)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canary_triggers');
        Schema::dropIfExists('canary_tokens');
    }
};
