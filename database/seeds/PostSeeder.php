<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('posts')->insert([
            'title' => 'post1',
            'content' => 'post1',
            'user_id' => 1,
            'category_id' => 2,

        ]);
        
        DB::table('posts')->insert([
            'title' => 'post2',
            'content' => 'post2',
            'user_id' => 2,
            'category_id' => 2,
        ]);
        
        DB::table('posts')->insert([
            'title' => 'post3',
            'content' => 'post3',
            'user_id' => 3,
            'category_id' => 3,
        ]);
    }
}
