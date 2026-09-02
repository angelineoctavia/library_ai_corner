<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AIUsageLog;

class AiTool extends Model
{
    use HasFactory;

    protected $table = 'ai_tools';
    protected $primaryKey = 'ai_id';

    public $timestamps = false;

    protected $fillable = ['ai_name', 'ai_url', 'ai_icon', 'status_del'];

    // Relasi ke Usage Logs (One-to-Many)
    public function usageLogs()
    {
        return $this->hasMany(AIUsageLog::class, 'ai_tools_name', 'ai_name');
    }
}