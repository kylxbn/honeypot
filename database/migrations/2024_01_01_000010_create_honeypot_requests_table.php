<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('honeypot_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->index();
            $table->string('method', 10);
            $table->text('url');
            $table->string('path', 1000)->index();
            $table->text('query_string')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('headers')->nullable();
            $table->longText('request_body')->nullable();
            $table->string('referer', 2000)->nullable();
            $table->string('trap_type', 60)->default('unknown')->index();
            $table->boolean('is_flagged')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('honeypot_requests');
    }
};
