<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->integer('idbarang')->autoIncrement();
            $table->string('kode', 20)->unique();
            $table->string('nama_barang', 255);
            $table->unsignedBigInteger('harga');
        });

        DB::table('barang')->insert([
            [
                'kode' => 'BRG001',
                'nama_barang' => 'Pulpen Hitam',
                'harga' => 5000,
            ],
            [
                'kode' => 'BRG002',
                'nama_barang' => 'Buku Tulis A5',
                'harga' => 12000,
            ],
            [
                'kode' => 'BRG003',
                'nama_barang' => 'Penghapus',
                'harga' => 4000,
            ],
            [
                'kode' => 'BRG004',
                'nama_barang' => 'Pensil 2B',
                'harga' => 3500,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
