<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
             'barang_id' => 1,
             'kategori_id' => 1,
             'barang_kode' => 'B001',
             'barang_nama' => 'Tahu walik',
             'harga_beli' => 2000,
             'harga_jual' => 5000,
            ],
            [
             'barang_id' => 2,
             'kategori_id' => 1,
             'barang_kode' => 'B002',
             'barang_nama' => 'Tempe tahu',
             'harga_beli' => 3000,
             'harga_jual' => 6000,
            ],
            [
             'barang_id' => 3,
             'kategori_id' => 1,
             'barang_kode' => 'B003',
             'barang_nama' => 'Sate Ayam',
             'harga_beli' => 5000,
             'harga_jual' => 10000,
            ],
            [
             'barang_id' => 4,
             'kategori_id' => 2,
             'barang_kode' => 'B004',
             'barang_nama' => 'Es Teh Manis',
             'harga_beli' => 1000,
             'harga_jual' => 2000,
            ],
            [
             'barang_id' => 5,
             'kategori_id' => 2,
             'barang_kode' => 'B005',
             'barang_nama' => 'Es Jeruk',
             'harga_beli' => 1500,
             'harga_jual' => 3000,
            ],
            [
             'barang_id' => 6,
             'kategori_id' => 3,
             'barang_kode' => 'B006',
             'barang_nama' => 'Laptop',
             'harga_beli' => 5000000,
             'harga_jual' => 6000000,
            ],
            [
             'barang_id' => 7,
             'kategori_id' => 3,
             'barang_kode' => 'B007',
             'barang_nama' => 'Smartphone',
             'harga_beli' => 3000000,
             'harga_jual' => 3500000,
            ],
            [
             'barang_id' => 8,
             'kategori_id' => 4,
             'barang_kode' => 'B008',
             'barang_nama' => 'T-Shirt',
             'harga_beli' => 50000,
             'harga_jual' => 60000,
            ],
            [
             'barang_id' => 9,
             'kategori_id' => 4,
             'barang_kode' => 'B009',
             'barang_nama' => 'Jeans',
             'harga_beli' => 100000,
             'harga_jual' => 120000,
            ],
            [
             'barang_id' => 10,
             'kategori_id' => 5,
             'barang_kode' => 'B010',
             'barang_nama' => 'Bola Sepak',
             'harga_beli' => 50000,
             'harga_jual' => 60000,
            ]
             ];
             DB::table('m_barang')->insert($data);
    }
}
