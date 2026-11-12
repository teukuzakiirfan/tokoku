<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = ['nama_produk', 'kategori', 'harga', 'stok', 'barcode', 'satuan', 'deskripsi', 'gambar'];

    protected $casts = ['harga' => 'integer', 'stok' => 'integer'];

    /** true bila gambar adalah file hasil upload (bukan URL luar). */
    public function gambarLokal(): bool
    {
        return $this->gambar && ! preg_match('#^https?://#i', $this->gambar);
    }

    public function getGambarUrlAttribute(): ?string
    {
        if (! $this->gambar) {
            return null;
        }

        return $this->gambarLokal() ? asset('storage/' . $this->gambar) : $this->gambar;
    }
}
