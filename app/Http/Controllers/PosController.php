<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function index()
    {
        $products = Produk::orderBy('nama_produk')->get()->map(fn ($p) => [
            'id' => $p->id,
            'nama' => $p->nama_produk,
            'kategori' => $p->kategori,
            'harga' => $p->harga,
            'stok' => $p->stok,
            'barcode' => $p->barcode,
            'satuan' => $p->satuan,
            'gambar' => $p->gambar_url,
        ])->values();

        return view('pos.index', ['products' => $products]);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'metode' => ['required', Rule::in(config('tokoku.payment_methods'))],
            'bayar' => ['required', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $trx = DB::transaction(function () use ($data, $request) {
            $lines = collect($data['items'])->groupBy('id');
            $products = Produk::whereIn('id', $lines->keys())->lockForUpdate()->get()->keyBy('id');

            $total = 0;
            $rows = [];

            foreach ($lines as $id => $group) {
                $p = $products->get($id);
                if (! $p) {
                    throw ValidationException::withMessages(['items' => 'Ada produk yang sudah dihapus. Muat ulang halaman.']);
                }

                $qty = (int) $group->sum('qty');
                if ($p->stok < $qty) {
                    throw ValidationException::withMessages(['items' => "Stok {$p->nama_produk} tidak cukup (sisa {$p->stok})."]);
                }

                $subtotal = $p->harga * $qty;
                $total += $subtotal;
                $rows[] = [
                    'produk_id' => $p->id,
                    'nama_produk' => $p->nama_produk,
                    'kategori' => $p->kategori,
                    'harga_satuan' => $p->harga,
                    'jumlah' => $qty,
                    'subtotal' => $subtotal,
                ];
                $p->decrement('stok', $qty);
            }

            // QRIS & Debit/Transfer dibayar pas
            $bayar = $data['metode'] === 'Tunai' ? $data['bayar'] : $total;
            if ($bayar < $total) {
                throw ValidationException::withMessages(['bayar' => 'Uang yang dibayarkan masih kurang.']);
            }

            $trx = Transaksi::create([
                'nomor_invoice' => $this->nomorInvoice(),
                'user_id' => $request->user()->id,
                'kasir_nama' => $request->user()->name,
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $bayar - $total,
                'metode_pembayaran' => $data['metode'],
                'catatan' => $data['catatan'] ?? null,
            ]);
            $trx->details()->createMany($rows);

            return $trx;
        });

        return response()->json(['id' => $trx->id, 'invoice' => $trx->nomor_invoice]);
    }

    /** Format: TRX-20261030-0001 (urutan per hari). */
    private function nomorInvoice(): string
    {
        $prefix = 'TRX-' . now()->format('Ymd') . '-';
        $seq = Transaksi::where('nomor_invoice', 'like', $prefix . '%')->count() + 1;

        while (Transaksi::where('nomor_invoice', $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT))->exists()) {
            $seq++;
        }

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
