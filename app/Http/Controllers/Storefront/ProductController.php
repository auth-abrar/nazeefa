<?php

namespace App\Http\Controllers\Storefront;

use App\Domain\Catalog\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ProductController
{
    public function show(string $slug): Response
    {
        $product = Product::with(['category', 'variants' => fn ($q) => $q->where('is_active', true)])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedProducts = Product::with(['variants'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return Inertia::render('storefront/ProductDetail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
