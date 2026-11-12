@extends('layouts.app')
@section('title', $user ? 'Ubah Akun' : 'Tambah Akun')

@section('content')
<div class="ph-title"><div><h2>{{ $user ? 'Ubah Akun' : 'Tambah Akun' }}</h2></div></div>

@php($self = $user && $user->id === auth()->id())
<form class="card" style="max-width:560px" method="POST" action="{{ $user ? route('users.update', $user) : route('users.store') }}">
    @csrf
    @if($user) @method('PUT') @endif

    <div class="field"><label for="name">Nama Lengkap</label>
        <input id="name" type="text" name="name" placeholder="Contoh: Rian Pratama" value="{{ old('name', $user->name ?? '') }}" required></div>

    <div class="row2">
        <div class="field"><label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="rian" value="{{ old('username', $user->username ?? '') }}" required autocomplete="off"></div>
        <div class="field"><label for="password">Password</label>
            <input id="password" type="password" name="password" minlength="4" placeholder="{{ $user ? 'Kosongkan jika tidak diganti' : 'Minimal 4 karakter' }}" {{ $user ? '' : 'required' }} autocomplete="new-password"></div>
    </div>

    <div class="row2">
        <div class="field"><label for="role">Peran (Role)</label>
            <select id="role" name="role" {{ $self ? 'disabled' : '' }}>
                <option value="kasir" @selected(old('role', $user->role ?? 'kasir') === 'kasir')>Kasir</option>
                <option value="admin" @selected(old('role', $user->role ?? '') === 'admin')>Admin (Pemilik)</option>
            </select>
            @if($self)<input type="hidden" name="role" value="{{ $user->role }}"><small>Akun yang sedang dipakai tidak bisa diubah perannya.</small>@endif</div>
        <div class="field"><label for="status">Status Akun</label>
            <select id="status" name="status" {{ $self ? 'disabled' : '' }}>
                <option value="aktif" @selected(old('status', $user->status ?? 'aktif') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(old('status', $user->status ?? '') === 'nonaktif')>Nonaktif</option>
            </select>
            @if($self)<input type="hidden" name="status" value="{{ $user->status }}">@endif</div>
    </div>

    <div class="row2">
        <div class="field"><label for="telepon">Nomor HP / WhatsApp</label>
            <input id="telepon" type="text" name="telepon" placeholder="08..." value="{{ old('telepon', $user->telepon ?? '') }}"></div>
        <div class="field"><label for="email">Email (Opsional)</label>
            <input id="email" type="email" name="email" placeholder="user@tokoku.id" value="{{ old('email', $user->email ?? '') }}"></div>
    </div>

    <div style="display:flex;gap:10px">
        <button class="btn btn-pri" type="submit">Simpan</button>
        <a class="btn" href="{{ route('users.index') }}">Batal</a>
    </div>
</form>
@endsection
