<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nama_bahan',
        'stok',
        'unit',
    ];

    public function produkJadis()
    {
        return $this->belongsToMany(ProdukJadi::class, 'materials')
            ->withPivot(['jumlah'])
            ->withTimestamps();
    }

    public function getStokWithUnitAttribute()
    {
        return $this->stok . ' ' . ($this->unit ?? '');
    }

}
