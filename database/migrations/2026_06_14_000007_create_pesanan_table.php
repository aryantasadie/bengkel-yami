<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('no_pesanan', 20)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->datetime('tanggal_masuk');
            $table->datetime('tanggal_selesai')->nullable();
            $table->text('deskripsi_pekerjaan');
            $table->enum('status', ['antrian', 'proses', 'selesai', 'dibatalkan'])->default('antrian');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
