<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductLandingController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->status === 'active' && $product->landing_page && view()->exists($product->landing_page), 404);

        $variants = $product->variants()
            ->with(['color', 'size'])
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->get();

        $images = $product->meta['images'] ?? [];

        $colors = $variants->pluck('color')->filter()->unique('id')->values()->map(fn ($color) => [
            'id' => $color->id,
            'name' => $color->name,
            'hex' => $color->hex_code,
            'image' => isset($images[$color->slug]) ? asset($images[$color->slug]) : null,
        ]);

        $sizes = $variants->pluck('size')->filter()->unique('id')->sortBy('id')->values()->map(fn ($size) => [
            'id' => $size->id,
            'name' => $size->name,
        ]);

        $stock = $variants->mapWithKeys(fn ($variant) => [
            $variant->color_id.'-'.$variant->size_id => $variant->id,
        ]);

        $price = (float) ($product->sale_price ?? $product->price);

        return view($product->landing_page, [
            'product' => $product,
            'price' => $price == (int) $price ? (int) $price : $price,
            'colors' => $colors,
            'sizes' => $sizes,
            'stock' => $stock,
            'cover' => $colors->first()['image'] ?? null,
            'inStock' => $variants->isNotEmpty(),
        ]);
    }

    public function order(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->status === 'active', 404);

        $data = $request->validate([
            'variant_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1', 'max:5'],
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^(?:\+212|0)[5-7]\d{8}$/'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
        ], [], [
            'variant_id' => 'اللون والمقاس',
        ]);

        DB::transaction(function () use ($product, $data) {
            $variant = ProductVariant::with(['color', 'size'])
                ->where('product_id', $product->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->find($data['variant_id']);

            if (! $variant || $variant->quantity < $data['quantity']) {
                throw ValidationException::withMessages([
                    'variant_id' => 'هذا اللون أو المقاس غير متوفر بالكمية المطلوبة.',
                ]);
            }

            $unitPrice = (float) ($variant->price ?? $product->sale_price ?? $product->price);
            $total = $unitPrice * $data['quantity'];

            // Livraison incluse dans le prix.
            $order = Order::create([
                'status' => 'pending',
                'subtotal' => $total,
                'shipping' => 0,
                'total' => $total,
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'city' => $data['city'],
                'meta' => ['source' => 'landing:'.$product->slug],
            ]);

            $order->lines()->create([
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'product_name' => $product->meta['name_fr'] ?? $product->name,
                'quantity' => $data['quantity'],
                'unit_price' => $unitPrice,
                'total' => $total,
                'meta' => ['variant' => trim($variant->color?->name.' / '.$variant->size?->name, ' /')],
            ]);

            $variant->decrement('quantity', $data['quantity']);
        });

        return redirect()->route('produits.show', $product)->with('ordered', true);
    }
}
