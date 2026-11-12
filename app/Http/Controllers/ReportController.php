<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public const PERIODE = [
        'hari' => 'Hari Ini',
        'kemarin' => 'Kemarin',
        'minggu' => '7 Hari Terakhir',
        'bulan' => 'Bulan Ini',
        'semua' => 'Semua Waktu',
    ];

    private function rentang(string $periode): array
    {
        return match ($periode) {
            'hari' => [now()->startOfDay(), now()->endOfDay()],
            'kemarin' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'minggu' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            'bulan' => [now()->startOfMonth(), now()->endOfDay()],
            default => [null, null],
        };
    }

    public function index(Request $request)
    {
        $periode = array_key_exists($request->periode, self::PERIODE) ? $request->periode : 'hari';
        $kasir = $request->integer('kasir') ?: null;
        [$from, $to] = $this->rentang($periode);

        $trx = fn () => Transaksi::filter($from, $to, $kasir);
        $detail = fn () => DetailTransaksi::whereHas('transaksi', fn ($q) => $q->filter($from, $to, $kasir));

        $count = $trx()->count();
        $omzet = (int) $trx()->sum('total');

        $summary = [
            'omzet' => $omzet,
            'count' => $count,
            'items' => (int) $detail()->sum('jumlah'),
            'avg' => $count ? intdiv($omzet, $count) : 0,
        ];

        $top = $detail()
            ->selectRaw('nama_produk, SUM(jumlah) as qty, SUM(subtotal) as omzet')
            ->groupBy('nama_produk')->orderByDesc('qty')->limit(5)->get();

        $metode = $trx()
            ->selectRaw('metode_pembayaran, COUNT(*) as n, SUM(total) as omzet')
            ->groupBy('metode_pembayaran')->orderByDesc('omzet')->get();

        $list = $trx()->latest()->paginate(15)->withQueryString();

        return view('reports.index', [
            'periode' => $periode,
            'kasir' => $kasir,
            'daftarKasir' => User::orderBy('name')->get(['id', 'name']),
            'summary' => $summary,
            'top' => $top,
            'metode' => $metode,
            'list' => $list,
            'label' => self::PERIODE[$periode],
        ]);
    }

    public function csv(Request $request)
    {
        $periode = array_key_exists($request->periode, self::PERIODE) ? $request->periode : 'hari';
        $kasir = $request->integer('kasir') ?: null;
        [$from, $to] = $this->rentang($periode);

        $rows = Transaksi::filter($from, $to, $kasir)->latest()->get();

        return response()->streamDownload(function () use ($rows) {
            $f = fopen('php://output', 'w');
            fputcsv($f, ['Invoice', 'Tanggal', 'Kasir', 'Metode', 'Total', 'Bayar', 'Kembalian', 'Catatan']);
            foreach ($rows as $r) {
                fputcsv($f, [$r->nomor_invoice, $r->created_at->format('Y-m-d H:i:s'), $r->kasir_nama, $r->metode_pembayaran, $r->total, $r->bayar, $r->kembalian, $r->catatan]);
            }
            fclose($f);
        }, 'laporan-penjualan-' . $periode . '-' . now()->format('Ymd') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
