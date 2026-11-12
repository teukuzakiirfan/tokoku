<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $list = Transaksi::query()
            ->with('details')
            ->when(! $request->user()->isAdmin(), fn ($q) => $q->where('user_id', $request->user()->id))
            ->when($request->filled('q'), function ($q) use ($request) {
                $kata = '%' . $request->q . '%';
                $q->where(fn ($w) => $w
                    ->where('nomor_invoice', 'like', $kata)
                    ->orWhere('kasir_nama', 'like', $kata)
                    ->orWhereHas('details', fn ($d) => $d->where('nama_produk', 'like', $kata)));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('transaksi.index', compact('list'));
    }

    public function receipt(Request $request, Transaksi $transaksi)
    {
        $this->izin($request, $transaksi);
        $transaksi->load('details');

        return view('transaksi.receipt', ['t' => $transaksi, 'embed' => $request->boolean('embed')]);
    }

    public function receiptText(Request $request, Transaksi $transaksi)
    {
        $this->izin($request, $transaksi);
        $transaksi->load('details');

        $w = 32;
        $line = str_repeat('-', $w);
        $kv = fn ($a, $b) => str_pad($a, $w - strlen($b)) . $b;

        $out = [
            strtoupper(config('tokoku.name')), config('tokoku.tagline'), config('tokoku.address'), 'Telp: ' . config('tokoku.phone'), $line,
            'No. Struk: ' . $transaksi->nomor_invoice,
            'Tanggal  : ' . tgl($transaksi->created_at, 'd/m/Y H:i'),
            'Kasir    : ' . $transaksi->kasir_nama,
            'Metode   : ' . $transaksi->metode_pembayaran, $line,
        ];
        foreach ($transaksi->details as $d) {
            $out[] = $d->nama_produk;
            $out[] = $kv("  {$d->jumlah} x " . rp($d->harga_satuan), rp($d->subtotal));
        }
        $out[] = $line;
        $out[] = $kv('TOTAL AKHIR', rp($transaksi->total));
        $out[] = $kv("Bayar ({$transaksi->metode_pembayaran})", rp($transaksi->bayar));
        $out[] = $kv('Kembalian', rp($transaksi->kembalian));
        $out[] = $line;
        $out[] = 'Terima kasih atas kunjungan Anda!';

        return response(implode("\n", $out), 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    private function izin(Request $request, Transaksi $transaksi): void
    {
        abort_unless($request->user()->isAdmin() || $transaksi->user_id === $request->user()->id, 403);
    }
}
