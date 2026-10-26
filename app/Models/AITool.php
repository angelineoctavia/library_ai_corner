<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiTool extends Model
{
    use HasFactory;

    protected $table = 'ai_tools';
    protected $primaryKey = 'ai_id';

    public $timestamps = false;

    protected $fillable = [
        'ai_name',
        'ai_url',
        'ai_icon',
        'ai_description',
        'ai_guide',
        'status_del'
    ];

    // Relasi ke Usage Logs (One-to-Many)
    // Log pemakaian menyimpan NAMA tool (kolom ai_tool_name), bukan ai_id,
    // jadi dicocokkan lewat ai_tool_name (di ai_usage_logs) = ai_name (di ai_tools).
    public function usageLogs(): HasMany
    {
        return $this->hasMany(AiUsageLog::class, 'ai_tool_name', 'ai_name');
    }
}