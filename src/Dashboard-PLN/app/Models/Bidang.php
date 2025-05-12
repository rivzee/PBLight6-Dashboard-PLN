<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bidang extends Model
{
    protected $fillable = [
        'nama',
        'kode',
        'role_pic',
        'deskripsi',
    ];

    /**
     * Mendapatkan semua indikator yang terkait dengan bidang ini
     */
    public function indikators(): HasMany
    {
        return $this->hasMany(Indikator::class);
    }

    /**
     * Mendapatkan PIC user dari bidang ini
     *
     * @return \App\Models\User|null
     */
    public function getPICUser()
    {
        return User::where('role', $this->role_pic)->first();
    }

    /**
     * Mendapatkan nilai rata-rata KPI bidang untuk periode tertentu
     *
     * @param int $tahun
     * @param int $bulan
     * @param string $periodeTipe
     * @return float
     */
    public function getNilaiRata(int $tahun, int $bulan, string $periodeTipe = 'bulanan'): float
    {
        $indikators = $this->indikators()->where('aktif', true)->get();

        if ($indikators->isEmpty()) {
            return 0;
        }

        $totalNilai = 0;
        foreach ($indikators as $indikator) {
            $totalNilai += $indikator->getNilai($tahun, $bulan, $periodeTipe);
        }

        return round($totalNilai / $indikators->count(), 2);
    }
}
