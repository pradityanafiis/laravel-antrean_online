<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'identity_number' => '3201010707000004',
                'name' => 'Praditya Nafiis Muhammad',
                'email' => 'nafiis@gmail.com',
                'phone' => '82277771838',
                'password' => Hash::make('password')
            ],
            [
                'identity_number' => '3201010707000001',
                'name' => 'Nadia Ayu Laksmidewi',
                'email' => 'nadia@gmail.com',
                'phone' => '83117389442',
                'password' => Hash::make('password')
            ],
            [
                'identity_number' => '3201010707000002',
                'name' => 'Zazabillah Sekar Puranti',
                'email' => 'zaza@gmail.com',
                'phone' => '85234662074',
                'password' => Hash::make('password')
            ]
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
