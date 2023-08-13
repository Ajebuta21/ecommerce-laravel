<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
        ]);

        $category = new Category($validatedData);
        $category->save();

        return response()->json(['category' => $category, 'message' => 'Category created'], 201);
    }

    public function index()
    {
        $categories = Category::all();

        return response()->json($categories, 200);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string|min:1',
        ]);

        $keyword = '%' . $validatedData['keyword'] . '%';
        $categories = Category::where('name', 'like', $keyword)->get();

        return response()->json($categories, 200);
    }

    public function count()
    {
        $categoryCount = Category::count();

        return response()->json($categoryCount, 200);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if(!$category){
            return response()->json(['message' => 'Category not found'], 404);
        }
        Product::where('category', $category->name)->delete();

        $category->delete();

        return response()->json(['message' => 'Category and associated products deleted successfully'], 200);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);

        return response()->json($category, 200);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string',
        ]);

        Product::where('category', $category->name)->update(['category' => $validatedData['name']]);
        $category->update($validatedData);


        return response()->json(['message' => 'Category field updated'], 200);
    }

    public function countCategoriesLast30Days()
    {
        $totalCategories = Category::count();

        $last30Days = Carbon::now()->subDays(30);
        $newCategoriesLast30Days = Category::where('created_at', '>=', $last30Days)->count();

        return response()->json([
            'total_categories' => $totalCategories,
            'new_categories_last_30_days' => $newCategoriesLast30Days
        ], 200);
    }

}
