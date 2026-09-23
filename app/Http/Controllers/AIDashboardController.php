<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AiTool;
use App\Models\AiUsageLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AIDashboardController extends Controller
{
    public function showLogin()
    {
        return view('student_login');
    }

    public function index()
    {
        $studentNim = session('student_nim') ?? session('staff_id');
        $studentMajor = session('student_major') ?? 'Staff / Lecturer';
        $displayName = session('student_name') ?? $studentNim ?? 'Guest';

        $aiTools = AiTool::where('status_del', '0')->get();

        $riwayat = AiUsageLog::latest()->get()->unique(function ($item) {
            return $item->student_nim . '-' . $item->ai_tool_name;
        });

        return view('ai_dashboard', compact('studentNim', 'studentMajor', 'displayName', 'aiTools', 'riwayat'));
    }

    public function processLogin(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|min:3',
            'source'     => 'nullable|string'
        ]);

        $loginSource = $request->input('source', 'manual');
        $rawIdentifier = trim($request->identifier);

        // --- PARSING FORMAT BARU (NIM+Nama+) ---
        if (str_contains($rawIdentifier, '+')) {
            $parts = explode('+', $rawIdentifier);
            $identifier  = trim($parts[0] ?? '');
            $scannedName = trim($parts[1] ?? '');
        } else {
            $identifier  = $rawIdentifier;
            $scannedName = '';
        }

        $prefix6 = substr($identifier, 0, 6);
        $prefix4 = substr($identifier, 0, 4);

        $mapping = [
            '101601' => 'Management - Reguler Class',
            '010601' => 'Management - Reguler Class',
            '101602' => 'Management - International Class',
            '010602' => 'Management - International Class',
            '101604' => 'Accounting',
            '010604' => 'Accounting',
            '101801' => 'Magister of Management',
            '010801' => 'Magister of Management',
            '101891' => 'Magister of Management (BUF)',
            '010891' => 'Magister of Management (BUF)',
            '101901' => 'Management S3',
            '010901' => 'Management S3',
            '102603' => 'Architecture',
            '020603' => 'Architecture',
            '102604' => 'Visual Communication Design',
            '020604' => 'Visual Communication Design',
            '102606' => 'Fashion Design and Business',
            '020606' => 'Fashion Design and Business',
            '103601' => 'Psychology',
            '030601' => 'Psychology',
            '103701' => 'Professional Psychologist Education',
            '030701' => 'Professional Psychologist Education',
            '104601' => 'Hotel, Tourism, and Event Business',
            '040601' => 'Hotel, Tourism, and Event Business',
            '104602' => 'Tourism - Culinary Business',
            '040602' => 'Tourism - Culinary Business',
            '104604' => 'Food Technology Program',
            '040604' => 'Food Technology Program',
            '105601' => 'Communication Science',
            '050601' => 'Communication Science',
            '106601' => 'Medicine',
            '060601' => 'Medicine',
            '106701' => 'Medical Doctor Profession Education',
            '060701' => 'Medical Doctor Profession Education',
            '109601' => 'Dental Medicine',
            '090601' => 'Dental Medicine',
            '107601' => 'Informatics',
            '070601' => 'Informatics',
            '107602' => 'Information System',
            '070602' => 'Information System'
        ];

        $department = $mapping[$prefix6] ?? ($mapping[$prefix4] ?? null);

        // Inisialisasi variabel $isStaff supaya aman dari warning editor
        $isStaff = false;

        // Cek apakah ini Staff ID (Total 8 digit, 4 digit awal adalah tahun wajar)
        if (!$department) {
            $tahunMasuk = substr($identifier, 0, 4);

            if (strlen($identifier) === 8 && is_numeric($tahunMasuk) && $tahunMasuk >= 1950 && $tahunMasuk <= 2030) {
                $isStaff = true;
                $department = 'Staff / Lecturer';
            }
        }

        if ($department || $isStaff) {
            $userName = $scannedName !== '' ? $scannedName : ($isStaff ? 'Staff (' . $identifier . ')' : 'Student (' . $identifier . ')');

            $user = User::updateOrCreate([
                'users_nim'        => $identifier,
                'users_name'       => $userName,
                'users_department' => $department,
                'status_del'       => '0'
            ]);

            // Simpan session sesuai identitas (mahasiswa atau staff)
            if ($isStaff) {
                session([
                    'user_id'       => $user->users_id,
                    'staff_id'      => $identifier,
                    'student_name'  => $userName,
                    'student_major' => $department,
                    'login_source'  => $loginSource
                ]);
            } else {
                session([
                    'user_id'       => $user->users_id,
                    'student_nim'   => $identifier,
                    'student_name'  => $userName,
                    'student_major' => $department,
                    'login_source'  => $loginSource
                ]);
            }

            $intendedAiId = session('intended_ai_id');
            $openAiUrl = null;

            if ($intendedAiId) {
                session()->forget('intended_ai_id');
                $openAiUrl = route('ai.visit', ['id' => $intendedAiId]);
                $redirectUrl = route('dashboard', ['reacquire_tab' => $intendedAiId]);
            } else {
                $redirectUrl = route('dashboard');
            }

            return response()->json([
                'success'      => true,
                'name'         => $userName,
                'redirect_url' => $redirectUrl,
                'open_ai_id'   => $intendedAiId,
                'open_ai_url'  => $openAiUrl,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'NIM atau Staff ID tidak dikenali dalam sistem.'
            ], 422);
        }
    }

    public function trackUsage($id)
    {
        $aiTool = AiTool::findOrFail($id);
        $nim = session('student_nim') ?? session('staff_id');

        // 1. Kalau user BELUM login, simpan dulu ID tool yang ingin dituju ke session, lalu lempar ke login
        if (!$nim) {
            session(['intended_ai_id' => $id]);
            return redirect()->route('student.login.page'); // Sesuaikan dengan route halaman login kamu
        }

        // Lock atomik di level database - anti race condition,
        // gak peduli berapa request nyaris bersamaan yang masuk
        $lock = Cache::store('database')->lock("ai_visit_{$nim}_{$id}", 5);

        if ($lock->get()) {
            try {
                $loggedTools = session('logged_tools', []);
                if (!in_array($id, $loggedTools)) {
                    AiUsageLog::create([
                        'student_nim'  => $nim,
                        'ai_tool_name' => $aiTool->ai_name,
                    ]);
                    $loggedTools[] = $id;
                    session(['logged_tools' => $loggedTools]);
                }
            } finally {
                $lock->release();
            }
        }
        // kalau lock gagal didapat -> ada request lain lagi proses barengan, skip logging, tetap redirect

        return redirect()->away($aiTool->ai_url);
    }

    public function closeSession(Request $request)
    {
        return response()->noContent();
    }

    public function logout()
    {
        // Cukup bersihkan session dan kembalikan ke halaman login/utama
        session()->forget(['user_id', 'student_nim', 'staff_id', 'student_name', 'student_major', 'login_source', 'logged_tools']);

        return redirect('/');
    }
}
