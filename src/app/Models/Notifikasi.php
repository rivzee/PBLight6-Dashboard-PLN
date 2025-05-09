<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'jenis', // info, success, warning, danger
        'link',
        'dibaca',
        'dibaca_pada',
    ];

    /**
     * Atribut yang harus dikonversi tipe datanya
     *
     * @var array
     */
    protected $casts = [
        'dibaca' => 'boolean',
        'dibaca_pada' => 'datetime',
    ];

    /**
     * User yang menerima notifikasi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Menandai notifikasi sudah dibaca
     */
    public function tandaiDibaca()
    {
        $this->update([
            'dibaca' => true,
            'dibaca_pada' => now(),
        ]);
    }

    /**
     * Mengirim notifikasi ke user tertentu
     *
     * @param int $userId User ID penerima notifikasi
     * @param string $judul Judul notifikasi
     * @param string $pesan Isi pesan notifikasi
     * @param string $jenis Jenis notifikasi (info, success, warning, danger)
     * @param string|null $link Link terkait notifikasi
     * @return Notifikasi
     */
    public static function kirim($userId, $judul, $pesan, $jenis = 'info', $link = null)
    {
        return self::create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'jenis' => $jenis,
            'link' => $link,
            'dibaca' => false,
        ]);
    }

    /**
     * Kirim notifikasi ke semua admin
     *
     * @param string $judul Judul notifikasi
     * @param string $pesan Isi pesan notifikasi
     * @param string $jenis Jenis notifikasi (info, success, warning, danger)
     * @param string|null $link Link terkait notifikasi
     * @return void
     */
    public static function kirimKeAdmin($judul, $pesan, $jenis = 'info', $link = null)
    {
        $admins = User::where('role', 'like', 'pic_%')->get();

        foreach ($admins as $admin) {
            self::kirim($admin->id, $judul, $pesan, $jenis, $link);
        }
    }

    /**
     * Kirim notifikasi ke master admin
     *
     * @param string $judul Judul notifikasi
     * @param string $pesan Isi pesan notifikasi
     * @param string $jenis Jenis notifikasi (info, success, warning, danger)
     * @param string|null $link Link terkait notifikasi
     * @return void
     */
    public static function kirimKeMasterAdmin($judul, $pesan, $jenis = 'info', $link = null)
    {
        $masterAdmin = User::where('role', 'asisten_manager')->first();

        if ($masterAdmin) {
            self::kirim($masterAdmin->id, $judul, $pesan, $jenis, $link);
        }
    }
}
