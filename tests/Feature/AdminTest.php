<?php

use App\Models\Category;
use App\Models\Color;
use App\Models\Order;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;

it('redirects guests to login', function () {
    $this->get('/admin/products')->assertRedirect('/login');
    $this->get('/register')->assertNotFound();
});

it('lets an authenticated user browse the admin pages', function () {
    $this->actingAs(User::factory()->create());

    foreach (['orders', 'products', 'products/create', 'categories', 'categories/create', 'colors', 'colors/create', 'sizes', 'sizes/create'] as $path) {
        $this->get("/admin/$path")->assertOk();
    }
});

it('manages categories and products', function () {
    $this->actingAs(User::factory()->create());

    $this->post('/admin/categories', ['name' => 'Robes', 'status' => 'active'])
        ->assertRedirect('/admin/categories');
    $category = Category::firstWhere('slug', 'robes');

    $this->post('/admin/products', [
        'category_id' => $category->id, 'name' => 'Robe Sahara', 'price' => 200,
        'quantity' => 5, 'status' => 'active', 'meta' => '{"fabric":"lin"}',
    ])->assertRedirect();

    $product = Product::firstWhere('slug', 'robe-sahara');
    expect($product->meta)->toBe(['fabric' => 'lin']);

    $this->get("/admin/products/{$product->id}/edit")->assertOk();
    $this->post("/admin/products/{$product->id}/variants", [
        'sku' => 'RS-1', 'quantity' => 3, 'status' => 'active',
    ])->assertRedirect();
    expect($product->variants()->count())->toBe(1);
});

it('updates the status of an order', function () {
    $this->actingAs(User::factory()->create());
    $order = Order::create([
        'status' => 'pending', 'subtotal' => 100, 'shipping' => 0, 'total' => 100,
        'customer_name' => 'Aya', 'phone' => '0600000000', 'address' => 'X', 'city' => 'Dakhla',
    ]);

    $this->get("/admin/orders/{$order->id}")->assertOk();
    $this->patch("/admin/orders/{$order->id}", ['status' => 'shipped'])->assertRedirect();
    expect($order->fresh()->status)->toBe('shipped');
});

it('manages product variants', function () {
    $this->actingAs(User::factory()->create());
    $category = Category::create(['name' => 'Robes', 'slug' => 'robes', 'status' => 'active']);
    $product = Product::create(['category_id' => $category->id, 'name' => 'Robe', 'slug' => 'robe', 'price' => 100, 'quantity' => 0, 'status' => 'active']);
    $red = Color::create(['name' => 'Rouge', 'slug' => 'rouge']);
    $blue = Color::create(['name' => 'Bleu', 'slug' => 'bleu']);
    $m = Size::create(['name' => 'M', 'slug' => 'm']);
    $l = Size::create(['name' => 'L', 'slug' => 'l']);

    $this->post("/admin/products/{$product->id}/variants/generate", [
        'color_ids' => [$red->id, $blue->id], 'size_ids' => [$m->id, $l->id], 'quantity' => 4,
    ])->assertSessionHas('status', fn ($s) => str_starts_with($s, '4 '));
    expect($product->variants()->pluck('sku')->all())->toContain('ROBE-ROUGE-M');

    $this->post("/admin/products/{$product->id}/variants/generate", [
        'color_ids' => [$red->id], 'size_ids' => [$m->id], 'quantity' => 1,
    ])->assertSessionHas('status', fn ($s) => str_starts_with($s, '0 '));

    $this->post("/admin/products/{$product->id}/variants", [
        'color_id' => $red->id, 'size_id' => $m->id, 'sku' => 'OTHER', 'quantity' => 1, 'status' => 'active',
    ])->assertSessionHasErrors('color_id');

    $variant = $product->variants()->first();
    $this->get("/admin/variants/{$variant->id}/edit")->assertOk();
    $this->put("/admin/variants/{$variant->id}", [
        'color_id' => $variant->color_id, 'size_id' => $variant->size_id, 'sku' => 'NEW-SKU',
        'quantity' => 9, 'price' => '120', 'status' => 'inactive', 'meta' => '{"a":1}',
    ])->assertRedirect("/admin/products/{$product->id}/edit");
    expect($variant->fresh())->sku->toBe('NEW-SKU')->quantity->toBe(9)->meta->toBe(['a' => 1]);

    $this->patch("/admin/variants/{$variant->id}", ['quantity' => 2, 'price' => '', 'status' => 'active'])->assertRedirect();
    expect($variant->fresh())->quantity->toBe(2)->meta->toBe(['a' => 1]);

    $this->get('/admin/products?q=robe&status=active')->assertOk()->assertSee('Robe');
    $this->delete("/admin/variants/{$variant->id}")->assertRedirect();
    expect($product->variants()->count())->toBe(3);
});
