<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        $letters = ['S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];
        $numbers = range(36, 54, 2);

        foreach ([...$letters, ...$numbers] as $name) {
            Size::firstOrCreate(
                ['slug' => Str::slug((string) $name)],
                ['guid' => (string) Str::uuid(), 'name' => (string) $name],
            );
        }
    }
}
