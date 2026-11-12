@extends('layouts.app')
@section('title', 'Transaksi Kasir')

@section('content')
<div class="pos">
    <section>
        <div class="tools">
            <input class="s" type="search" id="search" placeholder="Cari nama produk, barcode, atau kategori..." autocomplete="off" aria-label="Cari produk">
            <form id="barcodeForm">
                <input type="text" id="barcode" placeholder="Scan / Input Barcode" inputmode="numeric" autocomplete="off" aria-label="Barcode">
                <button class="btn" type="submit">Scan</button>
                <button class="btn" type="button" id="simBtn">Simulasi Scan</button>
            </form>
        </div>
        <div class="drawer hide" id="sim"><b>Coba Barcode Cepat:</b></div>
        <div class="chips" id="chips"></div>
        <div class="grid" id="grid"></div>
    </section>

    <aside class="card cart">
        <div class="cart-h">
            <div><h3>Keranjang Transaksi</h3><p>Kasir: <b>{{ auth()->user()->name }}</b></p></div>
            <button class="btn btn-sm btn-dng hide" id="clear" type="button">Kosongkan</button>
        </div>
        <div class="lines" id="lines"></div>
        <div class="err" id="err" role="alert"></div>
        <div class="pay">
            <div class="sum"><span>Jumlah Item</span><span id="count">0 item</span></div>
            <div class="sum big"><span>Total Bayar</span><b class="num" id="total">Rp 0</b></div>

            <div class="lbl" style="margin-top:10px">Metode Pembayaran</div>
            <div class="methods" id="methods"></div>

            <div id="qris" class="qris hide"><b>QRIS TokoKu</b>Scan via BCA, GoPay, OVO, ShopeePay<br>Nominal Pas: <span id="qrisAmt"></span></div>
            <div id="debit" class="qris hide"><b>Debit / Transfer</b>Nominal Pas: <span id="debitAmt"></span></div>

            <div id="cash">
                <div class="field" style="margin-bottom:6px"><label for="paid">Uang Diterima (Rp)</label>
                    <input type="text" id="paid" inputmode="numeric" autocomplete="off" placeholder="0"></div>
                <div class="quick" id="quick"></div>
                <div class="sum kembali"><span>Kembalian</span><b class="num" id="change">Rp 0</b></div>
            </div>

            <div class="field" style="margin:10px 0"><label for="note">Catatan (Opsional)</label>
                <input type="text" id="note" maxlength="255" placeholder="Catatan transaksi..."></div>
            <button class="btn btn-pri btn-blk" id="pay" type="button" disabled>Proses Pembayaran</button>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
const PRODUCTS = @json($products);
const CATS = ['Semua', ...@json(config('tokoku.categories'))];
const METHODS = @json(config('tokoku.payment_methods'));
const LOW = {{ (int) config('tokoku.low_stock') }};
const URL_CHECKOUT = @json(route('pos.checkout'));
const CSRF = document.querySelector('meta[name=csrf-token]').content;

const $ = s => document.querySelector(s);
const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const byId = id => PRODUCTS.find(p => p.id === +id);

let cart = {}, cat = 'Semua', term = '', method = 'Tunai', paid = 0;

const total = () => Object.entries(cart).reduce((t, [id, q]) => t + byId(id).harga * q, 0);
const itemCount = () => Object.values(cart).reduce((a, b) => a + b, 0);

function showErr(msg) { const e = $('#err'); e.textContent = msg || ''; e.classList.toggle('show', !!msg); }

function renderChips() {
  $('#chips').innerHTML = CATS.map(c => `<button type="button" class="chip ${c === cat ? 'on' : ''}" data-cat="${esc(c)}">${esc(c)}</button>`).join('');
}

function renderGrid() {
  const q = term.trim().toLowerCase();
  const list = PRODUCTS.filter(p => (cat === 'Semua' || p.kategori === cat) &&
    (!q || p.nama.toLowerCase().includes(q) || (p.barcode || '').includes(q) || p.kategori.toLowerCase().includes(q)));
  $('#grid').innerHTML = list.length ? list.map(p => {
    const out = p.stok <= 0, low = p.stok > 0 && p.stok <= LOW, inCart = cart[p.id];
    return `<button type="button" class="item" data-id="${p.id}" ${out ? 'disabled' : ''}>
      ${inCart ? `<span class="inc">${inCart}</span>` : ''}
      <div>
        <div class="im">${p.gambar ? `<img src="${esc(p.gambar)}" alt="${esc(p.nama)}" loading="lazy" onerror="this.parentNode.innerHTML='<div class=nf>Tanpa Foto</div>'">` : '<div class="nf">Tanpa Foto</div>'}
          <span class="tag c">${esc(p.kategori)}</span>
          <span class="tag s ${out ? 'out' : low ? 'low' : ''}">${out ? 'Habis' : low ? 'Sisa ' + p.stok : 'Stok ' + p.stok}</span></div>
        <h4>${esc(p.nama)}</h4>
      </div>
      <div class="pr"><div><small>Harga</small><b class="num">${rp(p.harga)}</b></div><span class="plus">+</span></div>
    </button>`;
  }).join('') : '<p class="empty" style="grid-column:1/-1">Tidak ada produk ditemukan. Coba ubah kata kunci atau filter kategori.</p>';
}

