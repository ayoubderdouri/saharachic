<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use App\Models\ProductVariant;

class TrenchEleganceSeeder extends Seeder
{
    public function run(): void
    {
        // Catégorie
        $category = Category::firstOrNew([
            'slug' => 'manteaux',
        ]);

        if (! $category->exists) {
            $category->guid = (string) Str::uuid();
        }

        $category->name = 'المعاطف';
        $category->status = 'active';
        $category->save();

        // Produit
        $product = Product::firstOrNew([
            'slug' => 'trench-elegance',
        ]);

        if (! $product->exists) {
            $product->guid = (string) Str::uuid();
        }

        $product->category_id = $category->id;
        $product->name = 'ترانش أناقة';
        $product->description = 'معطف نسائي أنيق بتصميم عصري، يتميز بياقة مرتفعة وأزرار أمامية مزدوجة وحزام قابل للتعديل وجيوب جانبية.';
        $product->price = 299; // Livraison incluse
        $product->sale_price = null;
        $product->quantity = 100; // Stock de démonstration
        $product->landing_page = 'produits.trench-elegance';
        $product->status = 'active';
        $product->is_featured = true;

        // Couleurs disponibles
        $colors = [
            ['name' => 'بيج',   'slug' => 'beige',  'hex_code' => '#CDB18E', 'sku' => 'BEI', 'image' => 'produits/manteau/beige.jpg'],
            ['name' => 'بني',   'slug' => 'marron', 'hex_code' => '#5B3828', 'sku' => 'MAR', 'image' => 'produits/manteau/maron.jpeg'],
            ['name' => 'زيتي',  'slug' => 'ziti',   'hex_code' => '#575B32', 'sku' => 'ZIT', 'image' => 'produits/manteau/zayti.jpg'],
            ['name' => 'أسود',  'slug' => 'noir',   'hex_code' => '#171717', 'sku' => 'NOI', 'image' => 'produits/manteau/noir.jpg'],
            ['name' => 'بوردو', 'slug' => 'bordo',  'hex_code' => '#701D2B', 'sku' => 'BOR', 'image' => 'produits/manteau/bordeaux.jpg'],
        ];

        $product->meta = [
            'name_fr' => 'Trench Élégance',
            'season' => 'الخريف والشتاء',
            'images' => array_column($colors, 'image', 'slug'),
            'features' => [
                'ياقة مرتفعة',
                'أزرار أمامية مزدوجة',
                'حزام قابل للتعديل',
                'جيوب جانبية',
                'أحزمة على الأكمام',
            ],
        ];

        $product->save();

        foreach ($colors as $colorData) {
            $color = Color::firstOrNew([
                'slug' => $colorData['slug'],
            ]);

            if (! $color->exists) {
                $color->guid = (string) Str::uuid();
            }

            $color->name = $colorData['name'];
            $color->hex_code = $colorData['hex_code'];
            $color->save();

            // Tailles disponibles
            foreach (['M', 'L', 'XL', '2XL'] as $sizeName) {
                $size = Size::firstOrNew([
                    'slug' => strtolower($sizeName),
                ]);

                if (! $size->exists) {
                    $size->guid = (string) Str::uuid();
                }

                $size->name = $sizeName;
                $size->save();

                // Variante produit : couleur + taille
                $variant = ProductVariant::firstOrNew([
                    'product_id' => $product->id,
                    'color_id' => $color->id,
                    'size_id' => $size->id,
                ]);

                if (! $variant->exists) {
                    $variant->guid = (string) Str::uuid();
                    $variant->quantity = 5; // Stock de démonstration
                }

                $variant->sku = 'TRE-' . $colorData['sku'] . '-' . $sizeName;
                $variant->price = null; // Utiliser le prix du produit
                $variant->status = 'active';
                $variant->save();
            }
        }
    }
}
