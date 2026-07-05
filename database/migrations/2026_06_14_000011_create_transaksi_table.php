<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('no_nota', 20)->unique();
            $table->foreignId('pesanan_id')->constrained('pesanan')->restrictOnDelete();
            $table->datetime('tanggal');
            $table->decimal('total_harga', 12, 2);
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->decimal('diskon_nominal', 12, 2)->default(0);
            $table->decimal('dp', 12, 2)->default(0);
            $table->decimal('sisa_bayar', 12, 2)->default(0);
            $table->decimal('total_bayar', 12, 2);
            $table->enum('status_bayar', ['lunas', 'dp', 'belum_bayar'])->default('belum_bayar');
            $table->string('metode_bayar', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
