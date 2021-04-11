<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('users')->insert([
        //     'name' => "Adel Mahmoud",
        //     'email' => 'adelmahmoud1997@gmail.com',
        //     'password' => Hash::make('password'),
        //     'admin' => true,
        //     'brandId'=> -1
        // ]);
        DB::table('users')->insert([
            'name' => "Admin",
            'email' => 'admin',
            'password' => Hash::make('baba@motorcity'),
            'admin' => true,
            'brandId'=> -1
        ]);
        DB::table('users')->insert([
            'name' => "Ahmed Ibrahem",
            'email' => 'Ahmed Ibrahem',
            'password' => Hash::make('1984'),
            'admin' => true,
            'brandId'=> -1
        ]);
        DB::table('users')->insert([
            'name' => "Aly",
            'email' => 'Aly',
            'password' => Hash::make('aly@motorcity'),
            'admin' => true,
            'brandId'=> -1
        ]);
        DB::table('users')->insert([
            'name' => "Adel",
            'email' => 'Adel',
            'password' => Hash::make('adel@motorcity'),
            'admin' => true,
            'brandId'=> -1,
            'access'=>'tempBank'
        ]);
    }
    
}
