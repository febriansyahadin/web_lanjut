<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [ 
        [
            'supplier_nama' => 'PT Sumber Makmur',
            'supplier_alamat' => 'Jl. Merdeka No. 45, Jakarta',
            'supplier_kontak' => '081234567890',
            'supplier_email' => 'sumur@sumbermakmur.co.id',
        ],
        [
            'supplier_nama' => 'PT Cahaya Abadi',
            'supplier_alamat' => 'Jl. Bunga Raya, Bandung',
            'supplier_kontak' => '081234567891',
            'supplier_email' => 'cahadi@cahayabadi.com',
        ],
        [
            'supplier_nama' => 'UD Jaya Sentosa',
            'supplier_alamat' => 'Jl. Industri No. 10, Surabaya',
            'supplier_kontak' => '081234567892',
            'supplier_email' => 'jayasentosa@example.com',
        ],
        [
            'supplier_nama' => 'PT Harapan Sejahtera', 
            'supplier_alamat' => 'Jl. Pemuda No. 11, Solo', 
            'supplier_kontak' => '081234567803', 
            'supplier_email' => 'harapansjahtera@example.com',
        ],
        [
            'supplier_nama' => 'PT Indo Trading', 
            'supplier_alamat' => 'Jl. Gajah Mada No. 14, Pontianak', 
            'supplier_kontak' => '081234567800', 
            'supplier_email' => 'sales@indotrading.co.id',
        ],
        ];
        DB::table('m_supplier')->insert($data);
    }
}
