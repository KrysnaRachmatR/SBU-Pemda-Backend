<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('anggotas', function (Blueprint $table) {
        // Hapus foreign key constraint dulu
        $table->dropForeign(['sub_klasifikasi_id']);
        
        // Baru hapus kolomnya
        $table->dropColumn('sub_klasifikasi_id');
    });
}

public function down(): void
{
    Schema::table('anggotas', function (Blueprint $table) {
        $table->foreignId('sub_klasifikasi_id')
            ->constrained()
            ->cascadeOnDelete();
    });
}

};
