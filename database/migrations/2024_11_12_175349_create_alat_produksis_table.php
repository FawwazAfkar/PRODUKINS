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
        Schema::create('alat_produksis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_alat');
            $table->date('tanggal_perawatan');
            $table->enum('status_alat', ['tersedia', 'rusak', 'dalam_perawatan'])->default('tersedia');
            $table->text('catatan_perawatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alat_produksis');
    }
};
