<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Commandes</h2>
    </x-slot>

    <div class="py-8"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @include('admin.partials.flash')
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
