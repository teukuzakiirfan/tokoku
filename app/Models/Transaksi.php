<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = ['nomor_invoice', 'user_id', 'kasir_nama', 'total', 'bayar', 'kembalian', 'metode_pembayaran', 'catatan'];

    public function details()
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query, $from = null, $to = null, $kasir = null)
    {
        return $query
            ->when($from, fn ($q) => $q->whereBetween('created_at', [$from, $to]))
            ->when($kasir, fn ($q) => $q->where('user_id', $kasir));
    }
}
