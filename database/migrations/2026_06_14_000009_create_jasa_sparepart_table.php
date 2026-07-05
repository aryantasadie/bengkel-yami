<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jasa_sparepart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jasa_id')->constrained('jasa')->cascadeOnDelete();
            $table->foreignId('sparepart_id')->constrained('sparepart')->restrictOnDelete();
            $table->integer('qty_default')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jasa_sparepart');
    }
};
