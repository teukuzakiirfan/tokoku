<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk', 150);
            $table->string('kategori', 60);
            $table->unsignedInteger('harga');
            $table->unsignedInteger('stok')->default(0);
            $table->string('barcode', 50)->nullable()->unique();
            $table->string('satuan', 20)->default('Pcs');
            $table->string('deskripsi', 500)->nullable();
            $table->string('gambar', 500)->nullable(); // path upload (storage) ATAU URL gambar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
