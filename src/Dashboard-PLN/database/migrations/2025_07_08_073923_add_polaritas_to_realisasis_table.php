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
        Schema::table('realisasis', function (Blueprint $table) {
            $table->string('jenis_polaritas')->default('positif')->after('persentase')->comment('positif atau negatif');
            $table->decimal('nilai_polaritas', 8, 2)->nullable()->after('jenis_polaritas')->comment('Hasil perhitungan polaritas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasis', function (Blueprint $table) {
            $table->dropColumn(['jenis_polaritas', 'nilai_polaritas']);
        });
    }
};
