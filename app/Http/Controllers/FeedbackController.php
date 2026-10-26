<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        // Feedback butuh email (penanda user), jadi harus sudah login
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Login session expired. Please login again.',
            ], 401);
        }

        $data = $request->validate([
            'category' => ['required', 'in:ai_request,complaint'],
            'message'  => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'message.required' => 'Pesan tidak boleh kosong.',
            'message.min'      => 'Message too short (5 character minimal).',
            'message.max'      => 'Message too long (1000 character maximal).',
        ]);

        DB::table('feedbacks')->insert([
            'user_email' => $user->email,
            'category'   => $data['category'],
            'message'    => $data['message'],
            'created_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}