<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bon de livraison {{ $order->guid }}</title>
    <style>
        @page { margin: 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; }
        h1 { font-size: 18px; margin: 0 0 12px; }
        p { margin: 4px 0; overflow-wrap: break-word; }
        .section { margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { padding: 6px 4px; border-bottom: 1px solid #ddd; text-align: left; vertical-align: top; overflow-wrap: break-word; }
        th { background: #f3f4f6; }
        .number { text-align: right; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Bon de livraison</h1>
    <div class="section">
        <p><strong>Commande :</strong> {{ $order->guid }}</p>
        <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <div class="section">
        <p><strong>Client :</strong> {{ $order->customer_name }}</p>
        <p><strong>Téléphone :</strong> {{ $order->phone }}</p>
        <p><strong>Adresse :</strong> {{ $order->address }}</p>
        <p><strong>Ville :</strong> {{ $order->city }}</p>
    </div>
    <table>
        <thead>
            <tr><th style="width: 46%">Produit</th><th class="number" style="width: 10%">Qté</th><th class="number" style="width: 22%">Prix unit.</th><th class="number" style="width: 22%">Total</th></tr>
        </thead>
        <tbody>
            @foreach ($order->lines as $line)
                <tr>
                    <td>{{ $line->product_name }} @if ($line->meta['variant'] ?? null) ({{ $line->meta['variant'] }}) @endif</td>
                    <td class="number">{{ $line->quantity }}</td>
                    <td class="number">{{ number_format($line->unit_price, 2) }}</td>
                    <td class="number">{{ number_format($line->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="3" class="number">Sous-total</td><td class="number">{{ number_format($order->subtotal, 2) }}</td></tr>
            <tr><td colspan="3" class="number">Livraison</td><td class="number">{{ number_format($order->shipping, 2) }}</td></tr>
            <tr class="total"><td colspan="3" class="number">Montant à encaisser</td><td class="number">{{ number_format($order->total, 2) }}</td></tr>
        </tfoot>
    </table>
</body>
</html>
