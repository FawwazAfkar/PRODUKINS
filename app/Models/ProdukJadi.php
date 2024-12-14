<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukJadi extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama_produk', 
        'kategori', 
        'stok',
        'harga',
        'gambar',
    ];
    
    public function bahanBakus()
    {
        return $this->belongsToMany(BahanBaku::class, 'materials')
            ->withPivot(['jumlah'])
            ->withTimestamps();
    }
}
