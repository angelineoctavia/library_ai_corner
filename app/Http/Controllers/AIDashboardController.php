<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AiTool;
use App\Models\AiUsageLog;

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

        return view('ai_dashboard', compact('studentNim', 'studentMajor', 'displayName', 'aiTools'));
    }

    public function processLogin(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|min:3'
        ]);

        $rawIdentifier = trim($request->identifier);

        // --- PARSING FORMAT BARU (NIM+Nama+) ---
        // Contoh: 0706022310006+Angeline+
        if (str_contains($rawIdentifier, '+')) {
            $parts = explode('+', $rawIdentifier);
            $identifier  = trim($parts[0] ?? ''); // Ini NIM-nya (bisa 8 digit, 10 digit, dsb)
            $scannedName = trim($parts[1] ?? ''); // Ini Nama dari hasil scan
        } else {
            $identifier  = $rawIdentifier;
            $scannedName = '';
        }

        // Ambil prefix untuk dicocokkan dengan list jurusan (bisa cek 6 digit atau 4 digit awal)
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

        // Cek apakah prefix 6 digit atau 4 digit terdaftar di mapping
        $department = $mapping[$prefix6] ?? ($mapping[$prefix4] ?? null);

        if ($department) {
            // Gunakan nama dari hasil scan kartu, kalau kosong pakai fallback NIM
            $userName = $scannedName !== '' ? $scannedName : ('Student (' . $identifier . ')');

            $user = User::Create([
                    'users_nim'        => $identifier,
                    'users_name'       => $userName,
                    'users_department' => $department,
                    'status_del'       => '0'
                ]
            );

            session([
                'user_id'       => $user->users_id,
                'student_nim'   => $identifier,
                'student_name'  => $userName,
                'student_major' => $department
            ]);

            return response()->json([
                'success'      => true,
                'name'         => $userName,
                'redirect_url' => route('dashboard')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'NIM atau jurusan tidak dikenali dalam sistem.'
            ], 422);
        }
    }

    public function trackUsage($id)
    {
        $aiTool = AiTool::findOrFail($id);
        $nim = session('student_nim') ?? session('staff_id');

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

    public function closeSession(Request $request)
    {
        return response()->noContent();
    }

    public function logout()
    {
        session()->forget(['user_id', 'student_nim', 'staff_id', 'student_name', 'student_major', 'logged_tools']);
        return redirect('/');
    }
}
