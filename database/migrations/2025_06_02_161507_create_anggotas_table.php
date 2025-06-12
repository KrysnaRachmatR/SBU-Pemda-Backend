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
        Schema::create('anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_klasifikasi_id')->constrained()->onDelete('cascade');

            $table->string('nama_perusahaan');
            $table->string('nama_penanggung_jawab');
            $table->string('alamat');
            $table->string('email');
            $table->string('no_telp');
            
            $table->enum('status',['aktif', 'pending', 'nonaktif'])->default('aktif');
            $table->date('tanggal_pendaftaran');
            $table->date('masa_berlaku_sampai');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggotas');
    }
};
