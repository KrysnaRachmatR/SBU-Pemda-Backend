<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sub_klasifikasis', function (Blueprint $table) {
            $table->dropColumn('sifat_usaha');
        });
    }

    public function down(): void
    {
        Schema::table('sub_klasifikasis', function (Blueprint $table) {
            $table->string('sifat_usaha')->nullable();
        });
    }
};

