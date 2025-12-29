<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\WebpEncoder;

class ProductController extends Controller
{
    public static function getAllProducts()
    {
        $products = Product::all();
        return $products;
    }

    public static function getLatestProducts($limit = 10)
    {
        $products = Product::where('is_hidden', false)->orderBy('created_at', 'desc')->take($limit)->get();
        return $products;
    }

    public function showDetails(Product $product)
    {
        $similarProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_hidden', false)
            ->with(['category', 'brand'])
            ->limit(4)
            ->get();

        return view('shop.product-detail', [
            'product' => $product,
            'similarProducts' => $similarProducts,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stocks' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'image_file' => 'required|image|max:5120',
        ]);

        $file = $request->file('image_file');
        $imageName = time() . '-' . uniqid() . '.webp';
        $path = 'images/' . $imageName;

        try {
            $webp = Image::read($file)->scale(width: 1280)
                ->encode(new WebpEncoder(quality: 85));

            Storage::disk('public')->put($path, $webp);
        } catch (\Throwable $e) {
            return back()->withErrors('Image upload failed: ' . $e->getMessage());
        }

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stocks' => $request->stocks,
            'image_url' => $path,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'is_hidden' => false,
        ]);

        return redirect('/dashboard/admin/products/create')->with('success', 'Product created successfully!');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'stocks' => 'sometimes|required|integer',
            'image_file' => 'nullable|image|max:5120',
            'category_id' => 'sometimes|required|exists:categories,id',
            'brand_id' => 'sometimes|required|exists:brands,id',
        ]);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $imageName = time() . '-' . uniqid() . '.webp';
            $path = 'images/' . $imageName;

            try {
                if ($product->image_url) {
                    Storage::disk('public')->delete($product->image_url);
                }

                $webp = Image::read($file)->scale(width: 1280)
                    ->encode(new WebpEncoder(quality: 85));

                Storage::disk('public')->put($path, $webp);

                $product->image_url = $path;
            } catch (\Throwable $e) {
                return back()->withErrors('Image upload failed: ' . $e->getMessage());
            }
        }

        $product->update($request->only([
            'name',
            'description',
            'price',
            'stocks',
            'category_id',
            'brand_id',
        ]));

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function toggleVisibility(Product $product)
    {
        $product->is_hidden = !$product->is_hidden;
        $product->save();

        $status = $product->is_hidden ? 'hidden' : 'visible';
        return redirect()->route('admin.products.index')->with('success', "Product is now {$status}!");
    }

    public function destroy(Product $product)
    {
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }

    public function liveSearch(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::select('id', 'name', 'price', 'stocks', 'image_url', 'category_id', 'brand_id')
            ->with(['category:id,name', 'brand:id,name'])
            ->where('is_hidden', false)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();

        return response()->json($products);
    }
}