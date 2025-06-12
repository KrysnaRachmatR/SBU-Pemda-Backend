<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('anggotas', function (Blueprint $table) {
            $table->unsignedBigInteger('kota_kabupaten_id')->after('sub_klasifikasi_id')->nullable();

            
            $table->foreign('kota_kabupaten_id')->references('id')->on('kota_kabupatens')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::table('anggotas', function (Blueprint $table) {
            $table->dropForeign(['kota_kabupaten_id']);
        });
    }
};
