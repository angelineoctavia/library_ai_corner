<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    use HasFactory;

    protected $table = 'ai_usage_logs';
    protected $primaryKey = 'usage_logs_id';

    // Nonaktifkan updated_at karena tabel ini tidak butuh update
    const UPDATED_AT = null;

    protected $fillable = [
        'user_email',
        'ai_tool_name',
        'login_session_token',
        'login_at',
        'logout_at',
        'ip_address',
        'location',
    ];
}