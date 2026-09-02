<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AiTool;
use App\Models\AiUsageLog;

class AIDashboardController extends Controller
{
    // Menampilkan Dashboard (Ambil data AI Tools dari database)
    public function index()
    {
        $studentNim = session('student_nim');
        $studentMajor = session('student_major');

        $aiTools = AiTool::where('status_del', '0')->get();

        return view('ai_dashboard', compact('studentNim', 'studentMajor', 'aiTools'));
    }

    // Menampilkan Halaman Login Mahasiswa
    public function showLogin()
    {
        return view('student_login');
    }

    // Proses Login Mahasiswa
    public function processLogin(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|min:6'
        ]);

        $nim = $request->nim;

        $prefix = substr($nim, 0, 6);
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

        $major = $mapping[$prefix] ?? 'Unknown Major';

        if ($major === 'Unknown Major') {
            return back()->with('error', 'NIM tidak terdaftar dalam sistem!');
        }

        $user = User::updateOrCreate(
            ['users_nim' => $nim],
            ['users_major' => $major]
        );

        session([
            'user_id' => $user->users_id,
            'student_nim' => $nim,
            'student_major' => $major
        ]);

        return redirect()->route('dashboard');
    }

    // Fungsi untuk mencatat log klik AI lalu redirect ke web aslinya
    // Hanya dicatat SEKALI per sesi login untuk AI tool yang sama —
    // klik berikutnya ke tool yang sama (misal karena tab-nya sudah
    // kebuka tapi user klik ikonnya lagi) tidak menambah baris baru.
    public function trackUsage($id)
    {
        $aiTool = AiTool::findOrFail($id);
        $nim = session('student_nim');

        $loggedTools = session('logged_tools', []);

        if (!in_array($id, $loggedTools)) {
            AiUsageLog::create([
                'student_nim' => $nim,
                'ai_tool_name' => $aiTool->ai_name,
            ]);

            $loggedTools[] = $id;
            session(['logged_tools' => $loggedTools]);
        }

        return redirect()->away($aiTool->ai_url);
    }

    // Beacon dari dashboard saat tab AI ditutup / tab dashboard aktif lagi.
    // Tracking durasi sudah tidak dipakai, jadi ini sengaja dibiarkan kosong
    // (tetap ada supaya route & JS beacon di ai_dashboard.blade.php tidak error).
    public function closeSession(Request $request)
    {
        return response()->noContent();
    }

    // Logout Mahasiswa
    public function logout()
    {
        session()->forget(['user_id', 'student_nim', 'student_major', 'logged_tools']);
        return redirect('/');
    }
}