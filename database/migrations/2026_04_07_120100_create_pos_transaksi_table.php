<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_transaksi', function (Blueprint $table) {
            $table->integer('idtransaksi')->autoIncrement();
            $table->string('kode_transaksi', 30)->unique();
            $table->dateTime('tanggal_transaksi');
            $table->unsignedBigInteger('total');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_transaksi');
    }
};
