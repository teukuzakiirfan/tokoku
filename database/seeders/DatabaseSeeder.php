<?php

namespace Database\Seeders;

use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun contoh (password semuanya: 123). GANTI sebelum dipakai sungguhan.
        foreach ([
            ['Bambang Wijaya (Pemilik Toko)', 'admin', 'admin', 'aktif', 'admin@tokoku.id', '0812-3456-7890'],
            ['Budi Santoso', 'budi', 'kasir', 'aktif', 'budi.kasir@tokoku.id', '0813-8899-1122'],
            ['Siti Rahmawati', 'siti', 'kasir', 'aktif', 'siti.kasir@tokoku.id', '0857-7766-3344'],
            ['Ahmad Fauzi (Akun Dinonaktifkan)', 'ahmad', 'kasir', 'nonaktif', 'ahmad@tokoku.id', '0878-1122-3344'],
        ] as [$nama, $username, $role, $status, $email, $telepon]) {
            User::updateOrCreate(['username' => $username], [
                'name' => $nama, 'password' => '123', 'role' => $role,
                'status' => $status, 'email' => $email, 'telepon' => $telepon,
            ]);
        }

        if (Produk::count() > 0) {
            return;
        }

        // [nama, kategori, harga, stok, barcode, satuan, deskripsi, gambar(URL)]
        foreach ([
            ['Indomie Goreng Spesial 85g', 'Makanan', 3500, 120, '899886620011', 'Bungkus', 'Mie instan goreng favorit keluarga dengan bumbu bawang gurih.', 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&auto=format&fit=crop&q=80'],
            ['Indomie Kuah Ayam Bawang 75g', 'Makanan', 3500, 85, '899886620028', 'Bungkus', 'Mie kuah dengan rasa kaldu ayam bawang yang sedap.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&auto=format&fit=crop&q=80'],
            ['Aqua Air Mineral Botol 600ml', 'Minuman', 4000, 95, '899277511101', 'Botol', 'Air mineral pegunungan murni kemasan botol praktis.', 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=400&auto=format&fit=crop&q=80'],
            ['Teh Botol Sosro Kotak 250ml', 'Minuman', 4500, 60, '899274198201', 'Kotak', 'Teh melati manis khas Sosro siap minum.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&auto=format&fit=crop&q=80'],
            ['Kopi Kapal Api Spesial Mix 10x24g', 'Minuman', 16000, 40, '899100210204', 'Renceng', 'Kopi bubuk hitam mantap berpadu dengan gula murni.', 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400&auto=format&fit=crop&q=80'],
            ['Ultra Milk Susu UHT Cokelat 250ml', 'Minuman', 7500, 45, '899269640441', 'Kotak', 'Susu segar rasa cokelat kaya kalsium dan vitamin.', 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&auto=format&fit=crop&q=80'],
            ['Chitato Sapi Panggang 68g', 'Snack', 11500, 35, '899886660121', 'Bungkus', 'Keripik kentang bergelombang rasa daging sapi panggang.', 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=400&auto=format&fit=crop&q=80'],
            ['Oreo Vanila Sandwich Biscuit 133g', 'Snack', 10500, 28, '899276012015', 'Bungkus', 'Biskuit cokelat dengan krim vanila lembut di tengah.', 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&auto=format&fit=crop&q=80'],
            ['SilverQueen Cashew Milk Chocolate 58g', 'Snack', 17500, 6, '899100140101', 'Batang', 'Cokelat susu lezat dengan butiran kacang mete renyah.', 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=400&auto=format&fit=crop&q=80'],
            ['Beras Ramos Super 5kg', 'Sembako', 74000, 18, '899887711200', 'Karung', 'Beras putih pulen kualitas premium tanpa pemutih.', 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&auto=format&fit=crop&q=80'],
            ['Minyak Goreng Tropical Pouch 2L', 'Sembako', 38500, 22, '899300512345', 'Pouch', 'Minyak goreng kelapa sawit 2 kali penyaringan.', 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&auto=format&fit=crop&q=80'],
            ['Gulaku Gula Pasir Tebu 1kg', 'Sembako', 18500, 30, '899280110012', 'Bungkus', 'Gula pasir kristal murni dari tebu pilihan.', 'https://images.unsplash.com/photo-1622484212850-eb596d769edc?w=400&auto=format&fit=crop&q=80'],
            ['Telur Ayam Negeri Fresh 1kg', 'Sembako', 28000, 4, '899887722100', 'Kg', 'Telur ayam segar langsung dari peternak lokal.', 'https://images.unsplash.com/photo-1516448620398-c5f44bf9f441?w=400&auto=format&fit=crop&q=80'],
            ['Sunlight Pencuci Piring Jeruk Nipis 700ml', 'Kebutuhan Rumah', 14500, 32, '899999905211', 'Pouch', 'Sabun cuci piring pembersih lemak 100x lebih cepat.', 'https://images.unsplash.com/photo-1585832770485-e68a5dbfad52?w=400&auto=format&fit=crop&q=80'],
            ['Rinso Anti Noda Deterjen Bubuk 800g', 'Kebutuhan Rumah', 22000, 19, '899999912040', 'Bungkus', 'Deterjen bubuk formula konsentrat angkat noda membandel.', 'https://images.unsplash.com/photo-1610557892470-55d9e80c0bce?w=400&auto=format&fit=crop&q=80'],
            ['So Klin Pewangi Pakaian Pink 900ml', 'Kebutuhan Rumah', 12500, 25, '899886611002', 'Pouch', 'Pewangi dan pelembut pakaian harum tahan hingga 30 hari.', 'https://images.unsplash.com/photo-1528740561666-dc2479dc08ab?w=400&auto=format&fit=crop&q=80'],
            ['Lifebuoy Sabun Mandi Batang Total 10 110g', 'Perawatan Pribadi', 5500, 50, '899999903120', 'Batang', 'Sabun mandi antibakteri perlindungan kuman aktif.', 'https://images.unsplash.com/photo-1600857544200-b2f666a9a2ec?w=400&auto=format&fit=crop&q=80'],
            ['Pepsodent Pasta Gigi Pencegah Gigi Berlubang 190g', 'Perawatan Pribadi', 16500, 24, '899999901414', 'Kotak', 'Pasta gigi dengan Mikro Kalsium aktif dan Pro-Fluoride.', 'https://images.unsplash.com/photo-1559591937-e1032b4eb14b?w=400&auto=format&fit=crop&q=80'],
            ['Clear Men Anti-Dandruff Shampoo Cool Sport 160ml', 'Perawatan Pribadi', 29500, 15, '899999908125', 'Botol', 'Shampo anti ketombe sensasi dingin menthol menyegarkan.', 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?w=400&auto=format&fit=crop&q=80'],
            ['Pocari Sweat Minuman Isotonik 500ml', 'Minuman', 8500, 0, '899277522301', 'Botol', 'Minuman pengganti ion dan cairan tubuh yang hilang.', 'https://images.unsplash.com/photo-1527661591475-527312dd65f5?w=400&auto=format&fit=crop&q=80'],
            ['Sedaap Mie Kuah Kari Spesial 75g', 'Makanan', 3500, 70, '899886630111', 'Bungkus', 'Kari kental sedap gurih mantap.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&auto=format&fit=crop&q=80'],
            ['Taro Net Seaweed Snack 65g', 'Snack', 9000, 40, '899276033441', 'Bungkus', 'Camilan jaring kentang rasa rumput laut renyah.', 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=400&auto=format&fit=crop&q=80'],
        ] as [$nama, $kategori, $harga, $stok, $barcode, $satuan, $deskripsi, $gambar]) {
            Produk::create([
                'nama_produk' => $nama, 'kategori' => $kategori, 'harga' => $harga, 'stok' => $stok,
                'barcode' => $barcode, 'satuan' => $satuan, 'deskripsi' => $deskripsi, 'gambar' => $gambar,
            ]);
        }
    }
}
