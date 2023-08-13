<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'product_id' => 'required|integer|exists:products,id',
            'value' => 'required|integer|in:1,2,3,4,5',
        ]);

        $rating = Rating::where('user_id', $validatedData['user_id'])
        ->where('product_id', $validatedData['product_id'])
        ->first();

        if ($rating) {
            $rating->update(['value' => $validatedData['value']]);
        } else {
            $rating = Rating::create($validatedData);
        }

        return response()->json(['rating' => $rating, "message" => "You rated this product ". $validatedData['value']." star(s)"], 201);
    }

    public function showUserRating($productId, $userId)
    {
        $rating = Rating::where('product_id', $productId)
            ->where('user_id', $userId)
            ->first();

        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        return response()->json(['rating' => $rating], 200);
    }
}
