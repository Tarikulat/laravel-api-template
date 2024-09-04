<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

       DB::table('users')->insert([
            [
                'username'     => 'admin',
                'phone_number' => '01686381998',
                'status'       => 1,
                'is_verified'  => 1,
                'password'     => Hash::make('123456789')
            ],
            [
                'username'     => 'superadmin',
                'phone_number' => '01764997485',
                'status'       => 1,
                'is_verified'  => 1,
                'password'     => Hash::make('123456789')
            ]
        ]);

        DB::table('role_user')->insert([
            [
                'role_id' => 1,
                'user_id' => 1,
                'user_type' => 'App\Models\User'
            ],
            [
                'role_id' => 1,
                'user_id' => 2,
                'user_type' => 'App\Models\User'
            ]
        ]);


    }
}
