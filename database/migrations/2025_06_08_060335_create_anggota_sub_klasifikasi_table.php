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
        Schema::create('anggota_sub_klasifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_klasifikasi_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['anggota_id', 'sub_klasifikasi_id']); // agar tidak dobel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_sub_klasifikasi');
    }
};
