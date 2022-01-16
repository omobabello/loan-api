<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

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
                'first_name' => 'Chidi',
                'last_name' => 'Nkwocha',
                'email' => 'chidi.nkwocha@54gene.com',
                'phone' => '07038696899',
                'password' => 'password',
                'account_type' => 'borrower',
                'address' => '54gene main office',
            ],
            [
                'first_name' => 'Francis',
                'last_name' => 'Osifo',
                'email' => 'francis@54gene.com',
                'phone' => '07038696890',
                'password' => 'password',
                'account_type' => 'lender',
                'address' => '54gene head quarters',
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate($user);
        }
    }
}
