<?php

use Illuminate\Database\Seeder;
use App\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            [
                'merchant_id' => 1,
                'name' => 'CSO - Customer Service',
                'description' => 'Layanan yang berhubungan dengan rekening anda (pembuatan rekening, ganti kartu, kartu hilang, dsb.)',
                'quota' => 100,
                'interval' => 10
            ],
            [
                'merchant_id' => 1,
                'name' => 'Teller',
                'description' => 'Layanan yang berhubungan dengan transaksi keuangan anda',
                'quota' => 150,
                'interval' => 5
            ],
            [
                'merchant_id' => 2,
                'name' => 'CSO - Customer Service',
                'description' => 'Layanan yang berhubungan dengan rekening anda (pembuatan rekening, ganti kartu, kartu hilang, dsb.)',
                'quota' => 100,
                'interval' => 10
            ],
            [
                'merchant_id' => 2,
                'name' => 'Teller',
                'description' => 'Layanan yang berhubungan dengan transaksi keuangan anda',
                'quota' => 150,
                'interval' => 5
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
