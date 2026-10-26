<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tailles</h2>
            <a href="{{ route('admin.sizes.create') }}" class="px-4 py-2 bg-gray-900 text-white rounded text-sm">Nouvelle taille</a>
        </div>
    </x-slot>

    <div class="py-8"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @include('admin.partials.flash')

    <table class="w-full bg-white rounded shadow-sm text-sm">
        <thead class="border-b"><tr><th class="p-3 text-start">Nom</th><th class="p-3 text-start">Slug</th><th></th></tr></thead>
        <tbody class="divide-y">
        @foreach ($sizes as $size)
            <tr>
                <td class="p-3">{{ $size->name }}</td>
                <td class="p-3">{{ $size->slug }}</td>
                <td class="p-3 text-end space-x-3">
                    <a href="{{ route('admin.sizes.edit', $size) }}" class="underline">Modifier</a>
                    <form method="POST" action="{{ route('admin.sizes.destroy', $size) }}" class="inline" onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 underline">Supprimer</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $sizes->links() }}</div>
    </div></div>
</x-app-layout>
