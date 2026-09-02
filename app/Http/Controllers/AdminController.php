<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiUsageLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\AiTool;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // GANTI INI kalau nama tabel admin kamu ternyata bukan 'admins'
    const ADMIN_TABLE = 'admins';

    public function dashboard(Request $request)
    {
        if (!session()->has('is_admin')) {
            return redirect()->route('admin.login');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = AiUsageLog::where('ai_usage_logs.status_del', '0')
            ->join('users', 'ai_usage_logs.student_nim', '=', 'users.users_nim');

        if ($startDate && $endDate) {
            $query->whereBetween('ai_usage_logs.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        // 1. KPI: AI Terpopuler Hari Ini
        $aiPopulerHariIni = AiUsageLog::where('status_del', '0')
            ->whereDate('created_at', Carbon::today())
            ->select('ai_tool_name', DB::raw('count(*) as total'))
            ->groupBy('ai_tool_name')
            ->orderByDesc('total')
            ->first()->ai_tool_name ?? '-';

        // 2. KPI: AI Paling Favorit (Sesuai Filter)
        $aiFavorit = (clone $query)->select('ai_tool_name', DB::raw('count(*) as total'))
            ->groupBy('ai_tool_name')
            ->orderByDesc('total')
            ->first()->ai_tool_name ?? '-';

        // 3. KPI: Jurusan Terbanyak
        $jurusanTerbanyak = (clone $query)->select('users.users_major', DB::raw('count(*) as total'))
            ->groupBy('users.users_major')
            ->orderByDesc('total')
            ->first()->users_major ?? '-';

        // 4. Data Line Chart (Jam Sibuk) — total klik per jam
        $jamSibukData = (clone $query)
            ->select(DB::raw('HOUR(ai_usage_logs.created_at) as hour'), DB::raw('count(*) as total'))
            ->groupBy('hour')
            ->pluck('total', 'hour')->toArray();

        $chartJam = [];
        for ($i = 8; $i <= 20; $i++) {
            $chartJam[] = $jamSibukData[$i] ?? 0;
        }

        // 5. Data Pie Chart (Proporsi AI)
        $pieDataRaw = (clone $query)->select('ai_tool_name', DB::raw('count(*) as total'))
            ->groupBy('ai_tool_name')
            ->get();
        $pieLabels = $pieDataRaw->pluck('ai_tool_name')->toArray();
        $pieValues = $pieDataRaw->pluck('total')->toArray();

        // 6. Tabel Riwayat Akses
        $riwayat = (clone $query)->select('users.users_nim', 'users.users_major', 'ai_usage_logs.created_at', 'ai_usage_logs.ai_tool_name')
            ->orderByDesc('ai_usage_logs.created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'startDate',
            'endDate',
            'aiPopulerHariIni',
            'aiFavorit',
            'jurusanTerbanyak',
            'chartJam',
            'pieLabels',
            'pieValues',
            'riwayat'
        ));
    }

    public function exportExcel(Request $request)
    {
        if (!session()->has('is_admin')) {
            return redirect()->route('admin.login');
        }

        $exportStart = $request->query('start_date');
        $exportEnd = $request->query('end_date');

        if ($exportStart && $exportEnd && $exportStart > $exportEnd) {
            return redirect()->back()->withErrors(['export_error' => 'Tanggal "Sampai" tidak boleh lebih awal dari tanggal "Dari"!']);
        }

        $query = AiUsageLog::where('ai_usage_logs.status_del', '0')
            ->join('users', 'ai_usage_logs.student_nim', '=', 'users.users_nim')
            ->select('ai_usage_logs.*', 'users.users_major');

        if ($exportStart && $exportEnd) {
            $query->whereBetween('ai_usage_logs.created_at', [$exportStart . ' 00:00:00', $exportEnd . ' 23:59:59']);
        } elseif ($exportStart) {
            $query->whereDate('ai_usage_logs.created_at', '>=', $exportStart);
        } elseif ($exportEnd) {
            $query->whereDate('ai_usage_logs.created_at', '<=', $exportEnd);
        }

        $logs = $query->orderBy('ai_usage_logs.created_at', 'desc')->get();
        $totalAllUsage = $logs->count();

        $spreadsheet = new Spreadsheet();
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF9F43']],
            'alignment' => ['horizontal' => 'center'],
        ];

        // ===== SHEET 1: Riwayat Penggunaan AI =====
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Riwayat Penggunaan');

        $sheet1->fromArray(
            ['No', 'NIM Mahasiswa', 'Jurusan', 'Nama AI Tool', 'Waktu Akses'],
            null,
            'A1'
        );
        $sheet1->getStyle('A1:E1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($logs as $index => $log) {
            $sheet1->fromArray([
                $index + 1,
                $log->student_nim,
                $log->users_major,
                $log->ai_tool_name,
                $log->created_at,
            ], null, 'A' . $row);
            $row++;
        }
        $sheet1->setAutoFilter('A1:E' . max(1, $row - 1));
        foreach (range('A', 'E') as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }
        if ($row > 2) {
            $sheet1->getStyle('A1:E' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // ===== SHEET 2: Ringkasan Statistik =====
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Ringkasan Statistik');

        // --- Tabel 1: Statistik & Proporsi Berdasarkan AI Tool ---
        $sheet2->fromArray(['Nama AI Tool', 'Total Penggunaan', 'Proporsi (%)', 'Jumlah User Unik'], null, 'A1');
        $sheet2->getStyle('A1:D1')->applyFromArray($headerStyle);

        $aiSummary = $logs->groupBy('ai_tool_name');
        $rowAi = 2;
        foreach ($aiSummary as $aiName => $items) {
            $total = $items->count();
            $percentage = $totalAllUsage > 0 ? round(($total / $totalAllUsage) * 100, 2) . '%' : '0%';
            $uniqueUsers = $items->unique('student_nim')->count();

            $sheet2->fromArray([$aiName, $total, $percentage, $uniqueUsers], null, 'A' . $rowAi);
            $rowAi++;
        }
        if ($rowAi > 2) {
            $sheet2->setAutoFilter('A1:D' . ($rowAi - 1));
            $sheet2->getStyle('A1:D' . ($rowAi - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // --- Tabel 2: Berdasarkan Jurusan (Kolom F - G) ---
        $sheet2->fromArray(['Jurusan', 'Total Penggunaan'], null, 'F1');
        $sheet2->getStyle('F1:G1')->applyFromArray($headerStyle);

        $deptSummary = $logs->groupBy('users_major')->map->count();
        $rowDept = 2;
        foreach ($deptSummary as $dept => $total) {
            $sheet2->fromArray([$dept ?: 'Lainnya', $total], null, 'F' . $rowDept);
            $rowDept++;
        }
        if ($rowDept > 2) {
            $sheet2->setAutoFilter('F1:G' . ($rowDept - 1));
            $sheet2->getStyle('F1:G' . ($rowDept - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        foreach (range('A', 'G') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);
        $fileSuffix = ($exportStart || $exportEnd)
            ? ($exportStart ?? 'awal') . '_sd_' . ($exportEnd ?? 'akhir')
            : now()->format('Y-m-d_His');
        $filename = 'Laporan_AI_Corner_' . $fileSuffix . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function controlPanel()
    {
        if (!session()->has('is_admin')) {
            return redirect()->route('admin.login');
        }

        $aiTools = AiTool::where('status_del', '0')->get();
        $inactiveTools = AiTool::where('status_del', '1')->get();

        return view('admin.control_panel', compact('aiTools', 'inactiveTools'));
    }

    public function destroy($id)
    {
        if (!session()->has('is_admin')) {
            return redirect()->route('admin.login');
        }

        $aiTool = AiTool::findOrFail($id);
        $aiTool->update(['status_del' => 1]);

        return redirect()->back()->with('success', 'AI berhasil disembunyikan.');
    }

    public function restore($id)
    {
        if (!session()->has('is_admin')) {
            return redirect()->route('admin.login');
        }

        $aiTool = AiTool::findOrFail($id);
        $aiTool->update(['status_del' => '0']);

        return redirect()->back()->with('success', 'AI Tool berhasil dipulihkan.');
    }

    public function store(Request $request)
    {
        if (!session()->has('is_admin')) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'ai_name' => 'required|string|max:255',
            'ai_url' => 'required|string|max:255',
            'ai_icon' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $iconPath = null;
        if ($request->hasFile('ai_icon')) {
            $iconPath = $request->file('ai_icon')->store('ai_icons', 'public');
        }

        AiTool::create([
            'ai_name' => $request->ai_name,
            'ai_url' => $request->ai_url,
            'ai_icon' => $iconPath,
            'status_del' => '0',
        ]);

        return redirect()->back()->with('success', 'AI Tool berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        if (!session()->has('is_admin')) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'ai_name' => 'required|string|max:255',
            'ai_url' => 'required|string|max:255',
            'ai_icon' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $aiTool = AiTool::findOrFail($id);

        $data = [
            'ai_name' => $request->ai_name,
            'ai_url' => $request->ai_url,
        ];

        if ($request->hasFile('ai_icon')) {
            $data['ai_icon'] = $request->file('ai_icon')->store('ai_icons', 'public');
        }

        $aiTool->update($data);

        return redirect()->back()->with('success', 'AI Tool berhasil diperbarui!');
    }

    public function index()
    {
        $aiTools = AiTool::where('status_del', '0')->get();
        $inactiveTools = AiTool::where('status_del', '1')->get();

        return view('admin.control_panel', compact('aiTools', 'inactiveTools'));
    }

    public function showAdminLogin()
    {
        if (session()->has('is_admin')) {
            return redirect()->route('admin.control.panel');
        }

        return view('admin.login');
    }

    public function processAdminLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $admin = DB::table(self::ADMIN_TABLE)
            ->where('username', $credentials['username'])
            ->where('status_del', '0')
            ->first();

        if ($admin && Hash::check($credentials['password'], $admin->password)) {
            $request->session()->regenerate();
            session()->put('is_admin', true);
            session()->put('admin_id', $admin->admin_id);
            session()->put('admin_username', $admin->username);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function adminLogout(Request $request)
    {
        $request->session()->forget(['is_admin', 'admin_id', 'admin_username']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}