<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
        ]);

        $brand = new Brand($validatedData);
        $brand->save();

        return response()->json(['brand' => $brand, 'message' => 'Brand created'], 201);
    }

    public function index()
    {
        $brands = Brand::all();

        return response()->json($brands, 200);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string|min:1',
        ]);

        $keyword = '%' . $validatedData['keyword'] . '%';
        $brands = Brand::where('name', 'like', $keyword)->get();

        return response()->json($brands, 200);
    }

    public function count()
    {
        $brandCount = Brand::count();

        return response()->json($brandCount, 200);
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        if (!$brand) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        Product::where('brand', $brand->name)->delete();

        $brand->delete();

        return response()->json(['message' => 'Brand and associated products deleted successfully'], 200);
    }

    public function show($id)
    {
        $brand = Brand::findOrFail($id);

        return response()->json($brand, 200);
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string',
        ]);

        Product::where('brand', $brand->name)->update(['brand' => $validatedData['name']]);
        $brand->update($validatedData);


        return response()->json(['message' => 'Category field updated'], 200);
    }

    public function countBrandsLast30Days()
    {
        $totalBrands = Brand::count();

        $last30Days = Carbon::now()->subDays(30);
        $newBrandsLast30Days = Brand::where('created_at', '>=', $last30Days)->count();

        return response()->json([
            'total_brands' => $totalBrands,
            'new_brands_last_30_days' => $newBrandsLast30Days
        ], 200);
    }
}
