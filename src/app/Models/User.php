<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang boleh diisi massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Atribut yang disembunyikan saat array atau JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus dikonversi tipe datanya.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Nilai KPI yang diinput oleh user ini
     */
    public function nilaiKPIs(): HasMany
    {
        return $this->hasMany(NilaiKPI::class);
    }

    /**
     * Nilai KPI yang diverifikasi oleh user ini
     */
    public function verifiedNilaiKPIs(): HasMany
    {
        return $this->hasMany(NilaiKPI::class, 'verifikasi_oleh');
    }

    /**
     * Cek apakah user adalah master admin (asisten manajer)
     */
    public function isMasterAdmin(): bool
    {
        return $this->role === 'asisten_manager';
    }

    /**
     * Cek apakah user adalah admin (PIC bidang)
     */
    public function isAdmin(): bool
    {
        return strpos($this->role, 'pic_') === 0;
    }

    /**
     * Cek apakah user adalah karyawan biasa
     */
    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }

    /**
     * Mendapatkan bidang yang dikelola oleh PIC ini
     */
    public function getBidang()
    {
        if (!$this->isAdmin()) {
            return null;
        }

        return Bidang::where('role_pic', $this->role)->first();
    }

    /**
     * Mendapatkan indikator yang menjadi tanggung jawab PIC ini
     */
    public function getIndikators()
    {
        $bidang = $this->getBidang();
        if (!$bidang) {
            return collect();
        }

        return $bidang->indikators;
    }
}
