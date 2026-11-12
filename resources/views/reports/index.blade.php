@extends('layouts.app')
@section('title', 'Laporan Penjualan')

@section('content')
<div class="ph-title">
    <div><h2>Laporan &amp; Rekap Penjualan</h2><p>Analisis omzet, rekap transaksi, dan statistik barang terlaris TokoKu · {{ $label }}</p></div>
    <div class="no-print" style="display:flex;gap:8px">
        <a class="btn" href="{{ route('reports.csv', ['periode' => $periode, 'kasir' => $kasir]) }}">Unduh CSV</a>
        <button class="btn" type="button" onclick="window.print()">Cetak</button>
    </div>
</div>

<div class="filters no-print">
    <div class="tabs">
        @foreach(\App\Http\Controllers\ReportController::PERIODE as $k => $v)
            <a href="{{ route('reports.index', ['periode' => $k, 'kasir' => $kasir]) }}" class="{{ $periode === $k ? 'on' : '' }}">{{ $v }}</a>
        @endforeach
    </div>
    <form method="GET" style="margin-left:auto">
        <input type="hidden" name="periode" value="{{ $periode }}">
        <select name="kasir" onchange="this.form.submit()" aria-label="Filter kasir">
            <option value="">Semua Kasir</option>
            @foreach($daftarKasir as $u)<option value="{{ $u->id }}" @selected($kasir === $u->id)>{{ $u->name }}</option>@endforeach
        </select>
    </form>
</div>

<div class="stats">
    <div class="stat"><span>Total Omzet</span><b class="em num">{{ rp($summary['omzet']) }}</b><small>Hasil penjualan bersih</small></div>
    <div class="stat"><span>Jumlah Transaksi</span><b class="num">{{ number_format($summary['count'], 0, ',', '.') }}</b><small>Struk kasir diterbitkan</small></div>
    <div class="stat"><span>Total Item Terjual</span><b class="num">{{ number_format($summary['items'], 0, ',', '.') }}</b><small>Produk keluar dari stok</small></div>
    <div class="stat"><span>Rata-rata Belanja</span><b class="num">{{ rp($summary['avg']) }}</b><small>Nilai transaksi per pelanggan</small></div>
</div>

<div class="two" style="margin-bottom:18px">
    <div class="card">
        <h3 style="margin-bottom:8px;font-size:14px">Produk Terlaris (Top 5)</h3>
        @forelse($top as $i => $r)
            <div class="rank"><span class="n">{{ $i + 1 }}</span><div><b>{{ $r->nama_produk }}</b><div class="mut" style="font-size:11px">{{ rp($r->omzet) }}</div></div><b class="num">{{ $r->qty }} terjual</b></div>
        @empty
            <div class="empty">Belum ada data produk terjual pada periode ini.</div>
        @endforelse
    </div>
    <div class="card">
        <h3 style="margin-bottom:12px;font-size:14px">Metode Pembayaran</h3>
        @php($maks = max(1, (int) $metode->max('omzet')))
        @forelse($metode as $m)
            <div class="bar-row"><div class="bl"><span>{{ $m->metode_pembayaran }} · {{ $m->n }} transaksi</span><b class="num">{{ rp($m->omzet) }}</b></div>
            <div class="bt"><i style="width:{{ round($m->omzet / $maks * 100) }}%"></i></div></div>
        @empty
            <div class="empty">Belum ada transaksi.</div>
        @endforelse
    </div>
</div>

<h3 style="margin:0 0 4px;font-size:14px">Daftar Transaksi</h3>
<p class="mut no-print" style="margin:0 0 10px;font-size:12px">Klik nomor invoice untuk melihat dan mencetak struk</p>
<div class="wrap">
<table class="tbl">
    <thead><tr><th>Invoice</th><th>Waktu</th><th>Kasir</th><th>Metode</th><th class="right">Total</th></tr></thead>
    <tbody>
    @forelse($list as $t)
        <tr>
            <td><a href="#" class="mono" onclick="openReceipt({{ $t->id }});return false">{{ $t->nomor_invoice }}</a></td>
            <td class="num">{{ tgl($t->created_at) }}</td>
            <td>{{ $t->kasir_nama }}</td>
            <td><span class="pill">{{ $t->metode_pembayaran }}</span></td>
            <td class="right num">{{ rp($t->total) }}</td>
        </tr>
    @empty
        <tr><td colspan="5" class="empty">Tidak ada transaksi pada periode yang dipilih.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@include('partials.pager', ['paginator' => $list])
@endsection
