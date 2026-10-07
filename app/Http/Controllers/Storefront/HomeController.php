<?php

namespace App\Http\Controllers\Storefront;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Collection;
use App\Domain\Catalog\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController
{
    public function index(Request $request)
    {
        $featuredProducts = collect();
        $categories = collect();
        $activeCollection = null;

        try {
            $featuredProducts = Product::with(['category', 'variants'])
                ->where('is_active', true)
                ->where('is_featured', true)
                ->take(8)
                ->get();

            $categories = Category::where('is_featured', true)
                ->orderBy('sort_order')
                ->get();

            $activeCollection = Collection::with(['products.variants'])
                ->where('is_active', true)
                ->first();
        } catch (\Throwable $e) {
            // Tables might be empty or migrating; continue gracefully
        }

        return Inertia::render('storefront/Home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'featuredCollection' => $activeCollection,
        ]);
    }
}
