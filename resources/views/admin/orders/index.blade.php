<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Commandes</h2>
    </x-slot>

    <div class="py-8"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @include('admin.partials.flash')
    <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-4 flex flex-wrap items-end gap-3">
        <div>
            <label for="status" class="block text-sm font-medium mb-1">Statut</label>
            <select id="status" name="status" class="rounded border-gray-300 text-sm">
                <option value="">Tous les statuts</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <button class="px-4 py-2 bg-gray-900 text-white rounded text-sm">Filtrer</button>
        @if (request()->filled('status'))<a href="{{ route('admin.orders.index') }}" class="underline text-sm">Réinitialiser</a>@endif
    </form>
    <table class="w-full bg-white rounded shadow-sm text-sm">
        <thead class="border-b"><tr><th class="p-3 text-start">Date</th><th class="p-3 text-start">Client</th><th class="p-3 text-start">Ville</th><th class="p-3 text-start">Total</th><th class="p-3 text-start">Statut</th><th></th></tr></thead>
        <tbody class="divide-y">
        @forelse ($orders as $order)
            <tr>
                <td class="p-3">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td class="p-3">{{ $order->customer_name }} <span class="text-gray-500">{{ $order->phone }}</span></td>
                <td class="p-3">{{ $order->city }}</td>
                <td class="p-3">{{ number_format($order->total, 2) }}</td>
                <td class="p-3">{{ $order->status }}</td>
                <td class="p-3 text-end"><a href="{{ route('admin.orders.show', $order) }}" class="underline">Voir</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="p-3 text-gray-500">Aucune commande.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $orders->links() }}</div>
    </div></div>
</x-app-layout>
