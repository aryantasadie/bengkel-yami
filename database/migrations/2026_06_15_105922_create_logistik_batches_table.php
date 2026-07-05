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
        Schema::create('logistik_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logistik_id')->constrained('logistik')->onDelete('cascade');
            $table->integer('qty_awal');
            $table->integer('qty_sisa');
            $table->decimal('harga_beli', 12, 2);
            $table->date('tanggal_masuk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistik_batches');
    }
};
