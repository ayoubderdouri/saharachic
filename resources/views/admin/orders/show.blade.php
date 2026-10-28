<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Commande {{ $order->guid }}</h2>
            <a href="{{ route('admin.orders.delivery-note', $order) }}" class="px-4 py-2 bg-gray-900 text-white rounded text-sm">Télécharger le bon de livraison A5</a>
        </div>
    </x-slot>

    <div class="py-8"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @include('admin.partials.flash')
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded shadow-sm p-6 text-sm space-y-1">
            <p><strong>Client :</strong> {{ $order->customer_name }}</p>
            <p><strong>Téléphone :</strong> {{ $order->phone }}</p>
            <p><strong>Adresse :</strong> {{ $order->address }}, {{ $order->city }}</p>
            @if ($order->user)<p><strong>Compte :</strong> {{ $order->user->email }}</p>@endif
            <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="bg-white rounded shadow-sm p-6 flex items-end gap-3">
            @csrf @method('PATCH')
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Statut</label>
                <select name="status" class="w-full rounded border-gray-300">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <button class="px-4 py-2 bg-gray-900 text-white rounded">Mettre à jour</button>
        </form>
    </div>

    <table class="w-full bg-white rounded shadow-sm text-sm">
        <thead class="border-b"><tr><th class="p-3 text-start">Produit</th><th class="p-3 text-start">Qté</th><th class="p-3 text-start">Prix unitaire</th><th class="p-3 text-start">Total</th></tr></thead>
        <tbody class="divide-y">
        @foreach ($order->lines as $line)
            <tr>
                <td class="p-3">{{ $line->product_name }} @if ($line->meta['variant'] ?? null)<span class="text-gray-500">({{ $line->meta['variant'] }})</span>@endif</td>
                <td class="p-3">{{ $line->quantity }}</td>
                <td class="p-3">{{ number_format($line->unit_price, 2) }}</td>
                <td class="p-3">{{ number_format($line->total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot class="border-t">
            <tr><td colspan="3" class="p-3 text-end">Sous-total</td><td class="p-3">{{ number_format($order->subtotal, 2) }}</td></tr>
            <tr><td colspan="3" class="p-3 text-end">Livraison</td><td class="p-3">{{ number_format($order->shipping, 2) }}</td></tr>
            <tr class="font-semibold"><td colspan="3" class="p-3 text-end">Total</td><td class="p-3">{{ number_format($order->total, 2) }}</td></tr>
        </tfoot>
    </table>
    </div></div>
</x-app-layout>
