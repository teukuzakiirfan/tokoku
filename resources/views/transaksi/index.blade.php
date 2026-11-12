@extends('layouts.app')
@section('title', 'Riwayat Transaksi')

@section('content')
<div class="ph-title">
    <div><h2>{{ auth()->user()->isAdmin() ? 'Semua Riwayat Transaksi' : 'Riwayat Transaksi Saya' }}</h2>
    <p>Klik baris untuk melihat rincian barang belanja, lalu cetak ulang struk bila perlu.</p></div>
</div>

<form class="filters" method="GET">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nomor invoice (TRX-...), nama barang, atau nama kasir..." aria-label="Cari transaksi">
    <button class="btn" type="submit">Cari</button>
    @if(request('q'))<a class="btn" href="{{ route('transaksi.index') }}">Reset</a>@endif
</form>

@forelse($list as $t)
    <details class="trx">
        <summary>
            <div class="sm">
                <div><b class="mono">{{ $t->nomor_invoice }}</b><div class="mut" style="font-size:11px">{{ tgl($t->created_at) }}</div></div>
                <div class="mut" style="font-size:12px">Kasir: {{ $t->kasir_nama }}</div>
                <div><span class="pill">{{ $t->metode_pembayaran }}</span></div>
                <div class="num" style="font-weight:800;color:var(--em4)">{{ rp($t->total) }}</div>
                <button class="btn btn-sm" type="button" onclick="event.preventDefault();openReceipt({{ $t->id }})">Struk</button>
            </div>
        </summary>
        <div class="det">
            <div class="wrap"><table class="tbl">
                <thead><tr><th>Nama Produk</th><th>Kategori</th><th class="right">Harga Satuan</th><th class="right">Jumlah</th><th class="right">Subtotal</th></tr></thead>
                <tbody>
                @foreach($t->details as $d)
                    <tr><td>{{ $d->nama_produk }}</td><td>{{ $d->kategori }}</td><td class="right num">{{ rp($d->harga_satuan) }}</td><td class="right num">{{ $d->jumlah }}</td><td class="right num">{{ rp($d->subtotal) }}</td></tr>
                @endforeach
                </tbody>
            </table></div>
            @if($t->catatan)<p class="mut" style="font-size:12px;margin:8px 0 0">Catatan: {{ $t->catatan }}</p>@endif
            <p class="mut" style="font-size:12px;margin:6px 0 0">Bayar {{ rp($t->bayar) }} · Kembalian {{ rp($t->kembalian) }}</p>
        </div>
    </details>
@empty
    <div class="card empty">Tidak ada riwayat transaksi.</div>
@endforelse
@include('partials.pager', ['paginator' => $list])
@endsection
