<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('nik', 20)->unique();
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->date('tanggal_masuk');
            $table->string('jabatan', 50);
            $table->decimal('gaji_pokok', 12, 2);
            $table->decimal('tunjangan', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
