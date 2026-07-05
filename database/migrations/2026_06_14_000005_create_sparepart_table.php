<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sparepart', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('satuan', 20);
            $table->decimal('harga_beli', 12, 2);
            $table->decimal('harga_jual', 12, 2);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sparepart');
    }
};
