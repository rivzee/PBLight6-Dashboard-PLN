<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivitasLog extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model
     *
     * @var string
     */
    protected $table = 'aktivitas_logs';

    /**
     * Atribut yang dapat diisi
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'tipe', // login, logout, create, update, delete, verify
        'deskripsi',
        'data', // JSON data
        'ip_address',
        'user_agent',
    ];

    /**
     * Atribut yang harus dikonversi tipe datanya
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * User yang melakukan aktivitas
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log aktivitas login
     *
     * @param User $user User yang login
     * @param string $ipAddress IP address
     * @param string $userAgent User agent
     * @return AktivitasLog
     */
    public static function logLogin($user, $ipAddress = null, $userAgent = null)
    {
        return self::create([
            'user_id' => $user->id,
            'tipe' => 'login',
            'deskripsi' => 'Login ke sistem',
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Log aktivitas logout
     *
     * @param User $user User yang logout
     * @param string $ipAddress IP address
     * @param string $userAgent User agent
     * @return AktivitasLog
     */
    public static function logLogout($user, $ipAddress = null, $userAgent = null)
    {
        return self::create([
            'user_id' => $user->id,
            'tipe' => 'logout',
            'deskripsi' => 'Logout dari sistem',
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Log aktivitas CRUD
     *
     * @param User $user User yang melakukan aktivitas
     * @param string $tipe Tipe aktivitas (create, update, delete, verify)
     * @param string $deskripsi Deskripsi aktivitas
     * @param array $data Data terkait aktivitas
     * @param string $ipAddress IP address
     * @param string $userAgent User agent
     * @return AktivitasLog
     */
    public static function log($user, $tipe, $deskripsi, $data = [], $ipAddress = null, $userAgent = null)
    {
        return self::create([
            'user_id' => $user->id,
            'tipe' => $tipe,
            'deskripsi' => $deskripsi,
            'data' => $data,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
