<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $category->exists ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}</h2>
    </x-slot>

    <div class="py-8"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @include('admin.partials.flash')
    <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="bg-white rounded shadow-sm p-6 space-y-4 max-w-xl">
        @csrf
        @if ($category->exists) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium mb-1">Nom</label>
            <input name="name" value="{{ old('name', $category->name) }}" class="w-full rounded border-gray-300" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Slug (auto si vide)</label>
            <input name="slug" value="{{ old('slug', $category->slug) }}" class="w-full rounded border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Statut</label>
            <select name="status" class="w-full rounded border-gray-300">
                @foreach (['active', 'inactive'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $category->status) === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <button class="px-4 py-2 bg-gray-900 text-white rounded">Enregistrer</button>
    </form>
    </div></div>
</x-app-layout>
