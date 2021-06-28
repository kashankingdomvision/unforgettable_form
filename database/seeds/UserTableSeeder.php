<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'role_id'           =>  1,
            'supervisor_id'     =>  NULL,
            'currency_id'       =>  NULL,
            'brand_id'          =>  NULL,
            'name'              =>  'Kashan',
            'email'             =>  'kashan.kingdomvision@gmail.com',
            'email_verified_at' =>  now(),
            'password'          =>  Hash::make(12345678),
        ]);
    }
}
