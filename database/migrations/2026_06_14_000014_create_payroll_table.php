<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->restrictOnDelete();
            $table->tinyInteger('periode_bulan');
            $table->smallInteger('periode_tahun');
            $table->decimal('gaji_pokok_snapshot', 12, 2);
            $table->decimal('tunjangan_snapshot', 12, 2);
            $table->decimal('total_komisi', 12, 2)->default(0);
            $table->decimal('total_lembur', 12, 2)->default(0);
            $table->decimal('potongan_absen', 12, 2)->default(0);
            $table->decimal('potongan_lain', 12, 2)->default(0);
            $table->decimal('gaji_bersih', 12, 2);
            $table->enum('status', ['draft', 'final', 'dibayar'])->default('draft');
            $table->date('tanggal_bayar')->nullable();
            $table->timestamps();

            $table->unique(['karyawan_id', 'periode_bulan', 'periode_tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll');
    }
};
