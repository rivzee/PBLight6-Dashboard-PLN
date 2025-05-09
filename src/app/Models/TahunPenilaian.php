<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunPenilaian extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini
     *
     * @var string
     */
    protected $table = 'tahun_penilaians';

    /**
     * Atribut yang dapat diisi
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tahun',
        'deskripsi',
        'is_aktif',
        'dibuat_oleh',
        'diupdate_oleh',
    ];

    /**
     * Atribut yang harus dikonversi tipe datanya
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    /**
     * Mendapatkan user yang membuat tahun penilaian
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Mendapatkan user yang mengupdate tahun penilaian
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'diupdate_oleh');
    }

    /**
     * Mendapatkan tahun aktif
     *
     * @return TahunPenilaian|null
     */
    public static function getActive()
    {
        return self::where('is_aktif', true)->first();
    }

    /**
     * Mendapatkan tahun aktif atau tahun saat ini jika tidak ada yang aktif
     *
     * @return int
     */
    public static function getActiveTahun()
    {
        $active = self::getActive();
        return $active ? $active->tahun : date('Y');
    }
}
