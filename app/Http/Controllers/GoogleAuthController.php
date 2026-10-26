<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        // PC perpus dipakai bergantian: akun Google orang sebelumnya bisa masih
        // login di browser. prompt=select_account memaksa Google SELALU nampilin
        // pemilih akun, jadi nggak ada yang otomatis masuk pakai akun orang lain.
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        // Orang menekan "Cancel" / menolak di halaman Google
        if ($request->has('error')) {
            return $this->backToLogin('Login with Google was cancelled.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            // Paling sering: session hilang (buka app lewat localhost tapi redirect URI
            // 127.0.0.1, atau sebaliknya) atau kode Google kedaluwarsa. Detailnya masuk log.
            report($e);
            return $this->backToLogin('Google login failed. Please try again.');
        }

        $email = strtolower(trim((string) $googleUser->getEmail()));

        // Akun bersama perpus (library@ciputra.ac.id): DITOLAK sebelum apa pun dibuat,
        // jadi nggak ada user, sesi, atau log yang tercatat atas nama akun ini.
        if (User::isBlockedEmail($email)) {
            return $this->backToLogin(
                'This is the shared library account and cannot be used to log in. '
                    . 'Please click "Use another account" on the Google screen and sign in with your own university email.'
            );
        }

        // Email harus dari domain kampus, dan sudah diverifikasi oleh Google
        $emailVerified = ($googleUser->user['email_verified'] ?? true) !== false;

        if (!$emailVerified || !User::isAllowedEmail($email)) {
            return $this->backToLogin('Please sign in with your student or staff Google account.');
        }

        // User yang dinonaktifkan admin (status_del = 1) nggak boleh masuk
        $existing = User::where('email', $email)->first();
        if ($existing && (string) $existing->status_del === '1') {
            return $this->backToLogin('This account has been deactivated. Please contact the library.');
        }

        // Kunci pencarian CUMA email -> 1 orang = 1 baris, nggak bakal dobel.
        // users_department diisi 'Student' / 'Staff' (dihitung dari domain email).
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'google_id'        => $googleUser->getId(),
                'users_name'       => $googleUser->getName() ?: $email,
                'users_department' => User::typeFromEmail($email),
            ]
        );

        // Kalau PC ini masih punya sesi orang sebelumnya yang belum logout,
        // tutup dulu (isi "End"-nya) sebelum sesi baru dimulai.
        AIDashboardController::closeLoginSession(session('login_session_token'));

        Auth::login($user);
        $request->session()->regenerate(); // ganti session ID setelah login
        AIDashboardController::startLoginSession(); // token sesi + waktu mulai ("Start")

        // Tadi klik icon AI sebelum login? Balik ke dashboard dan buka tab AI-nya
        $intendedAiId = session('intended_ai_id');
        if ($intendedAiId) {
            session()->forget('intended_ai_id');
            return redirect()->route('dashboard', ['reacquire_tab' => $intendedAiId]);
        }

        return redirect()->route('dashboard');
    }

    private function backToLogin(string $message)
    {
        // Pesan ini ditampilkan sebagai popup di student_login.blade.php
        return redirect()->route('student.login.page')->with('error', $message);
    }
}