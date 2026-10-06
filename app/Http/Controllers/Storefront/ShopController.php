<?php

namespace App\Http\Controllers\Storefront;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController
{
    public function index(Request $request): Response
    {
        $query = Product::with(['category', 'variants'])->where('is_active', true);

        if ($categorySlug = $request->input('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($request->boolean('customizable')) {
            $query->where('is_customizable', true);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return Inertia::render('storefront/Shop', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['category', 'customizable', 'search', 'sort']),
        ]);
    }
}
