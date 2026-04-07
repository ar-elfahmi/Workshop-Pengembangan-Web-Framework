<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_transaksi_detail', function (Blueprint $table) {
            $table->integer('iddetail')->autoIncrement();
            $table->integer('idtransaksi');
            $table->string('kode_barang', 20);
            $table->string('nama_barang', 255);
            $table->unsignedBigInteger('harga');
            $table->unsignedInteger('jumlah');
            $table->unsignedBigInteger('subtotal');

            $table->foreign('idtransaksi', 'fk_pos_detail_transaksi')
                ->references('idtransaksi')->on('pos_transaksi')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_transaksi_detail');
    }
};
