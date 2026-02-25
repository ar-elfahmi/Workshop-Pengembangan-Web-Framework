<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->integer('idbuku')->autoIncrement();
            $table->string('kode', 20);
            $table->string('judul', 500);
            $table->string('pengarang', 200);
            
            // Kolom Foreign Key
            $table->integer('idkategori')->nullable();

            // Mendefinisikan Relasi (Sesuai SQL Dump)
            $table->foreign('idkategori', 'fk_buku_kategori')
                  ->references('idkategori')->on('kategori')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};