<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('users_id');
            $table->string('users_nim');
            $table->string('users_name')->nullable();
            $table->string('users_department');
            $table->timestamp('created_at')->useCurrent();
            $table->string('status_del', 1)->default('0');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};