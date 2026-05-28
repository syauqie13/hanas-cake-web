<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ShippingZone;
use Illuminate\Support\Facades\Storage; // Pastikan import ini jika pakai Storage::url

class ProductController extends Controller
{
    /**
     * Ambil Semua Kategori
     */
    public function categories()
    {
        $categories = Category::all()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                // Jika kategori juga punya image, bungkus dengan url/asset seperti ini:
                'image' => $category->image ? url(Storage::url($category->image)) : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar Kategori',
            'data' => $categories
        ]);
    }

    /**
     * Ambil Daftar Produk (dengan fitur Filter & Search)
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(10);

        // Mengubah data di dalam paginator agar menyertakan URL penuh gambar
        $products->through(function ($product) {
            return [
                'id' => $product->id,
                'category_id' => $product->category_id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                // Mengubah path menjadi URL penuh (Contoh hasil: http://ip-laptop:8000/storage/products/xxx.jpg)
                'image' => $product->image ? url(Storage::url($product->image)) : null,
                'category' => $product->category,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar Produk',
            'data' => $products
        ]);
    }

    /**
     * Detail Produk Spesifik
     */
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // Format data sebelum dikirim ke JSON
        $data = [
            'id' => $product->id,
            'category_id' => $product->category_id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            // Memastikan URL gambar penuh
            'image' => $product->image ? url(Storage::url($product->image)) : null,
            'category' => $product->category,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail Produk',
            'data' => $data
        ]);
    }
}