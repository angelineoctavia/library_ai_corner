<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_tools', function (Blueprint $table) {
            $table->id('ai_id');
            $table->string('ai_name');
            $table->string('ai_icon'); // Lokasi file icon
            $table->string('ai_url');
            $table->timestamp('created_at')->useCurrent();
            $table->string('status_del', 1)->default('0');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_tools');
    }
};
