@extends('layouts.app')
@section('title', $produk ? 'Ubah Produk' : 'Tambah Produk')

@section('content')
<div class="ph-title"><div><h2>{{ $produk ? 'Ubah Produk' : 'Tambah Produk' }}</h2><p>Isi data produk, lalu upload foto dari perangkat atau tempel URL gambar.</p></div></div>

<form class="card" style="max-width:720px" method="POST" enctype="multipart/form-data"
      action="{{ $produk ? route('produk.update', $produk) : route('produk.store') }}">
    @csrf
    @if($produk) @method('PUT') @endif

    <div class="field">
        <span class="lbl">Foto Produk</span>
        <div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap">
            <img id="preview" class="thumb" style="width:110px;height:110px;{{ ($produk && $produk->gambar_url) ? '' : 'display:none' }}" src="{{ $produk->gambar_url ?? '' }}" alt="Pratinjau foto">
            <div style="flex:1;min-width:240px">
                <div class="tabs" style="margin-bottom:10px">
                    <a href="#" id="tabFile" class="on">Upload File</a>
                    <a href="#" id="tabUrl">Input URL</a>
                </div>
                <div id="boxFile">
                    <input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp">
                    <div><small>JPG, PNG, atau WEBP. Maksimal 2 MB.</small></div>
                </div>
                <div id="boxUrl" class="hide">
                    <input id="gambar_url" type="url" name="gambar_url" placeholder="https://images.unsplash.com/..." value="{{ old('gambar_url', ($produk && !$produk->gambarLokal()) ? $produk->gambar : '') }}">
                </div>
                @if($produk && $produk->gambar)
                    <label style="display:block;margin-top:8px;font-size:12px;color:var(--mut)"><input type="checkbox" name="hapus_gambar" value="1"> Hapus foto saat ini</label>
                @endif
            </div>
        </div>
    </div>

    <div class="field"><label for="nama_produk">Nama Produk</label>
        <input id="nama_produk" type="text" name="nama_produk" placeholder="Contoh: Aqua 600ml / Indomie Goreng" value="{{ old('nama_produk', $produk->nama_produk ?? '') }}" required></div>

    <div class="row2">
        <div class="field"><label for="kategori">Kategori</label>
            <select id="kategori" name="kategori" required>
                @foreach(config('tokoku.categories') as $c)<option value="{{ $c }}" @selected(old('kategori', $produk->kategori ?? 'Makanan') === $c)>{{ $c }}</option>@endforeach
            </select></div>
        <div class="field"><label for="satuan">Satuan</label>
            <input id="satuan" type="text" name="satuan" placeholder="Pcs, Botol, Kg, dll." value="{{ old('satuan', $produk->satuan ?? 'Pcs') }}"></div>
    </div>

    <div class="row2">
        <div class="field"><label for="harga">Harga Jual (Rp)</label>
            <input id="harga" type="number" min="0" name="harga" value="{{ old('harga', $produk->harga ?? '') }}" required></div>
        <div class="field"><label for="stok">Stok</label>
            <input id="stok" type="number" min="0" name="stok" value="{{ old('stok', $produk->stok ?? 0) }}" required></div>
    </div>

    <div class="field"><label for="barcode">Nomor Barcode / SKU</label>
        <input id="barcode" type="text" name="barcode" placeholder="899..." value="{{ old('barcode', $produk->barcode ?? '') }}"></div>

    <div class="field"><label for="deskripsi">Deskripsi Singkat (Opsional)</label>
        <textarea id="deskripsi" name="deskripsi" rows="2" maxlength="500" placeholder="Keterangan tambahan produk...">{{ old('deskripsi', $produk->deskripsi ?? '') }}</textarea></div>

    <div style="display:flex;gap:10px;margin-top:6px">
        <button class="btn btn-pri" type="submit">Simpan Produk</button>
        <a class="btn" href="{{ route('produk.index') }}">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
  const $ = id => document.getElementById(id), prev = $('preview');
  function mode(file) {
    $('tabFile').classList.toggle('on', file); $('tabUrl').classList.toggle('on', !file);
    $('boxFile').classList.toggle('hide', !file); $('boxUrl').classList.toggle('hide', file);
  }
  $('tabFile').addEventListener('click', e => { e.preventDefault(); mode(true); });
  $('tabUrl').addEventListener('click', e => { e.preventDefault(); mode(false); });
  if ($('gambar_url').value) mode(false);
  $('foto').addEventListener('change', function () {
    const f = this.files[0]; if (!f) return;
    prev.src = URL.createObjectURL(f); prev.style.display = '';
  });
  $('gambar_url').addEventListener('change', function () {
    if (this.value) { prev.src = this.value; prev.style.display = ''; }
  });
})();
</script>
@endpush
