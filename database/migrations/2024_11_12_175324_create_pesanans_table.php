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
            $table->foreignId('produk_jadi_id')->constrained('produk_jadis')->onDelete('cascade');
            $table->date('tanggal_pesan');
            $table->integer('jumlah');
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->string('nama_pelanggan');
            $table->string('alamat_pengiriman');
            $table->string('kontak_pelanggan');
            $table->enum('status_pesanan', ['diproses', 'pending', 'dikirim', 'selesai'])->default('pending');
            $table->string('bukti_pembayaran')->nullable();
            $table->string('no_resi')->nullable();
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
