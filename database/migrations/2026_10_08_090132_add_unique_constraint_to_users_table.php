<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Bersihin baris users yang NIM-nya duplikat (akibat bug
        // updateOrCreate() yang dulu include users_name di kriteria
        // pencarian). Sisain baris dengan users_id PALING BESAR
        // (paling baru/terakhir dibuat, kemungkinan data namanya
        // paling update), hapus sisanya.
        DB::statement("
            DELETE u1 FROM users u1
            INNER JOIN users u2
                ON u1.users_nim = u2.users_nim
                AND u1.users_id < u2.users_id
        ");

        Schema::table('users', function (Blueprint $table) {
            // Dari sini, 1 NIM = 1 baris, selamanya. Kalau ada percobaan
            // bikin baris baru dengan NIM yang sudah ada, DB yang nolak.
            $table->unique('users_nim', 'users_users_nim_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_users_nim_unique');
        });
    }
};