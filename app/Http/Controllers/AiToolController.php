<?php

namespace App\Http\Controllers;

use App\Models\AiTool;
//use Illuminate\Http\Request;

class AiToolController extends Controller
{
    // Menampilkan data yang Aktif (status_del = 0)
    public function controlPanel()
    {
        $aiTools = AiTool::where('status_del', '0')->get();
        $activeTab = 'active'; // Untuk menandai tab aktif di view
        return view('admin.control_panel', compact('aiTools', 'activeTab'));
    }

    // Menampilkan data yang Inactive / Terhapus (status_del = 1)
    public function inactive()
    {
        $aiTools = AiTool::where('status_del', '1')->get();
        $activeTab = 'inactive';
        return view('admin.control_panel', compact('aiTools', 'activeTab'));
    }

    // Mengubah status_del jadi '1' (Soft Delete custom)
    public function destroy($id)
    {
        $aiTool = AiTool::findOrFail($id);
        $aiTool->update(['status_del' => '1']);

        return redirect()->back()->with('success', 'AI Tool moved to inactive.');
    }

    // Mengembalikan status_del jadi '0' (Restore)
    public function restore($id)
    {
        $aiTool = AiTool::findOrFail($id);
        $aiTool->update(['status_del' => '0']);

        return redirect()->back()->with('success', 'AI Tool restored successfully.');
    }
}