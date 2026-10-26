<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $size->exists ? 'Modifier la taille' : 'Nouvelle taille' }}</h2>
    </x-slot>

    <div class="py-8"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @include('admin.partials.flash')
    <form method="POST" action="{{ $size->exists ? route('admin.sizes.update', $size) : route('admin.sizes.store') }}" class="bg-white rounded shadow-sm p-6 space-y-4 max-w-xl">
        @csrf
        @if ($size->exists) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium mb-1">Nom</label>
            <input name="name" value="{{ old('name', $size->name) }}" class="w-full rounded border-gray-300" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Slug (auto si vide)</label>
            <input name="slug" value="{{ old('slug', $size->slug) }}" class="w-full rounded border-gray-300">
        </div>
        <button class="px-4 py-2 bg-gray-900 text-white rounded">Enregistrer</button>
    </form>
    </div></div>
</x-app-layout>
