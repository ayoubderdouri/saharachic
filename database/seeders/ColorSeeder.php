<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            'Noir' => '#000000',
            'Blanc' => '#FFFFFF',
            'Gris' => '#808080',
            'Beige' => '#F5F5DC',
            'Marron' => '#8B4513',
            'Rouge' => '#DC2626',
            'Bordeaux' => '#800020',
            'Rose' => '#F472B6',
            'Orange' => '#F97316',
            'Jaune' => '#FACC15',
            'Vert' => '#16A34A',
            'Kaki' => '#8A8F5A',
            'Bleu' => '#2563EB',
            'Bleu marine' => '#1E3A8A',
            'Turquoise' => '#14B8A6',
            'Violet' => '#7C3AED',
        ];

        foreach ($colors as $name => $hex) {
            Color::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['guid' => (string) Str::uuid(), 'name' => $name, 'hex_code' => $hex],
            );
        }
    }
}
