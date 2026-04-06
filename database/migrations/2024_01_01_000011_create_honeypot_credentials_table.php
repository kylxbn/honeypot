<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('honeypot_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('honeypot_request_id')->nullable()->constrained()->nullOnDelete();
            $table->string('trap_url', 500);
            $table->string('username', 500)->nullable();
            $table->string('password', 500)->nullable();
            $table->json('additional_fields')->nullable();
            $table->string('ip_address', 45)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('honeypot_credentials');
    }
};
