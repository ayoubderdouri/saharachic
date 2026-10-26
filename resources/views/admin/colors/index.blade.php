<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Couleurs</h2>
            <a href="{{ route('admin.colors.create') }}" class="px-4 py-2 bg-gray-900 text-white rounded text-sm">Nouvelle couleur</a>
        </div>
    </x-slot>

    <div class="py-8"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @include('admin.partials.flash')

    <table class="w-full bg-white rounded shadow-sm text-sm">
        <thead class="border-b"><tr><th class="p-3 text-start">Nom</th><th class="p-3 text-start">Slug</th><th class="p-3 text-start">Code</th><th></th></tr></thead>
        <tbody class="divide-y">
        @foreach ($colors as $color)
            <tr>
                <td class="p-3">{{ $color->name }}</td>
                <td class="p-3">{{ $color->slug }}</td>
                <td class="p-3">
                    @if ($color->hex_code)<span class="inline-block w-4 h-4 rounded border align-middle" style="background: {{ $color->hex_code }}"></span> {{ $color->hex_code }}@endif
                </td>
                <td class="p-3 text-end space-x-3">
                    <a href="{{ route('admin.colors.edit', $color) }}" class="underline">Modifier</a>
                    <form method="POST" action="{{ route('admin.colors.destroy', $color) }}" class="inline" onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 underline">Supprimer</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $colors->links() }}</div>
    </div></div>
</x-app-layout>
