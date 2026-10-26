<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $product->exists ? 'Modifier le produit' : 'Nouveau produit' }}</h2>
    </x-slot>

    <div class="py-8"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @include('admin.partials.flash')
    <form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="bg-white rounded shadow-sm p-6 grid md:grid-cols-2 gap-4">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium mb-1">Nom</label>
            <input name="name" value="{{ old('name', $product->name) }}" class="w-full rounded border-gray-300" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Slug (auto si vide)</label>
            <input name="slug" value="{{ old('slug', $product->slug) }}" class="w-full rounded border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Catégorie</label>
            <select name="category_id" class="w-full rounded border-gray-300" required>
                <option value="">Choisir…</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Statut</label>
            <select name="status" class="w-full rounded border-gray-300">
                @foreach (['draft', 'active', 'archived'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $product->status) === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Prix</label>
            <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" class="w-full rounded border-gray-300" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Prix promo</label>
            <input type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="w-full rounded border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Quantité</label>
            <input type="number" min="0" name="quantity" value="{{ old('quantity', $product->quantity ?? 0) }}" class="w-full rounded border-gray-300" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Landing page</label>
            <input name="landing_page" value="{{ old('landing_page', $product->landing_page) }}" class="w-full rounded border-gray-300">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="4" class="w-full rounded border-gray-300">{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Meta (JSON)</label>
            <textarea name="meta" rows="3" class="w-full rounded border-gray-300 font-mono text-sm">{{ old('meta', $product->meta ? json_encode($product->meta, JSON_PRETTY_PRINT) : '') }}</textarea>
        </div>
        <label class="flex items-center gap-2 md:col-span-2">
            <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300" @checked(old('is_featured', $product->is_featured))>
            Produit vedette
        </label>
        <div class="md:col-span-2"><button class="px-4 py-2 bg-gray-900 text-white rounded">Enregistrer</button></div>
    </form>

    @if ($product->exists)
        <h2 class="text-xl font-semibold mt-10 mb-4">Variantes</h2>

        <table class="w-full bg-white rounded shadow-sm text-sm mb-6">
            <thead class="border-b"><tr><th class="p-3 text-start">SKU</th><th class="p-3 text-start">Variante</th><th class="p-3 text-start">Quantité</th><th class="p-3 text-start">Prix</th><th class="p-3 text-start">Statut</th><th></th></tr></thead>
            <tbody class="divide-y">
            @forelse ($product->variants as $variant)
                <tr>
                    <td class="p-3">{{ $variant->sku }}</td>
                    <td class="p-3">{{ collect([$variant->color?->name, $variant->size?->name])->filter()->implode(' / ') }}</td>
                    <td class="p-3"><input form="variant-{{ $variant->id }}" type="number" min="0" name="quantity" value="{{ $variant->quantity }}" class="w-20 rounded border-gray-300 text-sm"></td>
                    <td class="p-3"><input form="variant-{{ $variant->id }}" type="number" step="0.01" min="0" name="price" value="{{ $variant->price }}" class="w-24 rounded border-gray-300 text-sm"></td>
                    <td class="p-3">
                        <select form="variant-{{ $variant->id }}" name="status" class="rounded border-gray-300 text-sm">
                            @foreach (['active', 'inactive'] as $status)
                                <option value="{{ $status }}" @selected($variant->status === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-3 text-end space-x-3">
                        <form id="variant-{{ $variant->id }}" method="POST" action="{{ route('admin.variants.quick-update', $variant) }}" class="inline">
                            @csrf @method('PATCH')
                            <button class="underline">Enregistrer</button>
                        </form>
                        <a href="{{ route('admin.variants.edit', $variant) }}" class="underline">Modifier</a>
                        <form method="POST" action="{{ route('admin.variants.destroy', $variant) }}" class="inline" onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 underline">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-3 text-gray-500">Aucune variante.</td></tr>
            @endforelse
            </tbody>
        </table>

        <h3 class="font-semibold mb-2">Ajouter une variante</h3>
        <form method="POST" action="{{ route('admin.variants.store', $product) }}" class="bg-white rounded shadow-sm p-6 grid md:grid-cols-6 gap-3 items-end">
            @csrf
            <div><label class="block text-sm mb-1">SKU</label><input name="sku" class="w-full rounded border-gray-300" required></div>
            <div>
                <label class="block text-sm mb-1">Couleur</label>
                <select name="color_id" class="w-full rounded border-gray-300">
                    <option value="">—</option>
                    @foreach ($colors as $color)<option value="{{ $color->id }}">{{ $color->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Taille</label>
                <select name="size_id" class="w-full rounded border-gray-300">
                    <option value="">—</option>
                    @foreach ($sizes as $size)<option value="{{ $size->id }}">{{ $size->name }}</option>@endforeach
                </select>
            </div>
            <div><label class="block text-sm mb-1">Quantité</label><input type="number" min="0" name="quantity" value="0" class="w-full rounded border-gray-300" required></div>
            <div><label class="block text-sm mb-1">Prix (optionnel)</label><input type="number" step="0.01" min="0" name="price" class="w-full rounded border-gray-300"></div>
            <input type="hidden" name="status" value="active">
            <button class="px-4 py-2 bg-gray-900 text-white rounded">Ajouter</button>
        </form>

        <h3 class="font-semibold mt-8 mb-2">Générer des variantes (couleurs × tailles)</h3>
        <form method="POST" action="{{ route('admin.variants.generate', $product) }}" class="bg-white rounded shadow-sm p-6 grid md:grid-cols-2 gap-4">
            @csrf
            <fieldset>
                <legend class="text-sm font-medium mb-1">Couleurs</legend>
                <div class="flex flex-wrap gap-3">
                    @foreach ($colors as $color)
                        <label class="flex items-center gap-1 text-sm"><input type="checkbox" name="color_ids[]" value="{{ $color->id }}" class="rounded border-gray-300"> {{ $color->name }}</label>
                    @endforeach
                </div>
            </fieldset>
            <fieldset>
                <legend class="text-sm font-medium mb-1">Tailles</legend>
                <div class="flex flex-wrap gap-3">
                    @foreach ($sizes as $size)
                        <label class="flex items-center gap-1 text-sm"><input type="checkbox" name="size_ids[]" value="{{ $size->id }}" class="rounded border-gray-300"> {{ $size->name }}</label>
                    @endforeach
                </div>
            </fieldset>
            <div><label class="block text-sm mb-1">Quantité par variante</label><input type="number" min="0" name="quantity" value="0" class="w-full rounded border-gray-300" required></div>
            <div><label class="block text-sm mb-1">Prix (optionnel)</label><input type="number" step="0.01" min="0" name="price" class="w-full rounded border-gray-300"></div>
            <div class="md:col-span-2"><button class="px-4 py-2 bg-gray-900 text-white rounded">Générer</button> <span class="text-xs text-gray-500">Les combinaisons existantes sont ignorées. SKU = produit-couleur-taille.</span></div>
        </form>
    @endif
    </div></div>
</x-app-layout>
