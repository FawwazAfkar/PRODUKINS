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
            $table->foreignId('produk_jadi_id')->constrained('produk_jadis')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->time('jam_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->integer('jumlah_produksi');
            $table->json('bahan_baku')->nullable(); // Directly define the column as JSON
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
