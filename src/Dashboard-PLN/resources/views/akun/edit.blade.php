@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/akun.css') }}">
@endsection

@section('content')

<div class="custom-form">
    <h2>Edit Akun</h2>

    <form action="{{ route('akun.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name"
                   value="{{ old('name', $user->name) }}"
                   placeholder="Masukkan nama lengkap" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email', $user->email) }}"
                   placeholder="contoh@email.com" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="Biarkan kosong jika tidak diubah">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   placeholder="Biarkan kosong jika tidak diubah">
        </div>

        <div class="form-group">
            <label for="role">Peran</label>
            <select id="role" name="role" required>
                <option value="">-- Pilih Peran --</option>
                <option value="asisten_manager" {{ old('role', $user->role) == 'asisten_manager' ? 'selected' : '' }}>Asisten Manager</option>
                <option value="pic_keuangan" {{ old('role', $user->role) == 'pic_keuangan' ? 'selected' : '' }}>PIC Bidang Keuangan</option>
                <option value="pic_manajemen_risiko" {{ old('role', $user->role) == 'pic_manajemen_risiko' ? 'selected' : '' }}>PIC Manajemen Risiko</option>
                <option value="pic_sekretaris_perusahaan" {{ old('role', $user->role) == 'pic_sekretaris_perusahaan' ? 'selected' : '' }}>PIC Sekretaris Perusahaan</option>
                <option value="pic_perencanaan_operasi" {{ old('role', $user->role) == 'pic_perencanaan_operasi' ? 'selected' : '' }}>PIC Perencanaan Operasi</option>
                <option value="pic_pengembangan_bisnis" {{ old('role', $user->role) == 'pic_pengembangan_bisnis' ? 'selected' : '' }}>PIC Pengembangan Bisnis</option>
                <option value="pic_human_capital" {{ old('role', $user->role) == 'pic_human_capital' ? 'selected' : '' }}>PIC Human Capital</option>
                <option value="pic_k3l" {{ old('role', $user->role) == 'pic_k3l' ? 'selected' : '' }}>PIC K3L</option>
                <option value="pic_perencanaan_korporat" {{ old('role', $user->role) == 'pic_perencanaan_korporat' ? 'selected' : '' }}>PIC Perencanaan Korporat</option>
                <option value="karyawan" {{ old('role', $user->role) == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit">Update</button>
        </div>
    </form>
</div>
@endsection
