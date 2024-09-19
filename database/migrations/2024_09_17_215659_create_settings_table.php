<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('unit', 50); // Nama unit (TK, SD, SMP, dll.)
            $table->integer('radius')->default(100); // Radius absensi
            $table->decimal('latitude', 10, 7)->nullable(); // Koordinat latitude
            $table->decimal('longitude', 10, 7)->nullable(); // Koordinat longitude
            // Kolom untuk pengaturan waktu absensi
            $table->time('waktu_masuk_mulai')->nullable();
            $table->time('waktu_masuk_akhir')->nullable();
            $table->time('waktu_istirahat_mulai')->nullable();
            $table->time('waktu_istirahat_akhir')->nullable();
            $table->time('waktu_pulang_mulai')->nullable();
            $table->time('waktu_pulang_akhir')->nullable();
            $table->timestamps(); // Waktu pembuatan dan pembaruan data
        });
    }

    public function down() {
        Schema::dropIfExists('settings');
    }
};
