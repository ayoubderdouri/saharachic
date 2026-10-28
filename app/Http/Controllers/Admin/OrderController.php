<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public const STATUSES = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];

    public function index(Request $request)
    {
        $data = $request->validate(['status' => ['nullable', 'string', Rule::in(self::STATUSES)]]);

        return view('admin.orders.index', [
            'orders' => Order::when($data['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', [
            'order' => $order->load('lines', 'user'),
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate(['status' => ['required', Rule::in(self::STATUSES)]]);
        $order->update($data);

        return back()->with('status', 'Statut mis à jour.');
    }

    public function deliveryNote(Order $order)
    {
        return Pdf::loadView('admin.orders.delivery-note', ['order' => $order->load('lines')])
            ->setPaper('a5', 'portrait')
            ->download('bon-livraison-'.$order->guid.'.pdf');
    }
}
