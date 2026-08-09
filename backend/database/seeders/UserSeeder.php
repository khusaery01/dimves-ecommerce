<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@dimves.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Dimsum Vespa',
        ]);

        User::create([
            'name' => 'Customer',
            'email' => 'user@dimves.com',
            'password' => Hash::make('12345678'),
            'role' => 'user',
            'phone' => '081298765432',
            'address' => 'Pontianak',
        ]);
    }
}