<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Rating;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'brand' => 'required|string',
            'category' => 'required|string',
            'image_one' => 'required|image|max:2048',
            'image_two' => 'nullable|image|max:2048',
            'original_price' => 'nullable|string',
            'price' => 'required|string',
        ]);

        $slug = Str::slug($validatedData['title'], '-');

        $product = new Product($validatedData);
        if ($request->hasFile('image_one')) {
            $image = $request->file('image_one');
            $filename = $slug . '-1.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/product-image', $filename);
            $product->image_one = url(Storage::url($path));
        }
        if ($request->hasFile('image_two')) {
            $image = $request->file('image_two');
            $filename = $slug . '-2.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/product-image', $filename);
            $product->image_two = url(Storage::url($path));
        }
        $product->slug = $slug;
        $product->save();

        return response()->json(['product' => $product, 'message' => 'Product created'], 201);
    }

    public function index()
    {
        // Get all products with their average ratings
        $products = Product::select('products.*')
            ->selectRaw('(SELECT ROUND(AVG(value), 0) FROM ratings WHERE ratings.product_id = products.id) as average_rating')->selectRaw('(SELECT COUNT(DISTINCT user_id) FROM ratings WHERE ratings.product_id = products.id) as user_rating_count')->orderByDesc('id')
            ->get();

        return response()->json($products, 200);
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'keyword' => 'required|string|min:1',
        ]);

        $keyword = '%' . $validatedData['keyword'] . '%';
        $products = Product::where('title', 'like', $keyword)->get();

        return response()->json($products, 200);
    }

    public function count()
    {
        $productCount = Product::count();

        return response()->json($productCount, 200);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json(['message' => 'Product deleted'], 200);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json($product, 200);
    }

    public function getProductBySlug($slug)
    {
        $product = Product::select('products.*')
        ->selectRaw('(SELECT ROUND(AVG(value), 0) FROM ratings WHERE ratings.product_id = products.id) as average_rating')
        ->selectRaw('(SELECT COUNT(DISTINCT user_id) FROM ratings WHERE ratings.product_id = products.id) as user_rating_count')
        ->where('slug', $slug)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product, 200);
    }


    public function getUserRating($slug)
    {
        $product = Product::where('slug', $slug)->first();
        $user_id = Auth::user()->id;
        $userRating = Rating::select('value')
            ->where('product_id', $product->id)
            ->where('user_id', $user_id)
            ->first();

        $user_rating = $userRating ? $userRating->value : 0;

        return response()->json($user_rating, 200);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'brand' => 'required|string',
            'category' => 'required|string',
            'image_one' => 'nullable|max:2048',
            'image_two' => 'nullable|max:2048',
            'original_price' => 'nullable|string',
            'price' => 'required|string',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $slug = Str::slug($validatedData['title'], '-');

        $product->title = $validatedData['title'];
        $product->description = $validatedData['description'];
        $product->brand = $validatedData['brand'];
        $product->category = $validatedData['category'];
        $product->original_price = $validatedData['original_price'];
        $product->price = $validatedData['price'];

        if ($request->hasFile('image_one')) {
            if ($product->image_one) {
                Storage::delete($product->image_one);
            }
            $image = $request->file('image_one');
            $filename = $slug . '-1.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/product-image', $filename);
            $product->image_one = url(Storage::url($path));
        }

        if ($request->hasFile('image_two')) {
            if ($product->image_two) {
                Storage::delete($product->image_two);
            }
            $image = $request->file('image_two');
            $filename = $slug . '-2.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/product-image', $filename);
            $product->image_two = url(Storage::url($path));
        }

        $product->slug = $slug;
        $product->save();

        return response()->json(['product' => $product, 'message' => 'Product updated'], 200);
    }

    public function productsByCategory($category)
    {
        $products = Product::select('products.*')
            ->selectRaw('(SELECT ROUND(AVG(value), 0) FROM ratings WHERE ratings.product_id = products.id) as average_rating')->selectRaw('(SELECT COUNT(DISTINCT user_id) FROM ratings WHERE ratings.product_id = products.id) as user_rating_count')
            ->where('category', $category)
            ->get();

        return response()->json($products, 200);
    }

    public function productsByBrand($brand)
    {
        $products = Product::select('products.*')
            ->selectRaw('(SELECT ROUND(AVG(value), 0) FROM ratings WHERE ratings.product_id = products.id) as average_rating')->selectRaw('(SELECT COUNT(DISTINCT user_id) FROM ratings WHERE ratings.product_id = products.id) as user_rating_count')
            ->where('brand', $brand)
            ->get();

        return response()->json($products, 200);
    }

    public function countProductsLast30Days()
    {
        $totalProducts = Product::count();

        $last30Days = Carbon::now()->subDays(30);
        $newProductsLast30Days = Product::where('created_at', '>=', $last30Days)->count();

        return response()->json([
            'total_products' => $totalProducts,
            'new_products_last_30_days' => $newProductsLast30Days
        ], 200);
    }

    public function getTopRatedProducts()
    {
        $topRatedProducts = Product::select('products.*')
        ->selectRaw('(SELECT ROUND(AVG(value), 0) FROM ratings WHERE ratings.product_id = products.id) as average_rating')
        ->selectRaw('(SELECT COUNT(DISTINCT user_id) FROM ratings WHERE ratings.product_id = products.id) as user_rating_count')
        ->orderByDesc('average_rating')
        ->orderByDesc('user_rating_count')
        ->take(2)
            ->get();

        return response()->json($topRatedProducts, 200);
    }

}
