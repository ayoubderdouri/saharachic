<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

function whatsappProductData(Category $category): array
{
    return [
        'category_id' => $category->id,
        'name' => 'Trench',
        'price' => 299,
        'quantity' => 5,
        'status' => 'active',
        'landing_page' => 'produits.trench-elegance',
    ];
}

it('creates and updates the optional product WhatsApp URL', function () {
    $this->actingAs(User::factory()->create());
    $category = Category::create(['name' => 'Manteaux', 'slug' => 'manteaux', 'status' => 'active']);
    $data = whatsappProductData($category);

    $this->post('/admin/products', $data)->assertSessionHasNoErrors();
    $product = Product::where('slug', 'trench')->firstOrFail();
    expect($product->whatsapp_url)->toBeNull();

    $this->put("/admin/products/{$product->id}", $data + ['whatsapp_url' => 'https://wa.me/212600000000'])
        ->assertSessionHasNoErrors();
    expect($product->fresh()->whatsapp_url)->toBe('https://wa.me/212600000000');
    $this->get("/admin/products/{$product->id}/edit")->assertOk()
        ->assertSee('value="https://wa.me/212600000000"', false);

    $this->put("/admin/products/{$product->id}", $data + ['whatsapp_url' => ''])
        ->assertSessionHasNoErrors();
    expect($product->fresh()->whatsapp_url)->toBeNull();
});

it('saves a WhatsApp URL when creating a product', function () {
    $this->actingAs(User::factory()->create());
    $category = Category::create(['name' => 'Manteaux', 'slug' => 'manteaux', 'status' => 'active']);

    $this->post('/admin/products', whatsappProductData($category) + ['whatsapp_url' => 'https://wa.me/212600000000'])
        ->assertSessionHasNoErrors();
    $this->assertDatabaseHas('products', ['slug' => 'trench', 'whatsapp_url' => 'https://wa.me/212600000000']);
});

it('rejects invalid and unsafe WhatsApp URLs', function (string $url) {
    $this->actingAs(User::factory()->create());
    $category = Category::create(['name' => 'Manteaux', 'slug' => 'manteaux', 'status' => 'active']);
    $product = Product::create(whatsappProductData($category) + ['slug' => 'trench', 'whatsapp_url' => 'https://wa.me/212600000000']);

    $this->put("/admin/products/{$product->id}", whatsappProductData($category) + ['whatsapp_url' => $url])
        ->assertSessionHasErrors('whatsapp_url');
    expect($product->fresh()->whatsapp_url)->toBe('https://wa.me/212600000000');
})->with(['invalid' => 'not a url', 'javascript' => 'javascript:alert(1)', 'too long' => 'https://example.com/'.str_repeat('a', 256)]);

it('shows only an accessible WhatsApp icon when the product has a URL', function () {
    $category = Category::create(['name' => 'Manteaux', 'slug' => 'manteaux', 'status' => 'active']);
    $product = Product::create(whatsappProductData($category) + [
        'slug' => 'trench',
        'whatsapp_url' => 'https://wa.me/212600000000?text=hello&other=1',
    ]);

    $this->get('/produits/trench')->assertOk()
        ->assertSee('href="https://wa.me/212600000000?text=hello&amp;other=1"', false)
        ->assertSee('aria-label="تواصلي معنا عبر واتساب"', false)
        ->assertSee('rel="noopener noreferrer"', false);

    $product->update(['whatsapp_url' => null]);
    $this->get('/produits/trench')->assertOk()
        ->assertDontSee('aria-label="تواصلي معنا عبر واتساب"', false)
        ->assertDontSee('wa.me/212600000000');
});
