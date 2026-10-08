<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Bersihin dulu baris yang BENER-BENER duplikat persis
        // (student_nim + ai_tool_name + login_session_token sama persis),
        // simpan yang id-nya paling kecil (paling awal ditulis), biar unique
        // index di bawah bisa dipasang tanpa bentrok.
        //
        // Baris LAMA yang login_session_token-nya NULL sengaja TIDAK disentuh -
        // MySQL/MariaDB mengizinkan banyak baris NULL dalam unique index, jadi
        // baris lama itu tetap aman dan tidak akan membentur constraint ini.
        DB::statement("
            DELETE t1 FROM ai_usage_logs t1
            INNER JOIN ai_usage_logs t2
                ON t1.student_nim = t2.student_nim
                AND t1.ai_tool_name = t2.ai_tool_name
                AND t1.login_session_token = t2.login_session_token
                AND t1.login_session_token IS NOT NULL
                AND t1.usage_logs_id > t2.usage_logs_id
        ");

        Schema::table('ai_usage_logs', function (Blueprint $table) {
            // Dari sini, DB sendiri yang menolak kalau ada percobaan insert
            // kedua (atau ketiga, keempat, dst) untuk kombinasi NIM + tool +
            // sesi login yang sama - nggak peduli berapa request paralel
            // yang masuk nyaris bersamaan.
            $table->unique(
                ['student_nim', 'ai_tool_name', 'login_session_token'],
                'ai_usage_logs_dedupe_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('ai_usage_logs', function (Blueprint $table) {
            $table->dropUnique('ai_usage_logs_dedupe_unique');
        });
    }
};