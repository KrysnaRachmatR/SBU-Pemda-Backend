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
        Schema::table('anggotas', function (Blueprint $table) {
        $table->dropColumn(['status']);
    });
       Schema::table('anggota_sub_klasifikasi', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'nonaktif', 'pending'])->default('aktif')->after('masa_berlaku_sampai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anggota_sub_klasifikasi', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
