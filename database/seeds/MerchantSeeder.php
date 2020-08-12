<?php

use Illuminate\Database\Seeder;
use App\Merchant;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $merchants = [
            [
                'category_id' => '8',
                'user_id' => '1',
                'name' => 'Bank Mandiri - KCP Mulyosari',
                'address' => 'Jl. Raya Mulyosari No.360D, Dukuh Sutorejo, Kec. Mulyorejo, Kota SBY, Jawa Timur 60113'
            ],
            [
                'category_id' => '8',
                'user_id' => '2',
                'name' => 'Bank BCA - KCP Mulyosari',
                'address' => 'Jl. Raya Mulyosari Z No.56, RW.08, Dukuh Sutorejo, Kec. Mulyorejo, Kota SBY, Jawa Timur 60113'
            ],
        ];

        foreach ($merchants as $merchant) {
            Merchant::create($merchant);
        }
    }
}
