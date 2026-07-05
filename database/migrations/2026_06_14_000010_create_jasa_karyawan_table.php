<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jasa_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_jasa_id')->constrained('pesanan_jasa')->cascadeOnDelete();
            $table->foreignId('karyawan_id')->constrained('karyawan')->restrictOnDelete();
            $table->enum('status', ['ditugaskan', 'proses', 'selesai'])->default('ditugaskan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jasa_karyawan');
    }
};
