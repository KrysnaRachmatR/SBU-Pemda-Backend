<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKbliSubKlasifikasiTable extends Migration
{
    public function up(): void
    {
        Schema::create('kbli_sub_klasifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kbli_id')->constrained('kblis')->onDelete('cascade');
            $table->foreignId('sub_klasifikasi_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['kbli_id', 'sub_klasifikasi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kbli_sub_klasifikasi');
    }
}
