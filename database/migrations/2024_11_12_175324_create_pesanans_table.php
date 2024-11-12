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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_pesan');
            $table->string('nama_produk');
            $table->integer('jumlah');
            $table->string('nama_pelanggan');
            $table->string('alamat_pengiriman');
            $table->string('kontak_pelanggan');
            $table->enum('status_pesanan', ['diproses', 'tertunda', 'dikirim', 'selesai'])->default('diproses');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
