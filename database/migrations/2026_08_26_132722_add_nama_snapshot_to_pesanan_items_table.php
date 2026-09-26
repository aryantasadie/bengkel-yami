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
        Schema::table('pesanan_jasa', function (Blueprint $table) {
            $table->string('nama_snapshot')->nullable()->after('jasa_id');
        });

        Schema::table('pesanan_sparepart', function (Blueprint $table) {
            $table->string('nama_snapshot')->nullable()->after('sparepart_id');
        });

        DB::statement('ALTER TABLE jasa_karyawan MODIFY karyawan_id bigint unsigned NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan_jasa', function (Blueprint $table) {
            $table->dropColumn('nama_snapshot');
        });

        Schema::table('pesanan_sparepart', function (Blueprint $table) {
            $table->dropColumn('nama_snapshot');
        });

        DB::statement('ALTER TABLE jasa_karyawan MODIFY karyawan_id bigint unsigned NOT NULL');
    }
};
