<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatProduksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_alat',
        'tanggal_perawatan',
        'status_alat',
        'catatan_perawatan',
    ];
}
