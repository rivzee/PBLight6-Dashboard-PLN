<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pilar extends Model
{
    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'urutan',
    ];

    /**
     * Mendapatkan semua indikator yang termasuk dalam pilar ini
     */
    public function indikators(): HasMany
    {
        return $this->hasMany(Indikator::class)->orderBy('urutan');
    }

    /**
     * Mendapatkan total nilai pilar berdasarkan indikator-indikatornya
     *
     * @param int $tahun
     * @param int $bulan
     * @param string $periodeTipe
     * @return float
     */
    public function getNilai(int $tahun, int $bulan, string $periodeTipe = 'bulanan'): float
    {
        $totalBobot = $this->indikators()->sum('bobot');
        if ($totalBobot <= 0) {
            return 0;
        }

        $totalNilaiTertimbang = 0;
        foreach ($this->indikators as $indikator) {
            $nilaiIndikator = $indikator->getNilai($tahun, $bulan, $periodeTipe);
            $totalNilaiTertimbang += ($nilaiIndikator * $indikator->bobot / 100);
        }

        return round($totalNilaiTertimbang, 2);
    }
}
