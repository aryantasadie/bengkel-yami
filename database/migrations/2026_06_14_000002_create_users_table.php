<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawan')->nullOnDelete();
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('nama', 100);
            $table->enum('role', ['admin', 'owner', 'karyawan']);
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
