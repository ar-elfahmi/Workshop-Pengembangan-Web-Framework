<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk menggunakan fitur DB

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Memasukkan Data Kategori
        DB::table('kategori')->insert([
            ['idkategori' => 1, 'nama_kategori' => 'Novel'],
            ['idkategori' => 2, 'nama_kategori' => 'Biografi'],
            ['idkategori' => 3, 'nama_kategori' => 'Komik'],
            ['idkategori' => 4, 'nama_kategori' => 'Pendidikan'],
        ]);

        // 2. Memasukkan Data Buku
        DB::table('buku')->insert([
            [
                'idbuku' => 1,
                'kode' => 'NV-01',
                'judul' => 'Home Sweet Loan',
                'pengarang' => 'Almira Bastari',
                'idkategori' => 1,
            ],
        ]);

        // 3. Memasukkan Data Users (Admin & Akun Google Kamu)
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'admin',
                'email' => 'admin@mail.com',
                'google_id' => null,
                'provider' => null,
                'avatar' => null,
                // Menggunakan hash password persis seperti dari database lama kamu
                'password' => '$2y$12$sTpzJhvOSGFatRZR7mvCM.eLVf.en4HUwwfwSn8LosRuSSKBlCeOW',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}