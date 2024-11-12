<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal_pesan',
        'nama_produk',
        'jumlah',
        'nama_pelanggan',
        'alamat_pengiriman',
        'kontak_pelanggan',
        'status_pesanan',
        'catatan',
    ];
}
