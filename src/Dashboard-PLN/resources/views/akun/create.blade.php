@extends('layouts.app')

@section('title', 'Tambah Akun Baru - PLN')
@section('page_title', 'TAMBAH AKUN')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/akun.css') }}">
@endsection

@section('content')
<div class="form-container">
    <div class="form-header">
        <h2 class="form-title">Tambah Akun Baru</h2>
        <p class="form-subtitle">Silakan isi form berikut untuk menambahkan akun baru ke sistem</p>
    </div>

    @if ($errors->any())
    <div class="alert-danger mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('akun.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}"
                   placeholder="Masukkan nama lengkap" required>
            @error('name')
                <span class="form-validation-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}"
                   placeholder="contoh@email.com" required>
            @error('email')
                <span class="form-validation-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control"
                   placeholder="Minimal 8 karakter" required>
            @error('password')
                <span class="form-validation-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                   placeholder="Ketik ulang password" required>
            @error('password_confirmation')
                <span class="form-validation-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="role" class="form-label">Peran</label>
            <select id="role" name="role" class="form-control form-select" required>
                <option value="">-- Pilih Peran --</option>
                <option value="asisten_manager" {{ old('role') == 'asisten_manager' ? 'selected' : '' }}>Asisten Manager</option>
                <option value="pic_keuangan" {{ old('role') == 'pic_keuangan' ? 'selected' : '' }}>PIC Bidang Keuangan</option>
                <option value="pic_manajemen_risiko" {{ old('role') == 'pic_manajemen_risiko' ? 'selected' : '' }}>PIC Manajemen Risiko</option>
                <option value="pic_sekretaris_perusahaan" {{ old('role') == 'pic_sekretaris_perusahaan' ? 'selected' : '' }}>PIC Sekretaris Perusahaan</option>
                <option value="pic_perencanaan_operasi" {{ old('role') == 'pic_perencanaan_operasi' ? 'selected' : '' }}>PIC Perencanaan Operasi</option>
                <option value="pic_pengembangan_bisnis" {{ old('role') == 'pic_pengembangan_bisnis' ? 'selected' : '' }}>PIC Pengembangan Bisnis</option>
                <option value="pic_human_capital" {{ old('role') == 'pic_human_capital' ? 'selected' : '' }}>PIC Human Capital</option>
                <option value="pic_k3l" {{ old('role') == 'pic_k3l' ? 'selected' : '' }}>PIC K3L</option>
                <option value="pic_perencanaan_korporat" {{ old('role') == 'pic_perencanaan_korporat' ? 'selected' : '' }}>PIC Perencanaan Korporat</option>
                <option value="karyawan" {{ old('role') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
            </select>
            @error('role')
                <span class="form-validation-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('akun.index') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection
