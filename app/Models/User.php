<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    // Domain email yang diizinkan login. Dicocokkan PERSIS (bukan "mengandung"),
    // jadi foo@mail.ciputra.ac.id atau foo@ciputra.ac.id.evil.com tetap ditolak.
    public const STUDENT_EMAIL_DOMAIN = 'student.ciputra.ac.id'; // mahasiswa
    public const STAFF_EMAIL_DOMAIN = 'ciputra.ac.id';           // staff / dosen

    // Akun BERSAMA perpus (dipakai buat langganan AI). Nggak boleh dipakai login ke
    // AI Corner, soalnya pemakaiannya nggak bisa dikaitkan ke satu orang.
    // Tambah email lain di sini kalau ada akun bersama lain.
    public const BLOCKED_EMAILS = [
        'library@ciputra.ac.id',
    ];

    protected $table = 'users';
    protected $primaryKey = 'users_id';

    public $timestamps = false;

    protected $fillable = [
        'users_name',
        'email',
        'google_id',
        'users_department',
        'status_del',
    ];

    /**
     * SATU-SATUNYA tempat aturan domain ditulis. Dipakai oleh login,
     * dashboard mahasiswa, dan dashboard admin.
     *
     * Hasil: 'Student' | 'Staff' | 'Other'
     * 'Other' = domain di luar dua domain kampus (nggak boleh login).
     */
    public static function typeFromEmail(?string $email): string
    {
        $email = strtolower(trim((string) $email));

        if (!str_contains($email, '@')) {
            return 'Other';
        }

        // Ambil domain = semua setelah '@' terakhir
        $domain = substr(strrchr($email, '@'), 1);

        return match ($domain) {
            self::STUDENT_EMAIL_DOMAIN => 'Student',
            self::STAFF_EMAIL_DOMAIN   => 'Staff',
            default                    => 'Other',
        };
    }

    /**
     * Apakah ini akun bersama perpus yang dilarang login?
     */
    public static function isBlockedEmail(?string $email): bool
    {
        return in_array(strtolower(trim((string) $email)), self::BLOCKED_EMAILS, true);
    }

    /**
     * Boleh login hanya kalau email @student.ciputra.ac.id ATAU @ciputra.ac.id.
     */
    public static function isAllowedEmail(?string $email): bool
    {
        return self::typeFromEmail($email) !== 'Other';
    }

    // Pemakaian: $user->type  => 'Student' / 'Staff'
    public function getTypeAttribute(): string
    {
        return self::typeFromEmail($this->email);
    }

    // Relasi ke Usage Logs (One-to-Many), nyambung lewat email
    public function usageLogs()
    {
        return $this->hasMany(AiUsageLog::class, 'user_email', 'email');
    }
}