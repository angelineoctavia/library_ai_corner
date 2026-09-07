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

            // Langsung simpan NIM dan Nama AI-nya sebagai string
            $table->string('student_nim'); 
            $table->string('ai_tool_name');

            // Cuma pakai created_at saja, updated_at dihapus biar bersih
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};