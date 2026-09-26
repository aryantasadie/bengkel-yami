<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('jasa', function (Blueprint $table) {
            $table->foreignId('kategori_jasa_id')->nullable()->constrained('kategori_jasa')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('jasa', function (Blueprint $table) {
            $table->dropForeign(['kategori_jasa_id']);
            $table->dropColumn('kategori_jasa_id');
        });
    }
};
