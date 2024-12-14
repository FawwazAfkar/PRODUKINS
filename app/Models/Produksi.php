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
        'bahan_baku',
    ];

    protected $casts = [
        'bahan_baku' => 'array',
    ];

    public function produkJadi()
    {
        return $this->belongsTo(ProdukJadi::class, 'produk_jadi_id');
    }

    public static function calculateBahanBaku($produkJadiId, $jumlahProduksi)
    {
        $produkJadi = ProdukJadi::with('bahanBakus')->find($produkJadiId);

        if (!$produkJadi) {
            throw new \Exception("Produk Jadi tidak ditemukan.");
        }

        return $produkJadi->bahanBakus->map(function ($bahan) use ($jumlahProduksi) {
            return [
                'nama_bahan' => $bahan->nama_bahan,
                'jumlah' => $bahan->pivot->jumlah * $jumlahProduksi,
                'unit' => $bahan->unit,
            ];
        })->toArray();
    }

    public static function calculateAndDeductMaterials(int $produkJadiId, int $jumlahProduksi): array
    {
        $produkJadi = ProdukJadi::with('bahanBakus')->find($produkJadiId);
        $deductions = [];

        if (!$produkJadi || !$produkJadi->bahanBakus) {
            throw new \Exception("Produk Jadi atau bahan baku tidak ditemukan.");
        }

        foreach ($produkJadi->bahanBakus as $bahanBaku) {
            $requiredAmount = $bahanBaku->pivot->jumlah * $jumlahProduksi;

            if ($bahanBaku->stok < $requiredAmount) {
                throw new \Exception("Bahan baku tidak mencukupi: {$bahanBaku->nama_bahan} ({$requiredAmount})");
            }

            $deductions[] = [
                'nama_bahan' => $bahanBaku->nama_bahan,
                'jumlah' => $requiredAmount,
            ];

            // Deduct stock
            $bahanBaku->decrement('stok', $requiredAmount);
        }

        return $deductions;
    }
}
