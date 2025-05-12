<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nilai_kpi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('indikators')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users'); // User yang input
            $table->integer('tahun');       // Tahun penilaian
            $table->integer('bulan');       // Bulan penilaian (1-12)
            $table->integer('minggu')->nullable(); // Minggu penilaian (opsional, 1-5)
            $table->enum('periode_tipe', ['bulanan', 'mingguan'])->default('bulanan'); // Tipe periode
            $table->decimal('nilai', 8, 2); // Nilai KPI yang diinput
            $table->decimal('persentase', 5, 2); // Persentase pencapaian
            $table->text('keterangan')->nullable(); // Keterangan tambahan
            $table->boolean('diverifikasi')->default(false); // Status verifikasi
            $table->foreignId('verifikasi_oleh')->nullable()->constrained('users'); // User yang verifikasi
            $table->timestamp('verifikasi_pada')->nullable(); // Waktu verifikasi
            $table->timestamps();

            // Kombinasi unik untuk mencegah duplikasi data
            $table->unique(['indikator_id', 'tahun', 'bulan', 'minggu', 'periode_tipe']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_kpi');
    }
};
