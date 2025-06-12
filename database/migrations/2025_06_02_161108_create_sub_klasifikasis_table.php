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
        Schema::create('sub_klasifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klasifikasi_id')->constrained()->onDelete('cascade');
            $table->string('kode_sub_klasifikasi');
            $table->string('nama');
            $table->string('kode_kbli');
            $table->string('sifat_usaha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_klasifikasis');
    }
};
