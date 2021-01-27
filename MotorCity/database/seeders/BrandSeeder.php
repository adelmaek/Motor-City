<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('brands')->insert([
            'name' => "Peugeot"
        ]);
        DB::table('brands')->insert([
            'name' => "MG"
        ]);

    }
}
