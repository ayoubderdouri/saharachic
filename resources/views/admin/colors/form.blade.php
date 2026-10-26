<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $color->exists ? 'Modifier la couleur' : 'Nouvelle couleur' }}</h2>
    </x-slot>

    <div class="py-8"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @include('admin.partials.flash')
    <form method="POST" action="{{ $color->exists ? route('admin.colors.update', $color) : route('admin.colors.store') }}" class="bg-white rounded shadow-sm p-6 space-y-4 max-w-xl">
        @csrf
        @if ($color->exists) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium mb-1">Nom</label>
            <input name="name" value="{{ old('name', $color->name) }}" class="w-full rounded border-gray-300" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Slug (auto si vide)</label>
            <input name="slug" value="{{ old('slug', $color->slug) }}" class="w-full rounded border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Code hexadécimal (#RRGGBB)</label>
            <input name="hex_code" value="{{ old('hex_code', $color->hex_code) }}" placeholder="#000000" class="w-full rounded border-gray-300">
        </div>
        <button class="px-4 py-2 bg-gray-900 text-white rounded">Enregistrer</button>
    </form>
    </div></div>
</x-app-layout>
