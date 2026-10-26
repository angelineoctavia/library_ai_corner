<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use App\Models\AiTool;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    const ADMIN_TABLE = 'admins';

    public function dashboard()
    {
        if (!session()->has('is_admin')) {
            return redirect()->route('admin.login');
        }

        // Semua baris dikirim SEKALI ke view. Filter tanggal, filter kolom,
        // sort, KPI, dan chart dikerjakan di browser (JS).
        $rows = $this->getAccessRows()->map(fn($r) => [
            'email'      => $r->email,
            'type'       => $r->type, // 'Student' / 'Staff'
            'tool'       => $r->ai_tool_name,
            'created_at' => $r->created_at,
            'login_at'   => $r->login_at,
            'logout_at'  => $r->logout_at,
            'session'    => $r->login_session_token,
            'ip'         => $r->ip_address,
            'location'   => $r->location, // boleh null (belum ditentukan PC-nya)
        ])->values();

        // Mengambil data feedback untuk ditampilkan di tab/tabel dashboard admin
        $feedbacks = DB::table('feedbacks')
            ->orderByDesc('created_at')
            ->get(['user_email as email', 'category', 'message', 'created_at']);

        return view('admin.dashboard', compact('rows', 'feedbacks'));
    }

    private function getAccessRows(?string $startDate = null, ?string $endDate = null)
    {
        $query = DB::table(DB::raw("(
            SELECT
                user_email,
                ai_tool_name,
                MIN(created_at) AS created_at,
                MIN(login_at)   AS login_at,
                MAX(logout_at)  AS logout_at,
                MIN(login_session_token) AS login_session_token,
                MIN(ip_address) AS ip_address,
                MIN(location)   AS location
            FROM ai_usage_logs
            WHERE COALESCE(status_del, '0') = '0'
            GROUP BY
                user_email,
                ai_tool_name,
                COALESCE(login_session_token, CONCAT('legacy-', DATE(created_at)))
        ) AS deduped_logs"))
            ->join('users', 'deduped_logs.user_email', '=', 'users.email');

        if ($startDate) {
            $query->where('deduped_logs.created_at', '>=', $startDate . ' 00:00:00');
        }
        if ($endDate) {
            $query->where('deduped_logs.created_at', '<=', $endDate . ' 23:59:59');
        }

        return $query
            ->select(
                'users.email',
                'deduped_logs.ai_tool_name',
                'deduped_logs.created_at',
                'deduped_logs.login_at',
                'deduped_logs.logout_at',
                'deduped_logs.login_session_token',
                'deduped_logs.ip_address',
                'deduped_logs.location'
            )
            ->orderByDesc('deduped_logs.created_at')
            ->get()
            ->map(function ($r) {
                // Mendapatkan type user langsung dari domain email via method Model User
                $r->type = User::typeFromEmail($r->email);
                return $r;
            });
    }

    public function exportExcel(Request $request)
    {
        if (!session()->has('is_admin')) {
            return redirect()->route('admin.login');
        }

        $start = $request->query('start_date') ?: null;
        $end = $request->query('end_date') ?: null;

        if ($start && $end && $start > $end) {
            return redirect()->back()->withErrors(['export_error' => 'The "To" date cannot be earlier than the "From" date!']);
        }

        $rows = $this->getAccessRows($start, $end);

        $generatedAt = now()->format('Y-m-d H:i');
        $periodLabel = $this->periodLabel($start, $end);

        $spreadsheet = new Spreadsheet();
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FF9F43']],
            'alignment' => ['horizontal' => 'center'],
        ];
        $headerRow = 6;

        // =====================================================
        // SHEET 1: Access History
        // =====================================================
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Access History');
        $this->writeSheetMeta(
            $sheet1,
            'AI Corner - Access History Report',
            $generatedAt,
            $periodLabel,
            'Session Start/End = login/logout time. AI Opened At = when that AI tool was opened. Blank = not recorded. Repeated clicks on the same AI tool within one login session are counted once.',
            'J'
        );

        $sheet1->fromArray(
            ['No', 'Email', 'User Type', 'Date', 'Session Start', 'Session End', 'AI Opened At', 'AI Tool', 'IP Address', 'Location'],
            null,
            'A' . $headerRow
        );
        $sheet1->getStyle("A{$headerRow}:J{$headerRow}")->applyFromArray($headerStyle);

        $r = $headerRow + 1;
        foreach ($rows as $i => $log) {
            $created = Carbon::parse($log->created_at);
            $createdSerial = ExcelDate::PHPToExcel($created);

            $sheet1->setCellValue("A{$r}", $i + 1);
            $sheet1->setCellValueExplicit("B{$r}", (string) $log->email, DataType::TYPE_STRING);
            $sheet1->setCellValue("C{$r}", $log->type);
            $sheet1->setCellValue("D{$r}", floor($createdSerial));
            if ($log->login_at) {
                $sheet1->setCellValue("E{$r}", ExcelDate::PHPToExcel(Carbon::parse($log->login_at)));
            }
            if ($log->logout_at) {
                $sheet1->setCellValue("F{$r}", ExcelDate::PHPToExcel(Carbon::parse($log->logout_at)));
            }
            $sheet1->setCellValue("G{$r}", $createdSerial - floor($createdSerial));
            $sheet1->setCellValue("H{$r}", $log->ai_tool_name);
            if ($log->ip_address) {
                $sheet1->setCellValueExplicit("I{$r}", (string) $log->ip_address, DataType::TYPE_STRING);
            }
            if ($log->location) {
                $sheet1->setCellValueExplicit("J{$r}", (string) $log->location, DataType::TYPE_STRING);
            }
            $r++;
        }
        $lastRow1 = max($headerRow, $r - 1);

        if ($lastRow1 > $headerRow) {
            $first = $headerRow + 1;
            $sheet1->getStyle("D{$first}:D{$lastRow1}")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            $sheet1->getStyle("E{$first}:F{$lastRow1}")->getNumberFormat()->setFormatCode('yyyy-mm-dd hh:mm');
            $sheet1->getStyle("G{$first}:G{$lastRow1}")->getNumberFormat()->setFormatCode('hh:mm');
        }
        $this->finalizeSheet($sheet1, $headerRow, 'J', $lastRow1);

        // =====================================================
        // SHEET 2: AI Usage Summary
        // =====================================================
        $totalUsage = $rows->count();
        $aiSummary = $rows->groupBy('ai_tool_name')
            ->map(fn($g, $name) => [
                'name'  => $name,
                'total' => $g->count(),
                'users' => $g->pluck('email')->unique()->count(),
            ])
            ->values()
            ->sortBy([['total', 'desc'], ['name', 'asc']])
            ->values();

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('AI Usage Summary');
        $this->writeSheetMeta(
            $sheet2,
            'AI Corner - AI Usage Summary',
            $generatedAt,
            $periodLabel,
            'Usage is counted once per AI tool per login session.',
            'D'
        );
        $sheet2->fromArray(['AI Tool', 'Total Usage', 'Proportion (%)', 'Unique Users'], null, 'A' . $headerRow);
        $sheet2->getStyle("A{$headerRow}:D{$headerRow}")->applyFromArray($headerStyle);

        $r = $headerRow + 1;
        foreach ($aiSummary as $item) {
            $sheet2->setCellValue("A{$r}", $item['name']);
            $sheet2->setCellValue("B{$r}", $item['total']);
            $sheet2->setCellValue("C{$r}", $totalUsage > 0 ? $item['total'] / $totalUsage : 0);
            $sheet2->setCellValue("D{$r}", $item['users']);
            $r++;
        }
        $lastRow2 = max($headerRow, $r - 1);
        if ($lastRow2 > $headerRow) {
            $first = $headerRow + 1;
            $sheet2->getStyle("C{$first}:C{$lastRow2}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        }
        $this->finalizeSheet($sheet2, $headerRow, 'D', $lastRow2);

        // =====================================================
        // SHEET 3: Feedback Summary
        // =====================================================
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Feedback');
        $this->writeSheetMeta(
            $sheet3,
            'AI Corner - User Feedback & Suggestions',
            $generatedAt,
            $periodLabel,
            'Collection of user suggestions and feedback submitted through AI Corner.',
            'E'
        );

        $sheet3->fromArray(['No', 'Email', 'Type', 'Message', 'Date'], null, 'A' . $headerRow);
        $sheet3->getStyle("A{$headerRow}:E{$headerRow}")->applyFromArray($headerStyle);

        $fbQuery = DB::table('feedbacks')->orderByDesc('created_at');
        if ($start) $fbQuery->whereDate('created_at', '>=', $start);
        if ($end)   $fbQuery->whereDate('created_at', '<=', $end);

        $rowFb = $headerRow + 1;
        foreach ($fbQuery->get() as $i => $f) {
            $sheet3->setCellValue('A' . $rowFb, $i + 1);
            $sheet3->setCellValueExplicit('B' . $rowFb, $f->user_email, DataType::TYPE_STRING);
            $sheet3->setCellValue('C' . $rowFb, $f->category === 'ai_request' ? 'AI Suggestion' : 'Complaint / Feedback');
            $sheet3->setCellValueExplicit('D' . $rowFb, $f->message, DataType::TYPE_STRING);
            $sheet3->setCellValue('E' . $rowFb, $f->created_at);
            $rowFb++;
        }
        $lastRow3 = max($headerRow, $rowFb - 1);
        if ($lastRow3 > $headerRow) {
            $sheet3->getStyle("D" . ($headerRow + 1) . ":D{$lastRow3}")->getAlignment()->setWrapText(true);
        }
        $this->finalizeSheet($sheet3, $headerRow, 'E', $lastRow3);
        $sheet3->getColumnDimension('D')->setWidth(60);

        // =====================================================
        $spreadsheet->setActiveSheetIndex(0);

        $fileSuffix = ($start || $end)
            ? ($start ?: 'start') . '_to_' . ($end ?: 'latest')
            : now()->format('Y-m-d_His');
        $filename = 'AI_Corner_Report_' . $fileSuffix . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function periodLabel(?string $start, ?string $end): string
    {
        if ($start && $end) {
            return "{$start} to {$end}";
        }
        if ($start) {
            return "From {$start} (no end date)";
        }
        if ($end) {
            return "Until {$end} (no start date)";
        }

        return 'All dates (no filter)';
    }

    private function writeSheetMeta($sheet, string $title, string $generatedAt, string $period, string $note, string $lastCol): void
    {
        $lines = [
            1 => $title,
            2 => "Report generated: {$generatedAt}",
            3 => "Period: {$period}",
            4 => $note,
        ];

        foreach ($lines as $row => $text) {
            $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
            $sheet->setCellValue("A{$row}", $text);
        }

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setBold(true);
        $sheet->getStyle('A4')->getFont()->setItalic(true);
        $sheet->getStyle('A4')->getFont()->getColor()->setRGB('666666');
    }

    private function finalizeSheet($sheet, int $headerRow, string $lastCol, int $lastRow): void
    {
        if ($lastRow >= $headerRow) {
            $sheet->setAutoFilter("A{$headerRow}:{$lastCol}{$lastRow}");
            $sheet->getStyle("A{$headerRow}:{$lastCol}{$lastRow}")
                ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
        $sheet->freezePane('A' . ($headerRow + 1));

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
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
            'ai_description' => 'nullable|string|max:255',
            'ai_guide' => 'nullable|string|max:5000',
        ]);

        $iconPath = null;
        if ($request->hasFile('ai_icon')) {
            $iconPath = $request->file('ai_icon')->store('ai_icons', 'public');
        }

        AiTool::forceCreate([
            'ai_name' => $request->ai_name,
            'ai_url' => $request->ai_url,
            'ai_icon' => $iconPath,
            'ai_description' => $request->ai_description,
            'ai_guide' => $request->ai_guide,
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
            'ai_description' => 'nullable|string|max:255',
            'ai_guide' => 'nullable|string|max:5000',
        ]);

        $aiTool = AiTool::findOrFail($id);

        $data = [
            'ai_name' => $request->ai_name,
            'ai_url' => $request->ai_url,
            'ai_description' => $request->ai_description,
            'ai_guide' => $request->ai_guide,
        ];

        if ($request->hasFile('ai_icon')) {
            $data['ai_icon'] = $request->file('ai_icon')->store('ai_icons', 'public');
        }

        $aiTool->forceFill($data)->save();

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