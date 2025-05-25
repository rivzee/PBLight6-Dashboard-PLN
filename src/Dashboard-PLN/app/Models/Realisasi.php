<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ActivityLoggable;
use App\Models\User;
use App\Models\Indikator;

class Realisasi extends Model
{
    use HasFactory, ActivityLoggable;

    protected $table = 'realisasis';

    protected $guarded = ['id'];

    protected $fillable = [
        'indikator_id',
        'user_id',
        'tanggal',
        'nilai',
        'persentase',
        'keterangan',
        'diverifikasi',
        'verifikasi_oleh',
        'verifikasi_pada',
        'tahun',      // ditambahkan
        'bulan',      // ditambahkan
        'periode_tipe', // ditambahkan
    ];

    protected $casts = [
        'diverifikasi' => 'boolean',
        'verifikasi_pada' => 'datetime',
        'nilai' => 'float',
        'persentase' => 'float',
        'tanggal' => 'date',
        'tahun' => 'integer',
        'bulan' => 'integer',
    ];

    public function indikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikasi_oleh');
    }

    public function scopeTanggal($query, string $tanggal)
    {
        return $query->where('tanggal', $tanggal);
    }

    public function getNamaTanggalAttribute(): string
    {
        return $this->tanggal ? $this->tanggal->format('d F Y') : '';
    }

    public function getActivityLogTitle()
    {
        $tanggal = $this->tanggal ? $this->tanggal->format('d/m/Y') : '';
        $indikatorNama = $this->indikator ? $this->indikator->nama : '';

        return "Nilai KPI {$indikatorNama} tanggal {$tanggal}";
    }

    public function isVerified()
    {
        return $this->diverifikasi;
    }

    // Boot method untuk mengisi tahun, bulan, dan periode_tipe otomatis saat saving
    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->tanggal) {
                $model->tahun = date('Y', strtotime($model->tanggal));
                $model->bulan = (int) date('n', strtotime($model->tanggal));
            }
            // Isi default periode_tipe jika belum ada
            if (empty($model->periode_tipe)) {
                $model->periode_tipe = 'bulanan';  // default nilai
            }
        });
    }
}
