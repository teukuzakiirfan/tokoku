<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir') · TokoKu POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/tokoku.css') }}">
</head>
<body>
@php($admin = auth()->user()->isAdmin())
<header class="top">
    <div class="top-in">
        <div class="brand">
            <div class="logo">T</div>
            <div>
                <h1>TokoKu <span>POS</span></h1>
                <p>Sistem Penjualan &amp; Manajemen Ritel Modern</p>
            </div>
        </div>
        <div class="clock" id="clock">Memuat waktu...</div>
        <details class="usermenu">
            <summary>
                <div class="who"><b>{{ auth()->user()->name }}</b><small>{{ $admin ? 'Admin (Pemilik)' : 'Kasir Toko' }}</small></div>
                <div class="avatar {{ $admin ? 'admin' : '' }}">{{ mb_substr(auth()->user()->name, 0, 2) }}</div>
            </summary>
            <div class="dropdown">
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Keluar (Logout)</button></form>
            </div>
        </details>
    </div>
</header>

<div class="body">
    <aside class="side">
        <div class="role {{ $admin ? 'admin' : 'kasir' }}">
            <small>Hak Akses Aktif</small>
            <b>{{ $admin ? 'Admin / Pemilik Toko' : 'Kasir Shift Aktif' }}</b>
        </div>
        <div>
            <div class="nav-h">Menu Utama</div>
            <nav class="nav">
                <a href="{{ route('pos') }}" class="{{ request()->routeIs('pos') ? 'on' : '' }}"><span>Transaksi Kasir</span><span class="bd">Kasir &amp; Admin</span></a>
                <a href="{{ route('produk.index') }}" class="{{ request()->routeIs('produk.*') ? 'on' : '' }}"><span>{{ $admin ? 'Kelola Produk & Foto' : 'Katalog & Cek Stok' }}</span><span class="bd">{{ $admin ? 'Admin' : 'Kasir' }}</span></a>
                <a href="{{ route('transaksi.index') }}" class="{{ request()->routeIs('transaksi.*') ? 'on' : '' }}"><span>{{ $admin ? 'Semua Riwayat Transaksi' : 'Riwayat Transaksi Saya' }}</span><span class="bd">{{ $admin ? 'Semua' : 'Sendiri' }}</span></a>
                @if($admin)
                    <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'on' : '' }}"><span>Laporan Penjualan</span><span class="bd">Admin Only</span></a>
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'on' : '' }}"><span>Kelola Akun Kasir</span><span class="bd">Admin Only</span></a>
                @endif
            </nav>
        </div>
        <div class="foot">
            <div><span>Framework:</span><span>Laravel</span></div>
            <div><span>Status Database:</span><span>Tersimpan di server</span></div>
        </div>
    </aside>

    <main class="main">
        <div class="wrapmax">
            @if(session('ok'))<div class="flash ok">{{ session('ok') }}</div>@endif
            @if($errors->any())
                <div class="flash err"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            @yield('content')
        </div>
    </main>
</div>

{{-- Modal struk (dipakai Kasir, Riwayat, Laporan) --}}
<div class="modal" id="receiptModal" role="dialog" aria-modal="true" aria-label="Struk Pembayaran">
    <div class="mbox">
        <div class="mh"><h3>Struk Pembayaran</h3><button type="button" data-close aria-label="Tutup">&times;</button></div>
        <iframe id="receiptFrame" title="Struk"></iframe>
        <div class="mf">
            <button type="button" class="btn" id="copyReceipt">Salin Struk</button>
            <button type="button" class="btn" data-close>Tutup</button>
            <button type="button" class="btn btn-pri" id="printReceipt">Cetak Struk</button>
        </div>
    </div>
</div>

<script>
(function () {
  var tick = function () {
    document.getElementById('clock').textContent = new Intl.DateTimeFormat('id-ID', {weekday:'short',day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit',second:'2-digit'}).format(new Date());
  };
  tick(); setInterval(tick, 1000);

  var HTML = @json(route('transaksi.receipt', '__ID__')), TXT = @json(route('transaksi.text', '__ID__'));
  var modal = document.getElementById('receiptModal'), frame = document.getElementById('receiptFrame'), current = null;

  window.openReceipt = function (id) {
    current = id;
    frame.src = HTML.replace('__ID__', id) + '?embed=1';
    modal.classList.add('open');
  };
  function close() { modal.classList.remove('open'); frame.src = 'about:blank'; }
  modal.addEventListener('click', function (e) { if (e.target === modal || e.target.closest('[data-close]')) close(); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
  document.getElementById('printReceipt').addEventListener('click', function () { frame.contentWindow.focus(); frame.contentWindow.print(); });
  document.getElementById('copyReceipt').addEventListener('click', function () {
    var b = this;
    fetch(TXT.replace('__ID__', current)).then(function (r) { return r.text(); })
      .then(function (t) { return navigator.clipboard.writeText(t); })
      .then(function () { b.textContent = 'Tersalin!'; setTimeout(function () { b.textContent = 'Salin Struk'; }, 1500); })
      .catch(function () { b.textContent = 'Gagal menyalin'; setTimeout(function () { b.textContent = 'Salin Struk'; }, 1500); });
  });
})();
</script>
@stack('scripts')
</body>
</html>
