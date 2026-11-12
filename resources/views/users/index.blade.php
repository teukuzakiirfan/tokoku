@extends('layouts.app')
@section('title', 'Kelola Akun')

@section('content')
<div class="ph-title">
    <div><h2>Kelola Akun Kasir &amp; Pengguna</h2><p>Hak akses sistem, manajemen staf kasir TokoKu, dan kontrol status aktif / nonaktif</p></div>
    <a class="btn btn-pri" href="{{ route('users.create') }}">+ Tambah Akun</a>
</div>

<div class="wrap">
<table class="tbl">
    <thead><tr><th>Nama</th><th>Username</th><th>Kontak</th><th>Peran</th><th>Status</th><th class="right">Transaksi</th><th></th></tr></thead>
    <tbody>
    @foreach($users as $u)
        <tr>
            <td><b>{{ $u->name }}</b></td>
            <td class="mono">{{ $u->username }}</td>
            <td style="font-size:12px">{{ $u->telepon ?: '-' }}<div class="mut">{{ $u->email ?: '' }}</div></td>
            <td><span class="pill {{ $u->role === 'admin' ? 'adm' : '' }}">{{ $u->role === 'admin' ? 'Admin' : 'Kasir' }}</span></td>
            <td><span class="pill {{ $u->status === 'aktif' ? 'ok' : 'out' }}">{{ ucfirst($u->status) }}</span></td>
            <td class="right num">{{ $u->transaksi_count }}</td>
            <td class="acts">
                <a class="btn btn-sm" href="{{ route('users.edit', $u) }}">Ubah</a>
                @if($u->id !== auth()->id())
                <form method="POST" action="{{ route('users.destroy', $u) }}" onsubmit="return confirm('Hapus akun ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-dng" type="submit">Hapus</button>
                </form>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
@endsection
