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
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('jenis_laporan');
            $table->text('keterangan')->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
