<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKblisTable extends Migration
{
    public function up(): void
    {
        Schema::create('kblis', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama')->nullable(); // optional, tergantung kebutuhan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kblis');
    }
}
