<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komisi_karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->restrictOnDelete();
            $table->foreignId('pesanan_jasa_id')->constrained('pesanan_jasa')->cascadeOnDelete();
            $table->decimal('nominal_komisi', 12, 2);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komisi_karyawan');
    }
};
