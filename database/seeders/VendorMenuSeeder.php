<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VendorMenuSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('vendor') || ! Schema::hasTable('menu')) {
            return;
        }

        if (DB::table('vendor')->count() > 0) {
            return;
        }

        $vendors = [
            ['nama_vendor' => 'Warung Nasi Mak Ijah', 'created_at' => now(), 'updated_at' => now()],
            ['nama_vendor' => 'Soto Ayam Pak Mul', 'created_at' => now(), 'updated_at' => now()],
            ['nama_vendor' => 'Mie Goreng Terminal', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('vendor')->insert($vendors);

        $vendorMap = DB::table('vendor')->pluck('idvendor', 'nama_vendor');

        $menus = [
            [
                'nama_menu' => 'Nasi Ayam Geprek',
                'harga' => 18000,
                'path_gambar' => null,
                'idvendor' => $vendorMap['Warung Nasi Mak Ijah'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_menu' => 'Nasi Telur Balado',
                'harga' => 14000,
                'path_gambar' => null,
                'idvendor' => $vendorMap['Warung Nasi Mak Ijah'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_menu' => 'Soto Ayam + Nasi',
                'harga' => 16000,
                'path_gambar' => null,
                'idvendor' => $vendorMap['Soto Ayam Pak Mul'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_menu' => 'Es Teh Manis',
                'harga' => 5000,
                'path_gambar' => null,
                'idvendor' => $vendorMap['Soto Ayam Pak Mul'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_menu' => 'Mie Goreng Spesial',
                'harga' => 17000,
                'path_gambar' => null,
                'idvendor' => $vendorMap['Mie Goreng Terminal'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_menu' => 'Mie Rebus Jumbo',
                'harga' => 19000,
                'path_gambar' => null,
                'idvendor' => $vendorMap['Mie Goreng Terminal'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $validMenus = array_values(array_filter($menus, static fn(array $menu) => ! empty($menu['idvendor'])));

        if (! empty($validMenus)) {
            DB::table('menu')->insert($validMenus);
        }
    }
}
