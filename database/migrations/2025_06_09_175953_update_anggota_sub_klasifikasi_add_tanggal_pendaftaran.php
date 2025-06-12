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
    Schema::table('anggota_sub_klasifikasi', function (Blueprint $table) {
        $table->date('tanggal_pendaftaran')->nullable();
        $table->date('masa_berlaku_sampai')->nullable();
    });

    Schema::table('anggotas', function (Blueprint $table) {
        $table->dropColumn(['tanggal_pendaftaran', 'masa_berlaku_sampai']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota_sub_klasifikasi', function (Blueprint $table) {
            //
        });
    }
};
