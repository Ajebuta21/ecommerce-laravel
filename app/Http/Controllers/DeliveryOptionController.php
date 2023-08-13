<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOption;
use Illuminate\Http\Request;

class DeliveryOptionController extends Controller
{
    public function index()
    {
        $deliveryOptions = DeliveryOption::all();
        return response()->json($deliveryOptions, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'location' => 'required|string',
            'amount' => 'required|string',
        ]);

        $deliveryOption = DeliveryOption::create($validatedData);

        return response()->json(['message' => 'Delivery option created', $deliveryOption], 201);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string|min:1',
        ]);

        $keyword = '%' . $validatedData['keyword'] . '%';
        $deliveryOption = DeliveryOption::where('location', 'like', $keyword)->get();

        return response()->json($deliveryOption, 200);
    }

    public function count()
    {
        $deliverOptionsCount = DeliveryOption::count();

        return response()->json($deliverOptionsCount, 200);
    }

    public function show($id)
    {
        $deliveryOption = DeliveryOption::findOrFail($id);
        return response()->json($deliveryOption, 200);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'location' => 'required|string',
            'amount' => 'required|string',
        ]);

        $deliveryOption = DeliveryOption::findOrFail($id);
        $deliveryOption->update($validatedData);

        return response()->json(['message' => 'Delivery option deleted successfully', $deliveryOption], 200);
    }

    public function destroy($id)
    {
        $deliveryOption = DeliveryOption::findOrFail($id);
        $deliveryOption->delete();

        return response()->json(['message' => 'Delivery option deleted successfully'], 200);
    }
}
