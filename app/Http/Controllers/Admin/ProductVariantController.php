<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $this->validated($request);
        $this->ensureCombinationIsFree($product, $data);

        $product->variants()->create($data);

        return back()->with('status', 'Variante ajoutée.');
    }

    public function edit(ProductVariant $variant)
    {
        return view('admin.variants.edit', [
            'variant' => $variant->load('product'),
            'colors' => Color::orderBy('name')->get(),
            'sizes' => Size::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $data = $this->validated($request, $variant);
        $this->ensureCombinationIsFree($variant->product, $data, $variant);

        $variant->update($data);

        return redirect()->route('admin.products.edit', $variant->product)->with('status', 'Variante mise à jour.');
    }

    public function quickUpdate(Request $request, ProductVariant $variant)
    {
        $variant->update($request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]));

        return back()->with('status', 'Variante mise à jour.');
    }

    public function generate(Request $request, Product $product)
    {
        $data = $request->validate([
            'color_ids' => ['nullable', 'array'],
            'color_ids.*' => ['exists:colors,id'],
            'size_ids' => ['nullable', 'array'],
            'size_ids.*' => ['exists:sizes,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $colors = Color::whereIn('id', $data['color_ids'] ?? [])->get();
        $sizes = Size::whereIn('id', $data['size_ids'] ?? [])->get();

        if ($colors->isEmpty() && $sizes->isEmpty()) {
            throw ValidationException::withMessages(['color_ids' => 'Choisissez au moins une couleur ou une taille.']);
        }

        $created = 0;

        foreach ($colors->isEmpty() ? [null] : $colors as $color) {
            foreach ($sizes->isEmpty() ? [null] : $sizes as $size) {
                $combination = ['color_id' => $color?->id, 'size_id' => $size?->id];
                $sku = Str::upper(implode('-', array_filter([$product->slug, $color?->slug, $size?->slug])));

                if ($this->combinationExists($product, $combination) || ProductVariant::where('sku', $sku)->exists()) {
                    continue;
                }

                $product->variants()->create($combination + [
                    'sku' => $sku,
                    'quantity' => $data['quantity'],
                    'price' => $data['price'] ?? null,
                    'status' => 'active',
                ]);
                $created++;
            }
        }

        return back()->with('status', "$created variante(s) générée(s).");
    }

    public function destroy(ProductVariant $variant)
    {
        $variant->delete();

        return back()->with('status', 'Variante supprimée.');
    }

    private function validated(Request $request, ?ProductVariant $variant = null): array
    {
        $data = $request->validate([
            'color_id' => ['nullable', 'exists:colors,id'],
            'size_id' => ['nullable', 'exists:sizes,id'],
            'sku' => ['required', 'string', 'max:255', Rule::unique('product_variants', 'sku')->ignore($variant)],
            'quantity' => ['required', 'integer', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'meta' => ['nullable', 'json'],
        ]);

        // Only touch meta when the field was submitted (the quick-add form omits it).
        if ($request->has('meta')) {
            $data['meta'] = filled($data['meta']) ? json_decode($data['meta'], true) : null;
        } else {
            unset($data['meta']);
        }

        return $data;
    }

    private function combinationExists(Product $product, array $data, ?ProductVariant $ignore = null): bool
    {
        return $product->variants()
            ->where('color_id', $data['color_id'] ?? null)
            ->where('size_id', $data['size_id'] ?? null)
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))
            ->exists();
    }

    private function ensureCombinationIsFree(Product $product, array $data, ?ProductVariant $ignore = null): void
    {
        if ($this->combinationExists($product, $data, $ignore)) {
            throw ValidationException::withMessages(['color_id' => 'Cette combinaison couleur / taille existe déjà.']);
        }
    }
}
