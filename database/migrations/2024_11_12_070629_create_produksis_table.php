<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produksis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->date('tanggal_mulai');
            $table->time('jam_mulai');
            $table->date('tanggal_selesai');
            $table->time('jam_selesai');
            $table->integer('jumlah_produksi');
            $table->string('bahan_baku');
            $table->enum('status_produksi', ['proses', 'pending', 'selesai'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksis');
    }
};
