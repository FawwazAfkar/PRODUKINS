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
    
    protected static function booted()
    {
        static::deleting(function ($bahanBaku) {
            if ($bahanBaku->produkJadis()->exists()) {
                throw new \Exception('Bahan baku tidak bisa dihapus karena masih digunakan oleh produk jadi.');
            }
        });
    }

    public function getStokWithUnitAttribute()
    {
        return $this->stok . ' ' . ($this->unit ?? '');
    }
    public function isBelowMinimumStock(): bool
    {
        return $this->stok < 3;
    }

}
