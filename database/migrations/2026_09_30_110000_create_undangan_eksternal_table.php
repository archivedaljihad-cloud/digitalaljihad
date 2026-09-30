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
        if (!Schema::hasTable('undangan_eksternal')) {
            Schema::create('undangan_eksternal', function (Blueprint $table) {
                $table->id();
                $table->string('nama_pengundang'); // Nama Masjid / Mushola Pengundang
                $table->string('nama_acara'); // Nama Acara / Kegiatan
                $table->string('penceramah')->nullable(); // Penceramah / Tamu Undangan
                $table->string('tanggal_acara')->nullable(); // Hari & Tanggal Pelaksanaan
                $table->string('waktu_acara')->nullable(); // Waktu Pelaksanaan
                $table->string('tempat_acara')->nullable(); // Tempat / Alamat Acara
                $table->text('keterangan')->nullable(); // Keterangan / Ajakan Jamaah
                $table->boolean('is_active')->default(true); // Status Tayang di TV Display
                $table->integer('urutan')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('undangan_eksternal');
    }
};
