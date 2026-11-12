<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProdukController extends Controller
{
    private function rules(?Produk $produk = null): array
    {
        return [
            'nama_produk' => ['required', 'string', 'max:150'],
            'kategori' => ['required', Rule::in(config('tokoku.categories'))],
            'harga' => ['required', 'integer', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'barcode' => ['nullable', 'string', 'max:50', Rule::unique('produk', 'barcode')->ignore($produk?->id)],
            'satuan' => ['nullable', 'string', 'max:20'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gambar_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    private function messages(): array
    {
        return [
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
            'foto.uploaded' => 'Foto gagal diunggah. Cek ukuran file (maks 2 MB).',
            'barcode.unique' => 'Barcode ini sudah dipakai produk lain.',
            'gambar_url.url' => 'URL gambar tidak valid (harus diawali http:// atau https://).',
        ];
    }

    public function index(Request $request)
    {
        $low = (int) config('tokoku.low_stock');

        $produk = Produk::query()
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('nama_produk', 'like', '%' . $request->q . '%')
                ->orWhere('barcode', 'like', '%' . $request->q . '%')))
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori', $request->kategori))
            ->when($request->stok === 'low', fn ($q) => $q->where('stok', '>', 0)->where('stok', '<=', $low))
            ->when($request->stok === 'out', fn ($q) => $q->where('stok', '<=', 0))
            ->orderBy('nama_produk')
            ->paginate(12)
            ->withQueryString();

        $stat = [
            'jenis' => Produk::count(),
            'stok' => (int) Produk::sum('stok'),
            'low' => Produk::where('stok', '>', 0)->where('stok', '<=', $low)->count(),
            'out' => Produk::where('stok', '<=', 0)->count(),
        ];

        return view('produk.index', compact('produk', 'stat'));
    }

    public function create()
    {
        return view('produk.form', ['produk' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages());
        $data['satuan'] = $data['satuan'] ?: 'Pcs';
        $data['gambar'] = $this->resolveGambar($request);
        unset($data['foto'], $data['gambar_url']);

        Produk::create($data);

        return redirect()->route('produk.index')->with('ok', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        return view('produk.form', compact('produk'));
    }

    public function update(Request $request, Produk $produk)
    {
        $data = $request->validate($this->rules($produk), $this->messages());
        $data['satuan'] = $data['satuan'] ?: 'Pcs';
        unset($data['foto'], $data['gambar_url']);

        if ($request->hasFile('foto') || $request->filled('gambar_url')) {
            $baru = $this->resolveGambar($request);
            $this->hapusFile($produk);
            $data['gambar'] = $baru;
        } elseif ($request->boolean('hapus_gambar')) {
            $this->hapusFile($produk);
            $data['gambar'] = null;
        }

        $produk->update($data);

        return redirect()->route('produk.index')->with('ok', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $this->hapusFile($produk);
        $produk->delete();

        return redirect()->route('produk.index')->with('ok', 'Produk dihapus.');
    }

    public function restock(Request $request, Produk $produk)
    {
        $data = $request->validate(['jumlah' => ['required', 'integer', 'min:1', 'max:100000']]);
        $produk->increment('stok', $data['jumlah']);

        return back()->with('ok', "Stok {$produk->nama_produk} bertambah {$data['jumlah']}.");
    }

    /** Upload file diprioritaskan; jika tidak ada pakai URL; jika keduanya kosong -> null. */
    private function resolveGambar(Request $request): ?string
    {
        if ($request->hasFile('foto')) {
            return $request->file('foto')->store('produk', 'public');
        }

        return $request->filled('gambar_url') ? $request->gambar_url : null;
    }

    private function hapusFile(Produk $produk): void
    {
        if ($produk->gambarLokal()) {
            Storage::disk('public')->delete($produk->gambar);
        }
    }
}
