@extends('layouts.app')

@section('title', 'Riwayat KPI')
@section('page_title', 'RIWAYAT KPI')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Riwayat Kinerja</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" action="{{ route('kpi.history') }}">
                        <div class="input-group">
                            <select name="tahun" class="form-control">
                                @for($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}" {{ request('tahun', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            @if(isset($historiData))
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Bidang</th>
                                <th>Jan</th>
                                <th>Feb</th>
                                <th>Mar</th>
                                <th>Apr</th>
                                <th>Mei</th>
                                <th>Jun</th>
                                <th>Jul</th>
                                <th>Agu</th>
                                <th>Sep</th>
                                <th>Okt</th>
                                <th>Nov</th>
                                <th>Des</th>
                                <th>Rata-rata</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historiData as $bidang => $data)
                                <tr>
                                    <td>{{ $bidang }}</td>
                                    @for($i = 1; $i <= 12; $i++)
                                        @php
                                            $nilai = $data[$i] ?? 0;
                                            $class = '';
                                            if($nilai >= 80) $class = 'text-success';
                                            elseif($nilai >= 60) $class = 'text-warning';
                                            elseif($nilai > 0) $class = 'text-danger';
                                        @endphp
                                        <td class="{{ $class }}">{{ $nilai > 0 ? $nilai.'%' : '-' }}</td>
                                    @endfor
                                    <td class="font-weight-bold">{{ number_format($data['avg'], 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Tidak ada data riwayat KPI untuk ditampilkan.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
