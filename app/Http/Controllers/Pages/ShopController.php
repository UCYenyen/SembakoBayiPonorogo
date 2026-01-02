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
        $search = $request->input('search');
        
        $query = Product::with(['category', 'brand'])
            ->where('is_hidden', false);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('categories') && !empty($request->categories)) {
            $selectedCategories = $request->categories;
            $allCategoryIds = [];
            
            foreach ($selectedCategories as $categoryId) {
                $allCategoryIds[] = $categoryId;
                
                $category = Category::find($categoryId);
                if ($category && $category->children->count() > 0) {
                    // Cek apakah ada child dari kategori ini yang juga dipilih
                    $childIds = $category->children->pluck('id')->toArray();
                    $hasSelectedChild = !empty(array_intersect($childIds, $selectedCategories));
                    
                    // Hanya tambahkan children jika tidak ada child yang dipilih secara eksplisit
                    if (!$hasSelectedChild) {
                        foreach ($category->children as $child) {
                            $allCategoryIds[] = $child->id;
                            
                            // Jika ada sub-child (level 3)
                            if ($child->children->count() > 0) {
                                foreach ($child->children as $subChild) {
                                    $allCategoryIds[] = $subChild->id;
                                }
                            }
                        }
                    }
                }
            }
            
            $query->whereIn('category_id', array_unique($allCategoryIds));
        }

        if ($request->has('brands') && !empty($request->brands)) {
            $query->whereIn('brand_id', $request->brands);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

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

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::whereNull('parent_id')
            ->with(['children.children'])
            ->get();
        
        $brands = Brand::all();

        return view('shop.index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'searchQuery' => $search
        ]);
    }
}