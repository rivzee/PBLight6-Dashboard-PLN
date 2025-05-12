<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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
}
