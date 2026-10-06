<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Collection;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductVariant;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $tshirts = Category::create([
            'name' => 'T-Shirts & Tops',
            'name_bn' => 'টি-শার্ট ও টপস',
            'slug' => 't-shirts',
            'description' => 'Premium combed cotton t-shirts, drop-shoulder and oversized streetwear.',
            'image_url' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&q=80',
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $customPod = Category::create([
            'name' => 'Custom & Print-On-Demand',
            'name_bn' => 'কাস্টম প্রিন্ট অন ডিমান্ড',
            'slug' => 'custom-print',
            'description' => 'Personalized artwork, direct-to-film DTF printing and corporate apparel.',
            'image_url' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=800&q=80',
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        $hoodies = Category::create([
            'name' => 'Hoodies & Fleece',
            'name_bn' => 'হুডি ও শীতের পোশাক',
            'slug' => 'hoodies',
            'description' => 'Heavyweight 320+ GSM organic fleece hoodies and crewnecks.',
            'image_url' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&q=80',
            'is_featured' => true,
            'sort_order' => 3,
        ]);

        $monsoonDrop = Collection::create([
            'name' => 'Monsoon Minimalist Collection',
            'name_bn' => 'বর্ষা মিনিমালিস্ট কালেকশন',
            'slug' => 'monsoon-minimalist',
            'description' => 'Breathable 220 GSM combed compact cotton for the Bangladeshi tropical climate.',
            'banner_url' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=1600&q=80',
            'is_active' => true,
        ]);

        // Product 1: The Essential Oversized T-Shirt
        $prod1 = Product::create([
            'category_id' => $tshirts->id,
            'name' => 'Nazeefa Signature Oversized Heavy Tee',
            'name_bn' => 'নাজিফা সিগনেচার ওভারসাইজড হেভি টি-শার্ট',
            'slug' => 'signature-oversized-heavy-tee',
            'sku_prefix' => 'NZ-OVT-01',
            'short_description' => '240 GSM heavy combed organic cotton with tailored drop-shoulder fit.',
            'description' => 'Crafted specifically for the Dhaka climate, blending breathable organic cotton with substantial heavyweight drape. Features double-needle cover-stitched seams and a reinforced ribbed collar that maintains its structure wash after wash.',
            'material' => '100% Combed Compact Cotton (240 GSM)',
            'fit_type' => 'oversized',
            'is_customizable' => false,
            'is_featured' => true,
            'is_active' => true,
            'images' => [
                'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=1000&q=85',
                'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=1000&q=85',
                'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=1000&q=85',
            ],
        ]);
        $prod1->collections()->attach($monsoonDrop->id);

        foreach (['S', 'M', 'L', 'XL'] as $size) {
            ProductVariant::create([
                'product_id' => $prod1->id,
                'sku' => "NZ-OVT-01-BLK-{$size}",
                'size' => $size,
                'color_name' => 'Onyx Black',
                'color_hex' => '#111111',
                'price_amount' => 125000, // ৳ 1,250 in poisha
                'compare_at_amount' => 150000, // ৳ 1,500
                'currency' => 'BDT',
                'weight_grams' => 260,
                'stock_on_hand' => 50,
                'stock_reserved' => 0,
                'is_active' => true,
            ]);

            ProductVariant::create([
                'product_id' => $prod1->id,
                'sku' => "NZ-OVT-01-WHT-{$size}",
                'size' => $size,
                'color_name' => 'Bone White',
                'color_hex' => '#F4F4F0',
                'price_amount' => 125000,
                'compare_at_amount' => 150000,
                'currency' => 'BDT',
                'weight_grams' => 260,
                'stock_on_hand' => 40,
                'stock_reserved' => 0,
                'is_active' => true,
            ]);
        }

        // Product 2: Customizable Canvas T-Shirt (POD)
        $prod2 = Product::create([
            'category_id' => $customPod->id,
            'name' => 'Custom Print Blank Canvas Drop-Shoulder Tee',
            'name_bn' => 'কাস্টম প্রিন্ট ব্ল্যাঙ্ক ক্যানভাস ড্রপ-শোল্ডার টি-শার্ট',
            'slug' => 'custom-print-canvas-tee',
            'sku_prefix' => 'NZ-POD-01',
            'short_description' => 'Upload your design, brand logo or artwork. High-definition DTF printing.',
            'description' => 'Designed exclusively for custom creators and corporate orders across Bangladesh. Silky-smooth treated surface ensures ultra-vibrant Direct-to-Film (DTF) color reproduction with zero cracking or peeling.',
            'material' => '100% Bio-Washed Combed Cotton (210 GSM)',
            'fit_type' => 'drop_shoulder',
            'is_customizable' => true,
            'is_featured' => true,
            'is_active' => true,
            'images' => [
                'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=1000&q=85',
                'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=1000&q=85',
            ],
        ]);

        foreach (['M', 'L', 'XL'] as $size) {
            ProductVariant::create([
                'product_id' => $prod2->id,
                'sku' => "NZ-POD-01-BLK-{$size}",
                'size' => $size,
                'color_name' => 'Jet Black',
                'color_hex' => '#0A0A0A',
                'price_amount' => 145000, // ৳ 1,450
                'compare_at_amount' => 175000,
                'currency' => 'BDT',
                'weight_grams' => 240,
                'stock_on_hand' => 100,
                'stock_reserved' => 0,
                'is_active' => true,
            ]);
        }
    }
}
