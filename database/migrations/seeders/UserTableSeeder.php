<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


use Illuminate\Container\Attributes\DB as AttributesDB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
          
            //user
            [
                'name' => 'User',
                'username' => 'user',
                'email' => 'user@gmail.com',
                'password' => Hash::make('user'),
                'role' => 'user',
                'status' => 'active'
            ]

        ]);

       
    }
}
