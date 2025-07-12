<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ActivityLoggable;

class TargetKPI extends Model
{
    use HasFactory, ActivityLoggable;

    protected $table = 'target_kpi';

    protected $fillable = [
        'indikator_id',
        'tahun_penilaian_id',
        'user_id',
        'target_tahunan',
        'target_bulanan',
        'polaritas',
        'keterangan',
        'disetujui',
        'disetujui_oleh',
        'disetujui_pada',
    ];

    protected $casts = [
        'target_tahunan' => 'float',
        'target_bulanan' => 'array',
        'disetujui' => 'boolean',
        'disetujui_pada' => 'datetime',
    ];

    /**
     * Relasi ke Indikator
     */
    public function indikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class);
    }

    /**
     * Relasi ke TahunPenilaian
     */
    public function tahunPenilaian(): BelongsTo
    {
        return $this->belongsTo(TahunPenilaian::class);
    }

    /**
     * Relasi ke User yang input target
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }



    /**
     * Mendapatkan target untuk bulan tertentu (1-12)
     */
    public function getTargetBulan(int $bulan): float
    {
        if ($this->target_bulanan && isset($this->target_bulanan[$bulan])) {
            return (float) $this->target_bulanan[$bulan];
        }

        // Jika tidak tersedia, fallback ke rata-rata
        return $this->target_tahunan > 0 ? ($this->target_tahunan / 12) : 0;
    }

    /**
     * Menghitung persentase pencapaian target
     */
    public function hitungPersentasePencapaian(float $nilai, int $bulan = null): float
    {
        $target = $bulan ? $this->getTargetBulan($bulan) : $this->target_tahunan;

        if ($target <= 0) {
            return 0;
        }

        return min(110, ($nilai / $target) * 100);
    }

    /**
     * Mendapatkan judul untuk log aktivitas
     */
    public function getActivityLogTitle(): string
    {
        $tahun = $this->tahunPenilaian ? $this->tahunPenilaian->tahun : 'unknown';
        $indikator = $this->indikator ? $this->indikator->nama : 'unknown indikator';

        return 'Target KPI ' . $indikator . ' Tahun ' . $tahun;
    }

    /**
     * Mendapatkan target tahunan (dari target Desember)
     */
    public function getTargetTahunan(): float
    {
        if (!$this->target_bulanan || !is_array($this->target_bulanan)) {
            return 0;
        }

        // Target tahunan diambil dari target bulan Desember (index 11)
        return $this->target_bulanan[11] ?? 0;
    }

    /**
     * Mendapatkan total target tahunan kumulatif (penjumlahan semua bulan)
     */
    public function getTargetTahunanKumulatif(): float
    {
        if (!$this->target_bulanan || !is_array($this->target_bulanan)) {
            return 0;
        }

        return array_sum($this->target_bulanan);
    }

}
