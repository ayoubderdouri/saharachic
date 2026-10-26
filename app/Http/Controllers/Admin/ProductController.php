<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->withCount('variants')
            ->withSum('variants', 'quantity')
            ->when($request->query('q'), fn ($query, $q) => $query->where(
                fn ($query) => $query->where('name', 'like', "%$q%")->orWhere('slug', 'like', "%$q%")
            ))
            ->when($request->query('category'), fn ($query, $id) => $query->where('category_id', $id))
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product(['status' => 'draft']),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $product = Product::create($this->validated($request));

        return redirect()->route('admin.products.edit', $product)->with('status', 'Produit créé.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', [
            'product' => $product->load('variants.color', 'variants.size'),
            'categories' => Category::orderBy('name')->get(),
            'colors' => Color::orderBy('name')->get(),
            'sizes' => Size::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));

        return back()->with('status', 'Produit mis à jour.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Produit supprimé.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $request->merge([
            'slug' => Str::slug($request->input('slug') ?: $request->input('name')),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('products')->ignore($product)],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'quantity' => ['required', 'integer', 'min:0'],
            'landing_page' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'active', 'archived'])],
            'is_featured' => ['boolean'],
            'meta' => ['nullable', 'json'],
        ]);

        $data['meta'] = filled($data['meta'] ?? null) ? json_decode($data['meta'], true) : null;

        return $data;
    }
}
