<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id('usage_logs_id');
            $table->string('user_email');
            $table->string('ai_tool_name');
            $table->string('login_session_token', 64);

            // Waktu login & logout sesi (sesuai request untuk tracking presisi)
            $table->dateTime('login_at')->nullable();
            $table->dateTime('logout_at')->nullable();

            // Tambahan sesuai request (nullable dulu untuk persiapan praktek lab)
            $table->string('ip_address')->nullable();
            $table->string('location')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->string('status_del', 1)->default('0');

            // 1 sesi login + 1 AI = 1 baris unik
            $table->unique(['user_email', 'ai_tool_name', 'login_session_token'], 'ai_usage_logs_session_unique');
            $table->index('login_session_token'); // dipakai pas logout menutup sesi
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};
