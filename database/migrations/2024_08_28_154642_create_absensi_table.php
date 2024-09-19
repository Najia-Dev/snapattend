<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->time('time');
            $table->enum('type', ['Masuk', 'Istirahat', 'Pulang', 'Lembur', 'Urgent']);
            $table->boolean('is_in_radius')->default(true);
            $table->boolean('liveness_verified')->default(false);
            $table->enum('status', ['Success', 'Failed'])->default('Success');
            $table->text('reason')->nullable(); // Untuk absen urgent
            $table->string('proof_photo')->nullable(); // Path untuk foto bukti
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
