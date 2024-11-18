<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'produk_jadi_id',
        'tanggal_pesan',
        'jumlah',
        'total_harga',
        'nama_pelanggan',
        'alamat_pengiriman',
        'kontak_pelanggan',
        'status_pesanan',
        'bukti_pembayaran',
        'no_resi',
        'catatan',
    ];

    public function produkJadi()
    {
        return $this->belongsTo(ProdukJadi::class);
    }

    public static function calculateTotalHarga($produkJadiId, $jumlah)
    {
        $produkJadi = ProdukJadi::find($produkJadiId);

        if ($produkJadi) {
            return $produkJadi->harga * $jumlah;
        }

        return 0; // Return 0 if the product is not found
    }
}
