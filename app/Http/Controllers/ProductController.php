<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\WebpEncoder;

class ProductController extends Controller
{
    //
    public static function getAllProducts()
    {
        $products = Product::all();
        return $products;
    }

    public static function getLatestProducts($limit = 10)
    {
        $products = Product::orderBy('created_at', 'desc')->take($limit)->get();
        return $products;
    }

    public function getProductById($id)
    {
        $product = Product::findOrFail($id);
        return view('product-details', ['product' => $product]);
    }

    public function create(Request $request)
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

        // 🔹 Save product
        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stocks' => $request->stocks,
            // ✅ Simpan HANYA path relatif, bukan full URL
            'image_url' => $path, // 'images/1764898803-693237f38218d.webp'
            'image_public_id' => $imageName,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'is_hidden' => false,
        ]);

        return redirect('/dashboard/admin/products')->with('success', 'Product created successfully!');
    }

    public function editProduct(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'image_file' => 'nullable|image|max:5120',
            'category_id' => 'sometimes|required|exists:categories,id',
            'brand_id' => 'sometimes|required|exists:brands,id',
        ]);

        $product = Product::findOrFail($id);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $imageName = time() . '-' . uniqid() . '.webp';
            $path = 'images/' . $imageName;

            try {
                // Delete old image if it exists
                if ($product->image_public_id) {
                    Storage::disk('public')->delete('images/' . $product->image_public_id);
                }

                $webp = Image::read($file)->scale(width: 1280)
                    ->encode(new WebpEncoder(quality: 85));

                Storage::disk('public')->put($path, $webp);

                // ✅ Simpan HANYA path relatif
                $product->image_url = $path;
                $product->image_public_id = $imageName;
            } catch (\Throwable $e) {
                return back()->withErrors('Image upload failed: ' . $e->getMessage());
            }
        }

        $product->update($request->only([
            'name',
            'description',
            'price',
            'image_url',
            'image_public_id',
            'category_id',
            'brand_id',
        ]));

        return redirect('/');
    }

    public function hideProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->is_hidden = true;
        $product->save();
        return redirect('/');
    }

    public function setProductOnSale(Request $request, $id)
    {
        $request->validate([
            'discount_amount' => 'required|numeric|min:0|max:100',
        ]);

        $product = Product::findOrFail($id);
        $product->is_on_sale = true;
        $product->discount_amount = $request->discount_amount;
        $product->save();
        return redirect('/');
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect('/');
    }
}
