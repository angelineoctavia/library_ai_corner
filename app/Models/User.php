<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\AIUsageLog;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'users_id'; // Menyesuaikan dengan migration kamu

    public $timestamps = false;

    protected $fillable = [
        'users_nim',
        'users_name',
        'users_department',
        'status_del'
    ];

    // Relasi ke Usage Logs (One-to-Many)
    public function usageLogs()
    {
        return $this->hasMany(AIUsageLog::class, 'student_nim', 'users_nim');
    }
}