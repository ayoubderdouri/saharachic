<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Produits</h2>
            <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-gray-900 text-white rounded text-sm">Nouveau produit</a>
        </div>
    </x-slot>

    <div class="py-8"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @include('admin.partials.flash')

    <form method="GET" class="mb-4 flex flex-wrap gap-3 items-end">
        <input name="q" value="{{ request('q') }}" placeholder="Rechercher…" class="rounded border-gray-300 text-sm">
        <select name="category" class="rounded border-gray-300 text-sm">
            <option value="">Toutes catégories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded border-gray-300 text-sm">
            <option value="">Tous statuts</option>
            @foreach (['draft', 'active', 'archived'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 bg-gray-900 text-white rounded text-sm">Filtrer</button>
        @if (request()->hasAny(['q', 'category', 'status']))<a href="{{ route('admin.products.index') }}" class="underline text-sm">Réinitialiser</a>@endif
    </form>

    <table class="w-full bg-white rounded shadow-sm text-sm">
        <thead class="border-b"><tr><th class="p-3 text-start">Nom</th><th class="p-3 text-start">Catégorie</th><th class="p-3 text-start">Prix</th><th class="p-3 text-start">Stock</th><th class="p-3 text-start">Variantes</th><th class="p-3 text-start">Statut</th><th></th></tr></thead>
        <tbody class="divide-y">
        @forelse ($products as $product)
            <tr>
                <td class="p-3">{{ $product->name }} @if ($product->is_featured)<span title="Vedette">★</span>@endif</td>
                <td class="p-3">{{ $product->category->name }}</td>
                <td class="p-3">
                    @if ($product->sale_price)
                        <span class="text-red-600">{{ number_format($product->sale_price, 2) }}</span> <span class="line-through text-gray-400">{{ number_format($product->price, 2) }}</span>
                    @else
                        {{ number_format($product->price, 2) }}
                    @endif
                </td>
                <td class="p-3">{{ $product->variants_count ? (int) $product->variants_sum_quantity : $product->quantity }}</td>
                <td class="p-3">{{ $product->variants_count }}</td>
                <td class="p-3">{{ $product->status }}</td>
                <td class="p-3 text-end space-x-3">
                    @if ($product->status === 'active' && $product->landing_page && view()->exists($product->landing_page))
                        <button type="button" class="underline"
                                x-data="{ copied: false }"
                                @click="
                                    const url = @js(route('produits.show', $product));
                                    const done = () => { copied = true; setTimeout(() => copied = false, 2000); };
                                    if (navigator.clipboard && window.isSecureContext) {
                                        navigator.clipboard.writeText(url).then(done);
                                    } else {
                                        const input = document.createElement('input');
                                        input.value = url;
                                        document.body.appendChild(input);
                                        input.select();
                                        document.execCommand('copy');
                                        input.remove();
                                        done();
                                    }
                                "
                                x-text="copied ? 'Lien copié ✓' : 'Copier le lien'">Copier le lien</button>
                    @endif
                    <a href="{{ route('admin.products.edit', $product) }}" class="underline">Modifier</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 underline">Supprimer</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="p-3 text-gray-500">Aucun produit.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $products->links() }}</div>
    </div></div>
</x-app-layout>
