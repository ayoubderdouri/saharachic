<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // $this->call([
        //     SizeSeeder::class,
        //     ColorSeeder::class,
        //     TrenchEleganceSeeder::class,
        // ]);

        User::factory()->create([
            'name' => 'Ayoub Derdouri',
            'email' => 'derdouri.ayoub20@gmail.com',
            'password' => Hash::make('Ayoub@2001'),
        ]);
    }
}
