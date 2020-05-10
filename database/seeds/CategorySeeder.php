<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            'name' => 'Low',
        ]);

        DB::table('categories')->insert([
            'name' => 'Medium',
        ]);
        
        DB::table('categories')->insert([
            'name' => 'High',
        ]);
    }
}
