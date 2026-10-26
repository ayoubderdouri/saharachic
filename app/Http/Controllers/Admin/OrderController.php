<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public const STATUSES = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];

    public function index()
    {
        return view('admin.orders.index', ['orders' => Order::latest()->paginate(20)]);
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
}