function renderCart() {
  const ids = Object.keys(cart);
  $('#lines').innerHTML = ids.length ? ids.map(id => {
    const p = byId(id), q = cart[id];
    return `<div class="line">
      ${p.gambar ? `<img class="lt" src="${esc(p.gambar)}" alt="">` : '<span class="lt"></span>'}
      <div><div class="nm">${esc(p.nama)}</div><div class="hg">@ ${rp(p.harga)}</div>
        <div class="qty"><button type="button" data-act="dec" data-id="${id}" aria-label="Kurangi">−</button><b>${q}</b>
        <button type="button" data-act="inc" data-id="${id}" aria-label="Tambah">+</button>
        <button type="button" class="rm" data-act="rm" data-id="${id}">Hapus</button></div></div>
      <div class="sb num">${rp(p.harga * q)}</div></div>`;
  }).join('') : '<div class="cartempty"><b>Keranjang masih kosong</b>Klik produk di katalog untuk menambahkan</div>';
  $('#clear').classList.toggle('hide', !ids.length);
  $('#count').textContent = itemCount() + ' item';
  $('#total').textContent = rp(total());
  $('#qrisAmt').textContent = $('#debitAmt').textContent = rp(total());
  renderQuick(); renderPay(); renderGrid();
}

function renderMethods() {
  $('#methods').innerHTML = METHODS.map(m => `<button type="button" class="${m === method ? 'on' : ''}" data-m="${esc(m)}">${esc(m)}</button>`).join('');
  $('#cash').classList.toggle('hide', method !== 'Tunai');
  $('#qris').classList.toggle('hide', method !== 'QRIS');
  $('#debit').classList.toggle('hide', method !== 'Debit/Transfer');
}

function renderQuick() {
  const t = total();
  const presets = [['Uang Pas', t], ['Rp 10.000', 10000], ['Rp 20.000', 20000], ['Rp 50.000', 50000], ['Rp 100.000', 100000], ['Rp 200.000', 200000]];
  $('#quick').innerHTML = presets.map(([l, v]) => `<button type="button" data-paid="${v}">${l}</button>`).join('');
}

function renderPay() {
  const t = total();
  $('#change').textContent = rp(Math.max(0, paid - t));
  $('#pay').disabled = !(t > 0 && (method !== 'Tunai' || paid >= t));
}

function add(id) {
  const p = byId(id);
  if (!p || p.stok <= 0) return;
  if ((cart[id] || 0) >= p.stok) { showErr(`Stok ${p.nama} tidak cukup (sisa ${p.stok}).`); return; }
  showErr('');
  cart[id] = (cart[id] || 0) + 1;
  renderCart();
}

$('#chips').addEventListener('click', e => { const b = e.target.closest('[data-cat]'); if (b) { cat = b.dataset.cat; renderChips(); renderGrid(); } });
$('#search').addEventListener('input', e => { term = e.target.value; renderGrid(); });
$('#grid').addEventListener('click', e => { const b = e.target.closest('.item'); if (b) add(b.dataset.id); });
$('#lines').addEventListener('click', e => {
  const b = e.target.closest('[data-act]'); if (!b) return;
  const id = b.dataset.id;
  if (b.dataset.act === 'inc') return add(id);
  if (b.dataset.act === 'dec') { cart[id]--; if (cart[id] < 1) delete cart[id]; }
  if (b.dataset.act === 'rm') delete cart[id];
  showErr(''); renderCart();
});
$('#clear').addEventListener('click', () => { cart = {}; paid = 0; $('#paid').value = ''; showErr(''); renderCart(); });
$('#methods').addEventListener('click', e => { const b = e.target.closest('[data-m]'); if (!b) return; method = b.dataset.m; renderMethods(); renderPay(); });
$('#quick').addEventListener('click', e => { const b = e.target.closest('[data-paid]'); if (!b) return; paid = +b.dataset.paid; $('#paid').value = paid ? paid.toLocaleString('id-ID') : ''; renderPay(); });
$('#paid').addEventListener('input', e => {
  paid = parseInt(e.target.value.replace(/\D/g, '') || '0', 10);
  e.target.value = paid ? paid.toLocaleString('id-ID') : '';
  renderPay();
});

$('#barcodeForm').addEventListener('submit', e => {
  e.preventDefault();
  const code = $('#barcode').value.trim(); if (!code) return;
  const p = PRODUCTS.find(x => x.barcode && x.barcode === code);
  if (p) { add(p.id); $('#barcode').value = ''; } else showErr(`Produk dengan barcode "${code}" tidak ditemukan.`);
});
$('#simBtn').addEventListener('click', function () {
  const d = $('#sim'); d.classList.toggle('hide');
  this.textContent = d.classList.contains('hide') ? 'Simulasi Scan' : 'Tutup Scanner';
});
$('#sim').insertAdjacentHTML('beforeend', PRODUCTS.slice(0, 6).map(p => `<button type="button" data-id="${p.id}">${esc(p.nama.slice(0, 16))}… (${esc(p.barcode || '-')})</button>`).join(''));
$('#sim').addEventListener('click', e => { const b = e.target.closest('button'); if (b) add(b.dataset.id); });

$('#pay').addEventListener('click', async () => {
  const btn = $('#pay'); btn.disabled = true; btn.textContent = 'Memproses…'; showErr('');
  const items = Object.entries(cart).map(([id, qty]) => ({id: +id, qty}));
  try {
    const res = await fetch(URL_CHECKOUT, {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF},
      body: JSON.stringify({items, metode: method, bayar: method === 'Tunai' ? paid : total(), catatan: $('#note').value || null}),
    });
    const data = await res.json();
    if (res.ok) {
      items.forEach(i => { byId(i.id).stok -= i.qty; });
      cart = {}; paid = 0; $('#paid').value = ''; $('#note').value = '';
      renderCart(); openReceipt(data.id);
    } else {
      showErr(data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Transaksi gagal.'));
    }
  } catch (err) {
    showErr('Tidak bisa terhubung ke server. Coba lagi.');
  }
  btn.textContent = 'Proses Pembayaran'; renderPay();
});

renderChips(); renderMethods(); renderCart();
</script>
@endpush
