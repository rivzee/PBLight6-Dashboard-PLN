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
    Schema::create('tahun_penilaians', function (Blueprint $table) {
        $table->id();
        $table->integer('tahun')->unique();
        $table->string('deskripsi')->nullable();
        $table->date('tanggal_mulai')->nullable();      // ⬅️ Tambah ini
        $table->date('tanggal_selesai')->nullable();    // ⬅️ Tambah ini
        $table->boolean('is_aktif')->default(false);
        $table->boolean('is_locked')->default(false); // ⬅️ Tambahkan ini
        $table->foreignId('dibuat_oleh')->nullable()->constrained('users');
        $table->foreignId('diupdate_oleh')->nullable()->constrained('users');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_penilaians');
    }
};
