<?php

use App\Models\Order;
use App\Models\User;

function makeOrder(array $attributes = []): Order
{
    return Order::create(array_merge([
        'status' => 'pending',
        'subtotal' => 100,
        'shipping' => 20,
        'total' => 120,
        'customer_name' => 'Aya',
        'phone' => '0600000000',
        'address' => 'Rue du marché',
        'city' => 'Dakhla',
    ], $attributes));
}

it('filters orders by status and retains the filter when paginating', function () {
    $this->actingAs(User::factory()->create());
    for ($i = 1; $i <= 21; $i++) {
        makeOrder(['customer_name' => "Client confirmé $i", 'status' => 'confirmed']);
    }
    makeOrder(['customer_name' => 'Client en attente'])->forceFill(['created_at' => now()->addDay()])->save();

    $response = $this->get('/admin/orders?status=confirmed');

    $response->assertOk()
        ->assertSee('Client confirmé')
        ->assertDontSee('Client en attente')
        ->assertSee('status=confirmed&amp;page=2', false)
        ->assertSee('value="confirmed" selected', false);
    $this->get('/admin/orders?status=confirmed&page=2')
        ->assertOk()->assertSee('Client confirmé')->assertDontSee('Client en attente');
    $this->get('/admin/orders')->assertOk()->assertSee('Client en attente');
    $this->get('/admin/orders?status=delivered')->assertOk()->assertSee('Aucune commande.');
});

it('rejects invalid status filters', function () {
    $this->actingAs(User::factory()->create());

    $this->from('/admin/orders')->get('/admin/orders?status=invalid')
        ->assertRedirect('/admin/orders')->assertSessionHasErrors('status');
});

it('protects the delivery note from guests', function () {
    $order = makeOrder();

    $this->get("/admin/orders/{$order->id}/delivery-note")->assertRedirect('/login');
});

it('returns not found for a missing delivery note', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/admin/orders/999999/delivery-note')->assertNotFound();
});

it('downloads a delivery note as an A5 PDF', function () {
    $this->actingAs(User::factory()->create());
    $order = makeOrder();
    $order->lines()->create([
        'product_name' => 'Robe Sahara',
        'quantity' => 2,
        'unit_price' => 50,
        'total' => 100,
        'meta' => ['variant' => 'Rouge / M'],
    ]);

    $response = $this->get("/admin/orders/{$order->id}/delivery-note");

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertDownload("bon-livraison-{$order->guid}.pdf");
    expect($response->getContent())->toStartWith('%PDF-')->toContain('/MediaBox [0.000 0.000 419.530 595.280]');
    $this->get("/admin/orders/{$order->id}")->assertOk()
        ->assertSee(route('admin.orders.delivery-note', $order));
    $this->view('admin.orders.delivery-note', ['order' => $order->load('lines')])
        ->assertSee('Aya')->assertSee('0600000000')->assertSee('Rue du marché')
        ->assertSee('Dakhla')->assertSee('Robe Sahara')->assertSee('Rouge / M')
        ->assertSee('120.00');
});

it('escapes customer and product details on the delivery note', function () {
    $order = makeOrder(['customer_name' => '<script>alert(1)</script>']);
    $order->lines()->create([
        'product_name' => '<img src=x>',
        'quantity' => 1,
        'unit_price' => 100,
        'total' => 100,
    ]);

    $this->view('admin.orders.delivery-note', ['order' => $order->load('lines')])
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertSee('&lt;img src=x&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertDontSee('<img src=x>', false);
});
