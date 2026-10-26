<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Catégories</h2>
            <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-gray-900 text-white rounded text-sm">Nouvelle catégorie</a>
        </div>
    </x-slot>

    <div class="py-8"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @include('admin.partials.flash')

    <table class="w-full bg-white rounded shadow-sm text-sm">
        <thead class="text-start border-b"><tr><th class="p-3 text-start">Nom</th><th class="p-3 text-start">Slug</th><th class="p-3 text-start">Statut</th><th class="p-3 text-start">Produits</th><th></th></tr></thead>
        <tbody class="divide-y">
        @foreach ($categories as $category)
            <tr>
                <td class="p-3">{{ $category->name }}</td>
                <td class="p-3">{{ $category->slug }}</td>
                <td class="p-3">{{ $category->status }}</td>
                <td class="p-3">{{ $category->products_count }}</td>
                <td class="p-3 text-end space-x-3">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="underline">Modifier</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 underline">Supprimer</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $categories->links() }}</div>
    </div></div>
</x-app-layout>
