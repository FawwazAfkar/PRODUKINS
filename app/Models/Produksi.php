<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    use HasFactory;

    // Add the columns to be mass assigned
    protected $fillable = [
        'nama_produk',
        'tanggal_mulai',
        'jam_mulai',
        'tanggal_selesai',
        'jam_selesai',
        'jumlah_produksi',
        'bahan_baku',
        'status_produksi',
    ];
}
