<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AiTool;
use App\Models\AiUsageLog;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AIDashboardController extends Controller
{
    public function showLogin()
    {
        return view('student_login');
    }

    public function index()
    {
        $user = Auth::user();

        $displayName = $user->users_name ?? 'Guest';
        $userEmail = $user->email ?? null;
        $userType = $user ? User::typeFromEmail($user->email) : null;

        $aiTools = AiTool::where('status_del', '0')
            ->withCount(['usageLogs as usage_count' => function ($query) {
                $query->where(DB::raw("COALESCE(status_del, '0')"), '0');
            }])
            ->orderByDesc('usage_count')
            ->orderBy('ai_name', 'asc')
            ->get();

        return view('ai_dashboard', compact('displayName', 'userEmail', 'userType', 'aiTools'));
    }

    /**
     * Dipanggil GoogleAuthController SETELAH Auth::login() berhasil.
     * Bikin 1 sesi login baru: token unik (buat dedupe di admin), waktu mulai
     * (kolom "Start" di admin), dan daftar tool yang sudah ke-log dikosongkan.
     */
    public static function startLoginSession(): void
    {
        session([
            'login_session_token' => (string) Str::uuid(),
            'login_at'            => now()->format('Y-m-d H:i:s'),
            'logged_tools'        => [],
        ]);
    }

    /**
     * Tutup 1 sesi login (isi logout_at = kolom "End" di admin).
     * Dipanggil pas logout, dan oleh GoogleAuthController SEBELUM login baru
     * (kalau PC perpus ditinggal tanpa logout, lalu orang berikutnya login,
     * sesi orang sebelumnya ditutup di saat itu).
     */
    public static function closeLoginSession(?string $token): void
    {
        if (!$token) {
            return;
        }

        DB::table('ai_usage_logs')
            ->where('login_session_token', $token)
            ->whereNull('logout_at')
            ->update(['logout_at' => now()->format('Y-m-d H:i:s')]);
    }

    public function trackUsage($id)
    {
        $aiTool = AiTool::findOrFail($id);
        $user = Auth::user();

        // Belum login: simpan dulu tool yang diklik, lempar ke halaman login
        if (!$user) {
            session(['intended_ai_id' => $id]);
            return redirect()->route('student.login.page');
        }

        // Jaring pengaman: kalau callback Google belum bikin token sesi,
        // bikin sekarang supaya insert nggak gagal. (Normalnya sudah dibuat
        // pas login; kalau ini sering kepakai berarti callback-nya belum
        // manggil startLoginSession().)
        if (!session()->has('login_session_token')) {
            self::startLoginSession();
        }

        $loggedTools = session('logged_tools', []);

        if (!in_array($id, $loggedTools)) {
            try {
                AiUsageLog::create([
                    'user_email'          => $user->email,
                    'ai_tool_name'        => $aiTool->ai_name,
                    'login_session_token' => session('login_session_token'),
                    'login_at'            => session('login_at'),
                    'ip_address'          => request()->ip(),
                ]);
            } catch (QueryException $e) {
                // Unique (user_email + ai_tool_name + login_session_token) bentrok:
                // baris yang sama persis sudah ada. Sengaja dibiarkan, bukan error.
                if (!$this->isDuplicateKeyError($e)) {
                    throw $e;
                }
            }

            $loggedTools[] = $id;
            session(['logged_tools' => $loggedTools]);
        }

        return redirect()->away($aiTool->ai_url);
    }

    private function isDuplicateKeyError(QueryException $e): bool
    {
        // 1062 = kode error "Duplicate entry" di MySQL/MariaDB
        return (int) ($e->errorInfo[1] ?? 0) === 1062;
    }

    public function closeSession(Request $request)
    {
        return response()->noContent();
    }

    public function logout(Request $request)
    {
        // Catat "End" sesi ini. Auto-logout karena idle juga lewat sini
        // (form logout yang sama).
        self::closeLoginSession(session('login_session_token'));

        Auth::logout();

        // PC bersama: hapus SEMUA isi session + ganti session ID, supaya
        // nggak ada sisa data orang sebelumnya buat pengguna berikutnya.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
