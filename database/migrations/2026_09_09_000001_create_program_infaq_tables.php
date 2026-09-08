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
        if (!Schema::hasTable('program_infaq')) {
            Schema::create('program_infaq', function (Blueprint $table) {
                $table->id();
                $table->string('nama_program');
                $table->text('keterangan')->nullable();
                $table->decimal('target_dana', 15, 2)->default(0.00);
                $table->date('tanggal_mulai')->nullable();
                $table->date('tanggal_selesai')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('donasi_infaq')) {
            Schema::create('donasi_infaq', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('program_infaq_id');
                $table->string('nama_donatur')->default('Hamba Allah');
                $table->boolean('is_anonim')->default(false);
                $table->decimal('nominal', 15, 2)->default(0.00);
                $table->date('tanggal');
                $table->string('keterangan')->nullable();
                $table->timestamps();

                $table->foreign('program_infaq_id')
                      ->references('id')
                      ->on('program_infaq')
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donasi_infaq');
        Schema::dropIfExists('program_infaq');
    }
};
