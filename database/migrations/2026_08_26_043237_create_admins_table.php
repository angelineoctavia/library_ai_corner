<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id('admin_id');
            $table->string('username')->unique(); // Username default admin
            $table->string('password');           // Password yang di-hash (bcrypt)
            $table->timestamp('created_at')->useCurrent();
            $table->string('status_del', 1)->default('0');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
