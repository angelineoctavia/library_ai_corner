<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id('feedback_id');
            $table->string('user_email');
            $table->string('category'); // Misal: 'Usulan AI Baru' atau 'Kritik & Saran'
            $table->text('message');     // Isi pesan / nama AI yang diusulkan
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};