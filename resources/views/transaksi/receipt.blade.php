<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $t->nomor_invoice }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @page { size: {{ config('tokoku.paper_width') }} auto; margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #CBD5E1; font: 12px/1.5 'Space Mono', 'Courier New', monospace; color: #000; }
        .paper { width: {{ config('tokoku.paper_width') }}; max-width: 100%; margin: 14px auto; background: #fff; padding: 12px 10px 16px; }
        .c { text-align: center; }
        .sep { border: 0; border-top: 1px dashed #000; margin: 8px 0; }
        .r { display: flex; justify-content: space-between; gap: 6px; }
        .name { word-break: break-word; font-weight: 700; }
        .tot { font-weight: 700; font-size: 14px; }
        .bars { display: flex; justify-content: center; gap: 1px; height: 30px; margin: 8px 0 4px; }
        .bars i { display: block; background: #000; }
        .bars i.g { background: transparent; }
        .note { font-size: 10px; }
        .bar { width: {{ config('tokoku.paper_width') }}; margin: 14px auto 0; display: flex; gap: 8px; font-family: system-ui, sans-serif; }
        .bar a, .bar button { flex: 1; padding: 9px; border: 1px solid #059669; background: #059669; color: #fff; border-radius: 8px; font: 700 13px system-ui, sans-serif; text-align: center; text-decoration: none; cursor: pointer; }
        .bar a { background: #fff; color: #047857; }
        @media print { body { background: #fff; } .paper { margin: 0; width: 100%; } .bar { display: none; } }
    </style>
</head>
<body>
@unless($embed)
<div class="bar">
    <a href="{{ route('pos') }}">Kembali ke kasir</a>
    <button type="button" onclick="window.print()">Cetak</button>
</div>
@endunless
<div class="paper">
    <div class="c"><b style="font-size:15px">{{ strtoupper(config('tokoku.name')) }}</b><br>{{ config('tokoku.tagline') }}<br>{{ config('tokoku.address') }}<br>Telp: {{ config('tokoku.phone') }}</div>
    <hr class="sep">
    <div class="r"><span>No. Struk:</span><span>{{ $t->nomor_invoice }}</span></div>
    <div class="r"><span>Tanggal:</span><span>{{ tgl($t->created_at, 'd/m/Y H:i') }}</span></div>
    <div class="r"><span>Kasir:</span><span>{{ $t->kasir_nama }}</span></div>
    <div class="r"><span>Metode:</span><span>{{ $t->metode_pembayaran }}</span></div>
    <hr class="sep">
    @foreach($t->details as $d)
        <div class="name">{{ $d->nama_produk }}</div>
        <div class="r"><span>{{ $d->jumlah }} x {{ rp($d->harga_satuan) }}</span><span>{{ rp($d->subtotal) }}</span></div>
    @endforeach
    <hr class="sep">
    <div class="r"><span>Subtotal ({{ $t->details->sum('jumlah') }} item)</span><span>{{ rp($t->total) }}</span></div>
    <div class="r tot"><span>TOTAL AKHIR</span><span>{{ rp($t->total) }}</span></div>
    <div class="r"><span>Bayar ({{ $t->metode_pembayaran }})</span><span>{{ rp($t->bayar) }}</span></div>
    <div class="r"><span>Kembalian</span><span>{{ rp($t->kembalian) }}</span></div>
    @if($t->catatan)<hr class="sep"><div class="note">Catatan: {{ $t->catatan }}</div>@endif
    <hr class="sep">
    @php($bars = collect(str_split(substr(md5($t->nomor_invoice), 0, 30)))->map(fn ($c) => (hexdec($c) % 3) + 1))
    <div class="bars">@foreach($bars as $w)<i style="width:{{ $w }}px"></i><i class="g" style="width:1px"></i>@endforeach</div>
    <div class="c note">{{ $t->nomor_invoice }}</div>
    <div class="c" style="margin-top:8px">Terima kasih atas kunjungan Anda!</div>
    <div class="c note">Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan</div>
</div>
</body>
</html>
