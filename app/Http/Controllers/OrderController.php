<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        return response()->json($orders, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cart' => 'required',
            'subtotal' => 'required',
            'total' => 'required',
            'delivery' => 'required',
            'user_id' => 'required|exists:users,id',
            'region' => 'required',
        ]);

        $user = User::findOrFail($validatedData['user_id']);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }
        $validatedData['order_number'] = 'Order-' . Str::random(8);
        $validatedData['user_firstname'] = $user->firstname;
        $validatedData['user_address'] = $user->address;
        $validatedData['user_phone'] = $user->phone;

        $order = Order::create($validatedData);

        return response()->json(['message' => 'Order processing', $order], 201);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order, 201);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'status' => 'string|in:pending,in-transit,delivered,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $validatedData['status'];
        $order->save();

        return response()->json(['message' => 'Status changed', $order], 201);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string|min:1',
        ]);

        $keyword = '%' . $validatedData['keyword'] . '%';
        $orders = Order::where('order_number', 'like', $keyword)->get();

        return response()->json($orders, 200);
    }

    public function getOrdersByUserId($user_id)
    {
        $orders = Order::where('user_id', $user_id)->get();


        return response()->json($orders, 200);
    }


    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully'], 200);
    }

    public function count()
    {
        $orderCount = Order::count();

        return response()->json($orderCount, 200);
    }

    public function sum()
    {
        $totalRevenue = Order::sum('total');

        return response()->json($totalRevenue, 200);
    }

    public function monthly()
    {
        $monthlyOrders = Order::selectRaw('YEAR(created_at) year, MONTH(created_at) month, COUNT(*) count')
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

        $labels = [];
        $values = [];

        foreach ($monthlyOrders as $order) {
            $labels[] = $order->year . '-' . str_pad($order->month, 2, '0', STR_PAD_LEFT);
            $values[] = $order->count;
        }

        $data = [
            'labels' => $labels,
            'values' => $values,
        ];

        return response()->json($data, 200);
    }
}
