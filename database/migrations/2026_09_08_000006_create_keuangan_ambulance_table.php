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
        if (!Schema::hasTable('keuangan_ambulance')) {
            Schema::create('keuangan_ambulance', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal');
                $table->string('deskripsi');
                $table->decimal('pemasukan', 15, 2)->default(0.00);
                $table->decimal('pengeluaran', 15, 2)->default(0.00);
                $table->decimal('saldo', 15, 2)->default(0.00);
                $table->string('kategori', 100)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangan_ambulance');
    }
};
