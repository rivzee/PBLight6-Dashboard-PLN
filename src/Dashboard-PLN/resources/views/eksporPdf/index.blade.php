@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/pdf.css') }}">
@endsection


@section('title', 'Ekspor Laporan PDF')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="h3 mb-0 text-gray-800">Ekspor Laporan PDF</h1>
                <p class="text-muted">Ekspor laporan kinerja dalam format PDF berdasarkan kriteria yang dipilih</p>
            </div>
        </div>

        @include('components.alert')



        <!-- Ekspor Keseluruhan -->
        <div class="col-md-4">
            <div class="form-section">
                <h4><i class="fas fa-chart-line mr-2"></i> Laporan Keseluruhan</h4>
                <form action="{{ route('eksporPdf.keseluruhan') }}" method="POST" target="_blank">
                    @csrf

                    <div class="form-group">
                        <label for="tahun">Tahun</label>
                        <select name="tahun" id="tahun" class="form-control" required>
                            @for ($i = date('Y'); $i >= 2020; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bulan">Bulan</label>
                        <select name="bulan" id="bulan" class="form-control" required>
                            @php
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
                                    12 => 'Desember',
                                ];
                            @endphp
                            @foreach ($namaBulan as $key => $bulan)
                                <option value="{{ $key }}">{{ $bulan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-pdf mr-2"></i> Ekspor PDF
                    </button>
                </form>

            </div>
        </div>

    </div>
    </div>
@endsection
