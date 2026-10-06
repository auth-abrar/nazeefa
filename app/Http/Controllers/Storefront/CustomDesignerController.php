<?php

namespace App\Http\Controllers\Storefront;

use App\Domain\Catalog\Models\Product;
use App\Domain\POD\Models\Artwork;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class CustomDesignerController
{
    public function show(Request $request): Response
    {
        $productId = $request->input('product_id');
        $customizableProducts = Product::with(['variants'])
            ->where('is_customizable', true)
            ->where('is_active', true)
            ->get();

        $selectedProduct = $productId
            ? $customizableProducts->firstWhere('id', (int) $productId) ?? $customizableProducts->first()
            : $customizableProducts->first();

        return Inertia::render('storefront/CustomDesigner', [
            'products' => $customizableProducts,
            'selectedProduct' => $selectedProduct,
        ]);
    }

    public function uploadArtwork(Request $request): JsonResponse
    {
        $request->validate([
            'artwork' => ['required', 'image', 'mimes:png,jpg,jpeg,svg', 'max:25600'], // 25MB max
            'placement' => ['required', 'in:front,back,left_chest,right_chest,sleeve'],
            'canvas_metadata' => ['nullable', 'array'],
        ]);

        $file = $request->file('artwork');
        $path = $file->store('artworks', 'public');

        $artwork = Artwork::create([
            'user_id' => auth()->id(),
            'file_path' => Storage::url($path),
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size_bytes' => $file->getSize(),
            'print_placement' => $request->input('placement'),
            'canvas_metadata' => $request->input('canvas_metadata'),
        ]);

        return response()->json([
            'status' => 'success',
            'artwork_id' => $artwork->id,
            'file_url' => $artwork->file_path,
        ]);
    }
}
