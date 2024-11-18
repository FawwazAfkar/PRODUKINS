<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    use HasFactory;

    // Add the columns to be mass assigned
    protected $fillable = [
        'produk_jadi_id',
        'nama_produk',
        'tanggal_mulai',
        'jam_mulai',
        'tanggal_selesai',
        'jam_selesai',
        'jumlah_produksi',
        'status_produksi',
    ];

    protected $casts = [
        'bahan_baku' => 'array',
    ];

    public function produkJadi()
    {
        return $this->belongsTo(ProdukJadi::class);
    }

    public static function calculateBahanBaku($produkJadiId, $jumlahProduksi)
    {
        $produkJadi = ProdukJadi::with('bahanBakus')->find($produkJadiId);

        if (!$produkJadi) {
            return [];
        }

        return $produkJadi->bahanBakus->map(function ($bahan) use ($jumlahProduksi) {
            return [
                'nama_bahan' => $bahan->nama_bahan,
                'jumlah' => $bahan->pivot->jumlah * $jumlahProduksi, // Multiply by production quantity
            ];
        })->toArray();
    }
}
