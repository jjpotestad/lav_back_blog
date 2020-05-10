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
        $user = User::create([
            'name' => 'Julio',
            'surname' => 'Potestad',
            'email' => 'julio@gmail.com',
            'password' => Hash::make('admin'),
        ]);

        $user->assignRole('ADMIN');
        
        $user = User::create([
            'name' => 'Lester',
            'surname' => 'Potestad',
            'email' => 'lester@gmail.com',
            'password' => Hash::make('admin'),
        ]);

        $user->assignRole('USER');
        
        $user = User::create([
            'name' => 'Nelsy',
            'surname' => 'Berovides',
            'email' => 'nelsy@gmail.com',
            'password' => Hash::make('admin'),
        ]);

        $user->assignRole('USER');
    }
}
