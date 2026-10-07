<?php

namespace App\Http\Controllers\Storefront;

use App\Domain\Catalog\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomDesignerController
{
    public function show(Request $request)
    {
        $products = collect();
        try {
            $products = Product::where('is_active', true)
                ->where('is_customizable', true)
                ->with(['variants'])
                ->get();
        } catch (\Throwable $e) {
            // Cold start fallback
        }

        return Inertia::render('storefront/CustomDesigner', [
            'customizableProducts' => $products,
            'selectedProduct' => $products->first(),
        ]);
    }

    public function uploadArtwork(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,webp,svg|max:20480',
        ]);

        $path = $request->file('file')->store('artworks', 'public');

        return response()->json([
            'url' => asset('storage/' . $path),
            'filename' => basename($path),
            'format' => $request->file('file')->getClientOriginalExtension(),
        ]);
    }
}
