@extends('layouts.app')
@section('title', 'Produk & Stok')

@section('content')
@php($admin = auth()->user()->isAdmin())
<div class="ph-title">
    <div>
        <h2>{{ $admin ? 'Kelola Produk & Foto' : 'Katalog & Cek Stok' }}</h2>
        <p>{{ $admin ? 'Tambah produk, upload foto produk dari perangkat, atur harga, dan pantau stok inventaris' : 'Lihat katalog dan cek ketersediaan stok barang' }}</p>
    </div>
    @if($admin)<a class="btn btn-pri" href="{{ route('produk.create') }}">+ Tambah Produk</a>@endif
</div>

<div class="stats">
    <div class="stat"><span>Jenis Produk</span><b>{{ number_format($stat['jenis'], 0, ',', '.') }}</b><small>Item terdaftar</small></div>
    <div class="stat"><span>Total Stok</span><b class="em">{{ number_format($stat['stok'], 0, ',', '.') }}</b><small>Seluruh unit di gudang</small></div>
    <div class="stat"><span>Stok Menipis</span><b class="am">{{ $stat['low'] }}</b><small>Sisa ≤ {{ config('tokoku.low_stock') }}</small></div>
    <div class="stat"><span>Stok Habis</span><b class="rs">{{ $stat['out'] }}</b><small>Perlu restock</small></div>
</div>

<form class="filters" method="GET">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama barang atau nomor barcode..." aria-label="Cari produk">
    <select name="kategori" aria-label="Kategori">
        <option value="">Semua kategori</option>
        @foreach(config('tokoku.categories') as $c)<option value="{{ $c }}" @selected(request('kategori') === $c)>{{ $c }}</option>@endforeach
    </select>
    <select name="stok" aria-label="Filter stok">
        <option value="">Semua stok</option>
        <option value="low" @selected(request('stok') === 'low')>Stok menipis</option>
        <option value="out" @selected(request('stok') === 'out')>Stok habis</option>
    </select>
    <button class="btn" type="submit">Terapkan</button>
    @if(request()->hasAny(['q','kategori','stok']))<a class="btn" href="{{ route('produk.index') }}">Reset Filter</a>@endif
</form>

<div class="wrap">
<table class="tbl">
    <thead><tr><th>Foto</th><th>Produk</th><th>Kategori</th><th class="right">Harga</th><th class="right">Stok</th>@if($admin)<th></th>@endif</tr></thead>
    <tbody>
    @forelse($produk as $p)
        @php($low = $p->stok > 0 && $p->stok <= config('tokoku.low_stock'))
        <tr>
            <td>@if($p->gambar_url)<img class="thumb" src="{{ $p->gambar_url }}" alt="{{ $p->nama_produk }}" loading="lazy">@else<div class="thumb">Tanpa<br>Foto</div>@endif</td>
            <td><b>{{ $p->nama_produk }}</b><div class="mut" style="font-size:11px">{{ $p->barcode ?: 'tanpa barcode' }} · {{ $p->satuan }}</div></td>
            <td>{{ $p->kategori }}</td>
            <td class="right num">{{ rp($p->harga) }}</td>
            <td class="right"><span class="pill {{ $p->stok <= 0 ? 'out' : ($low ? 'low' : 'ok') }}">{{ $p->stok <= 0 ? 'Habis' : $p->stok }}</span></td>
            @if($admin)
            <td class="acts">
                <details class="restock">
                    <summary class="btn btn-sm">Tambah Stok</summary>
                    <form method="POST" action="{{ route('produk.restock', $p) }}">@csrf
                        <input type="number" name="jumlah" value="10" min="1" aria-label="Jumlah tambahan stok">
                        <button class="btn btn-sm btn-pri" type="submit">Simpan</button>
                    </form>
                </details>
                <a class="btn btn-sm" href="{{ route('produk.edit', $p) }}">Ubah</a>
                <form method="POST" action="{{ route('produk.destroy', $p) }}" onsubmit="return confirm('Hapus produk ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-dng" type="submit">Hapus</button>
                </form>
            </td>
            @endif
        </tr>
    @empty
        <tr><td colspan="{{ $admin ? 6 : 5 }}" class="empty">Tidak ada produk yang cocok dengan pencarian / filter ini.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@include('partials.pager', ['paginator' => $produk])
@endsection
