<?php

use App\Models\Product;
use Database\Seeders\DatabaseSeeder;

it('seeds the trench product and its variants idempotently', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    $product = Product::where('slug', 'trench-elegance')->firstOrFail();

    expect($product->status)->toBe('active')
        ->and($product->meta['images'])->toBe([
            'beige' => 'produits/manteau/beige.jpg',
            'marron' => 'produits/manteau/maron.jpeg',
            'ziti' => 'produits/manteau/zayti.jpg',
            'noir' => 'produits/manteau/noir.jpg',
            'bordo' => 'produits/manteau/bordeaux.jpg',
        ]);

    foreach ($product->meta['images'] as $image) {
        expect(file_exists(public_path($image)))->toBeTrue();
    }

    $this->assertDatabaseHas('categories', [
        'slug' => 'manteaux',
        'status' => 'active',
    ]);
    $this->assertDatabaseCount('categories', 1);
    $this->assertDatabaseCount('products', 1);
    $this->assertDatabaseCount('product_variants', 20);
    $this->assertDatabaseHas('sizes', [
        'slug' => '2xl',
        'name' => '2XL',
    ]);
    $this->assertDatabaseMissing('sizes', ['slug' => 'xxl']);
    $this->assertDatabaseHas('product_variants', [
        'sku' => 'TRE-BEI-2XL',
        'status' => 'active',
    ]);
});
