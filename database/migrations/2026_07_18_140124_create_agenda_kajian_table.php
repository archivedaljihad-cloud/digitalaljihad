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
        Schema::create('agenda_kajian', function (Blueprint $table) {

            $table->id();

            $table->string('judul');
            $table->string('pemateri');
            $table->date('tanggal');
            $table->time('waktu');

            $table->string('lokasi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();

            $table->integer('urutan')->default(1);
            $table->boolean('aktif')->default(true);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_kajian');
    }
};