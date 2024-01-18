<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'id' => Str::random(10),
            'houseId' => Str::random(10).'@gmail.com',
            'name' => Hash::make('password'),
        ]);
    }
}
