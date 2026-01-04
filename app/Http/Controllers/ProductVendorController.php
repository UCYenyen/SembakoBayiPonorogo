<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Vendor;
use App\Models\ProductVendor;
use Illuminate\Http\Request;

class ProductVendorController extends Controller
{
    // Menampilkan vendor untuk product tertentu
    public function showVendorsForProduct(Product $product)
    {
        $product->load('vendors');
        $availableVendors = Vendor::whereNotIn('id', $product->vendors->pluck('id'))->get();
        
        return view('dashboard.admin.products.vendor', compact('product', 'availableVendors'));
    }

    public function attachVendorToProduct(Request $request, Product $product)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        if ($product->vendors()->where('vendor_id', $request->vendor_id)->exists()) {
            return redirect()->back()->with('error', 'Vendor sudah terhubung dengan produk ini!');
        }

        $product->vendors()->attach($request->vendor_id);

        return redirect()->back()->with('success', 'Vendor berhasil ditambahkan ke produk!');
    }

    public function detachVendorFromProduct(Product $product, Vendor $vendor)
    {
        $product->vendors()->detach($vendor->id);

        return redirect()->back()->with('success', 'Vendor berhasil dihapus dari produk!');
    }

    public function showProductsForVendor(Vendor $vendor)
    {
        $vendor->load(['products.category', 'products.brand']);
        $availableProducts = Product::whereNotIn('id', $vendor->products->pluck('id'))
                                    ->where('is_hidden', false)
                                    ->with(['category', 'brand'])
                                    ->get();
        
        return view('dashboard.admin.vendors.product', compact('vendor', 'availableProducts'));
    }
    public function attachProductToVendor(Request $request, Vendor $vendor)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        if ($vendor->products()->where('product_id', $request->product_id)->exists()) {
            return redirect()->back()->with('error', 'Produk sudah terhubung dengan vendor ini!');
        }

        $vendor->products()->attach($request->product_id);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke vendor!');
    }

    // Menghapus product dari vendor
    public function detachProductFromVendor(Vendor $vendor, Product $product)
    {
        $vendor->products()->detach($product->id);

        return redirect()->back()->with('success', 'Produk berhasil dihapus dari vendor!');
    }
}