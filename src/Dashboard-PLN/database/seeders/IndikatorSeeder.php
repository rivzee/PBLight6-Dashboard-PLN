<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Indikator;
use App\Models\Pilar;
use App\Models\Bidang;

class IndikatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mendapatkan referensi pilar dan bidang
        $pilarA = Pilar::where('kode', 'A')->first();
        $pilarB = Pilar::where('kode', 'B')->first();
        $pilarC = Pilar::where('kode', 'C')->first();
        $pilarD = Pilar::where('kode', 'D')->first();
        $pilarE = Pilar::where('kode', 'E')->first();
        $pilarF = Pilar::where('kode', 'F')->first();

        $bidangKeu = Bidang::where('kode', 'KEU')->first();
        $bidangPop = Bidang::where('kode', 'POP')->first();
        $bidangSek = Bidang::where('kode', 'SEK')->first();
        $bidangPkr = Bidang::where('kode', 'PKR')->first();
        $bidangMrk = Bidang::where('kode', 'MRK')->first();
        $bidangPbn = Bidang::where('kode', 'PBN')->first();
        $bidangHcm = Bidang::where('kode', 'HCM')->first();
        $bidangK3l = Bidang::where('kode', 'K3L')->first();
        $bidangSPI = Bidang::where('kode', 'SPI')->first();

        // Indikator untuk Pilar A (Nilai Ekonomi dan Sosial)
        $indikatorsA = [
            // Bidang Keuangan (A1-A3)
            [
                'pilar_id' => $pilarA->id,
                'bidang_id' => $bidangKeu->id,
                'kode' => 'A1',
                'nama' => 'EBITDA',
                'deskripsi' => 'Earnings Before Interest, Taxes, Depreciation, and Amortization',
                'bobot' => 8,
                'urutan' => 1,
            ],
            [
                'pilar_id' => $pilarA->id,
                'bidang_id' => $bidangKeu->id,
                'kode' => 'A2',
                'nama' => 'Operating Ratio',
                'deskripsi' => 'Rasio Operasional',
                'bobot' => 8,
                'urutan' => 2,
            ],
            [
                'pilar_id' => $pilarA->id,
                'bidang_id' => $bidangKeu->id,
                'kode' => 'A3',
                'nama' => 'ROIC',
                'deskripsi' => 'Return on Invested Capital',
                'bobot' => 5,
                'urutan' => 3,
            ],

            // Bidang Perencanaan Operasi (A4-A6)
            [
                'pilar_id' => $pilarA->id,
                'bidang_id' => $bidangPop->id,
                'kode' => 'A4',
                'nama' => 'GWh Penjualan Tenaga Listrik',
                'deskripsi' => '',
                'bobot' => 9,
                'urutan' => 4,
            ],
            [
                'pilar_id' => $pilarA->id,
                'bidang_id' => $bidangPop->id,
                'kode' => 'A5',
                'nama' => 'Jumlah Produksi Uap yang dihasilkan',
                'deskripsi' => '',
                'bobot' => 9,
                'urutan' => 5,
            ],
            [
                'pilar_id' => $pilarA->id,
                'bidang_id' => $bidangPop->id,
                'kode' => 'A6',
                'nama' => 'EAF',
                'deskripsi' => 'Equivalent Availabiltiy Factor',
                'bobot' => 10,
                'urutan' => 6,
            ],

            // Bidang Sekretaris Perusahaan (A7-A8)
            [
                'pilar_id' => $pilarA->id,
                'bidang_id' => $bidangSek->id,
                'kode' => 'A7',
                'nama' => 'Pengelolaan Komunikasi & TJSL',
                'deskripsi' => '',
                'bobot' => 4,
                'urutan' => 7,
            ],
            [
                'pilar_id' => $pilarA->id,
                'bidang_id' => $bidangSek->id,
                'kode' => 'A8',
                'nama' => 'Maturity Level Sustainability',
                'deskripsi' => '',
                'bobot' => 4,
                'urutan' => 8,
            ],

            // Bidang Perencanaan Korporat (A9)
            [
                'pilar_id' => $pilarA->id,
                'bidang_id' => $bidangPkr->id,
                'kode' => 'A9',
                'nama' => 'Penyelesaian RJPP',
                'deskripsi' => '',
                'bobot' => 2,
                'urutan' => 9,
            ],
        ];

        // Indikator untuk Pilar B (Inovasi Model Bisnis)
        $indikatorsB = [
            // Bidang Manajemen Risiko (B1)
            [
                'pilar_id' => $pilarB->id,
                'bidang_id' => $bidangMrk->id,
                'kode' => 'B1',
                'nama' => 'Implementasi Roadmap Perbaikan',
                'deskripsi' => 'Implementasi Roadmap Perbaikan Penerapan Manajemen Risiko SH/AP',
                'bobot' => 4,
                'urutan' => 1,
            ],

            // Bidang Pengembangan Bisnis (B2)
            [
                'pilar_id' => $pilarB->id,
                'bidang_id' => $bidangPbn->id,
                'kode' => 'B2',
                'nama' => 'Pendapatan luar PLN',
                'deskripsi' => 'Pendapatan dari luar PLN Group (beyond kWh ) ',
                'bobot' => 6,
                'urutan' => 2,
            ],

            // Bidang Perencanaan Korporat (B3)
            [
                'pilar_id' => $pilarB->id,
                'bidang_id' => $bidangPkr->id,
                'kode' => 'B3',
                'nama' => 'Sinergi',
                'deskripsi' => 'Sinergi Antar Sub Holding, Anak Perusahaan Lain & Pusharlis',
                'bobot' => 2,
                'urutan' => 3,
            ],
        ];

        // Indikator untuk Pilar C (Kepemimpinan Teknologi)
        $indikatorsC = [
            // Bidang Pengembangan Bisnis (C1)
            [
                'pilar_id' => $pilarC->id,
                'bidang_id' => $bidangPbn->id,
                'kode' => 'C1',
                'nama' => 'Penambahan Kontrak Baru di Luar PLN Group',
                'deskripsi' => '',
                'bobot' => 10,
                'urutan' => 1,
            ],
        ];

        // Indikator untuk Pilar D (Peningkatan Investasi)
        $indikatorsD = [
            // Bidang Keuangan (D1)
            [
                'pilar_id' => $pilarD->id,
                'bidang_id' => $bidangKeu->id,
                'kode' => 'D1',
                'nama' => 'Pengendalian Penggunaan Anggara',
                'deskripsi' => 'Pengendalian penggunaan Anggaran Investasi sesuai RKAP 2024',
                'bobot' => 5,
                'urutan' => 1,
            ],
        ];

        // Indikator untuk Pilar E (Pengembangan Talenta)
        $indikatorsE = [
            //Bidang Human Capital
            [
                'pilar_id' => $pilarE->id,
                'bidang_id' => $bidangHcm->id,
                'kode' => 'E1',
                'nama' => 'Rasio Talent Milenial',
                'deskripsi' => 'Persentase tenaga kerja milenial terhadap total tenaga kerja di organisasi.',
                'bobot' => 2,
                'urutan' => 1,
            ],
            [
                'pilar_id' => $pilarE->id,
                'bidang_id' => $bidangHcm->id,
                'kode' => 'E2',
                'nama' => 'Rasio Talent Perempuan',
                'deskripsi' => 'Persentase tenaga kerja perempuan terhadap total tenaga kerja di organisasi.',
                'bobot' => 2,
                'urutan' => 2,
            ],
            [
                'pilar_id' => $pilarE->id,
                'bidang_id' => $bidangHcm->id,
                'kode' => 'E3',
                'nama' => 'Rasio Pemenuhan Kualifikasi Organ Pengelola Risiko SH/AP',
                'deskripsi' => 'Persentase pemenuhan kualifikasi kompetensi oleh anggota Organ Pengelola Risiko Sumber Daya Manusia (SH) dan Administrasi & Personalia (AP).',
                'bobot' => 2,
                'urutan' => 3,
            ],
            [
                'pilar_id' => $pilarE->id,
                'bidang_id' => $bidangHcm->id,
                'kode' => 'E4',
                'nama' => 'Human Capital Readiness (HCR) & Organizational Capital Readiness (OCR) dan Produktivitas Pegawai',
                'deskripsi' => 'Indeks kesiapan sumber daya manusia (HCR) dan kesiapan modal organisasi (OCR) serta tingkat produktivitas pegawai dalam organisasi.',
                'bobot' => 2,
                'urutan' => 4,
            ],
             // Bidang K3L (E5)
            [
                'pilar_id' => $pilarE->id,
                'bidang_id' => $bidangK3l->id,
                'kode' => 'E5',
                'nama' => 'Nihil Kecelakaan',
                'deskripsi' => 'Indeks Kesehatan dan Keselamatan Kerja',
                'bobot' => 2,
                'urutan' => 5,
            ],
        ];


        // Indikator untuk Pilar F (Kepatuhan)
        $indikatorsF = [
            // Bidang Sekretaris Perusahaan (F1)
            [
                'pilar_id' => $pilarF->id,
                'bidang_id' => $bidangSek->id,
                'kode' => 'F1',
                'nama' => 'Implementasi GCG',
                'deskripsi' => 'Indeks Good Corporate Governance',
                'bobot' => 4,
                'urutan' => 1,
            ],

            // Bidang Perencanaan Korporat (F2)
            [
                'pilar_id' => $pilarF->id,
                'bidang_id' => $bidangSPI->id,
                'kode' => 'F2',
                'nama' => 'Compliance',
                'deskripsi' => 'Compliance (Zero Fatality, Kepatuhan K3L, SPI, Reporting, Busdev Alignment)',
                'bobot' => 10,
                'urutan' => 2,
            ],
        ];

        // Menggabungkan semua indikator
        $indikators = array_merge(
            $indikatorsA,
            $indikatorsB,
            $indikatorsC,
            $indikatorsD,
            $indikatorsE,
            $indikatorsF
        );

        // Menyimpan semua indikator ke database
        foreach ($indikators as $indikator) {
            Indikator::create($indikator);
        }
    }
}
