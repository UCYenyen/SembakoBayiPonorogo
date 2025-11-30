<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public static function getAllProducts(){
        $products = Product::all();
        return $products;
    }

    public function getProductById($id){
        $product = Product::findOrFail($id);
        return view('product-details', ['product' => $product]);
    }

    public function create(Request $request){
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image_url' => 'required|string',
            'image_public_id' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ]);

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image_url' => $request->image_url,
            'image_public_id' => $request->image_public_id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
        ]);

         return redirect('/');
    }

    public function editProduct(Request $request, $id){
        $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'image_url' => 'sometimes|required|string',
            'image_public_id' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'brand_id' => 'sometimes|required|exists:brands,id',
        ]);

        $product = Product::findOrFail($id);

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

    public function hideProduct($id){
        $product = Product::findOrFail($id);
        $product->is_hidden = true;
        $product->save();
        return redirect('/');
    }

    public function setProductOnSale(Request $request, $id){
        $request->validate([
            'discount_amount' => 'required|numeric|min:0|max:100',
        ]);

        $product = Product::findOrFail($id);
        $product->is_on_sale = true;
        $product->discount_amount = $request->discount_amount;
        $product->save();
        return redirect('/');
    }

    public function delete($id){
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect('/');
    }
}
