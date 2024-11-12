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
        'bahan_baku', 
        'stok',
        'harga',
        'status_produk',
    ];
}
