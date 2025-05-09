<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiKPI extends Model
{
    protected $table = 'nilai_kpi';

    protected $fillable = [
        'indikator_id',
        'user_id',
        'tahun',
        'bulan',
        'minggu',
        'periode_tipe',
        'nilai',
        'persentase',
        'keterangan',
        'diverifikasi',
        'verifikasi_oleh',
        'verifikasi_pada',
    ];

    protected $casts = [
        'diverifikasi' => 'boolean',
        'verifikasi_pada' => 'datetime',
        'nilai' => 'float',
        'persentase' => 'float',
    ];

    /**
     * Mendapatkan indikator terkait nilai ini
     */
    public function indikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class);
    }

    /**
     * Mendapatkan user yang menginput nilai
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan user yang memverifikasi nilai
     */
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikasi_oleh');
    }

    /**
     * Scope untuk memfilter berdasarkan periode tertentu
     */
    public function scopePeriode($query, int $tahun, int $bulan = null, string $periodeTipe = null)
    {
        $query->where('tahun', $tahun);

        if ($bulan !== null) {
            $query->where('bulan', $bulan);
        }

        if ($periodeTipe !== null) {
            $query->where('periode_tipe', $periodeTipe);
        }

        return $query;
    }

    /**
     * Mendapatkan nama bulan dari angka bulan
     */
    public function getNamaBulanAttribute(): string
    {
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return $namaBulan[$this->bulan] ?? '';
    }
}
