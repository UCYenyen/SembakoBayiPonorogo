<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])
            ->where('is_hidden', false);

        // Filter by categories (multiple selection)
        if ($request->has('categories') && !empty($request->categories)) {
            $query->whereIn('category_id', $request->categories);
        }

        // Filter by brands (multiple selection)
        if ($request->has('brands') && !empty($request->brands)) {
            $query->whereIn('brand_id', $request->brands);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price !== null) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price !== null) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        // Pagination
        $products = $query->paginate(12)->withQueryString();

        // Get all categories and brands for filter
        $categories = Category::where('level', '1')->with('children')->get();
        $brands = Brand::all();

        return view('shop.index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
