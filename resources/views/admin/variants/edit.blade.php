<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Variante {{ $variant->sku }}</h2>
            <a href="{{ route('admin.products.edit', $variant->product) }}" class="underline text-sm text-gray-600 dark:text-gray-300">&larr; {{ $variant->product->name }}</a>
        </div>
    </x-slot>

    <div class="py-8"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @include('admin.partials.flash')

    <form method="POST" action="{{ route('admin.variants.update', $variant) }}" class="bg-white rounded shadow-sm p-6 grid md:grid-cols-2 gap-4 max-w-3xl">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">SKU</label>
            <input name="sku" value="{{ old('sku', $variant->sku) }}" class="w-full rounded border-gray-300" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Statut</label>
            <select name="status" class="w-full rounded border-gray-300">
                @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $variant->status) === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Couleur</label>
            <select name="color_id" class="w-full rounded border-gray-300">
                <option value="">—</option>
                @foreach ($colors as $color)
                    <option value="{{ $color->id }}" @selected(old('color_id', $variant->color_id) == $color->id)>{{ $color->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Taille</label>
            <select name="size_id" class="w-full rounded border-gray-300">
                <option value="">—</option>
                @foreach ($sizes as $size)
                    <option value="{{ $size->id }}" @selected(old('size_id', $variant->size_id) == $size->id)>{{ $size->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Quantité</label>
            <input type="number" min="0" name="quantity" value="{{ old('quantity', $variant->quantity) }}" class="w-full rounded border-gray-300" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Prix (vide = prix du produit)</label>
            <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $variant->price) }}" class="w-full rounded border-gray-300">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">Meta (JSON)</label>
            <textarea name="meta" rows="3" class="w-full rounded border-gray-300 font-mono text-sm">{{ old('meta', $variant->meta ? json_encode($variant->meta, JSON_PRETTY_PRINT) : '') }}</textarea>
        </div>
        <div class="md:col-span-2"><button class="px-4 py-2 bg-gray-900 text-white rounded">Enregistrer</button></div>
    </form>
    </div></div>
</x-app-layout>
