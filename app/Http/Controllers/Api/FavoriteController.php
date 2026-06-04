<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * FavoriteController
 *
 * Mengelola daftar produk favorit pelanggan via API (Flutter).
 *
 * Fitur:
 * - Lihat semua produk favorit milik user yang login
 * - Toggle favorit (tambah jika belum ada, hapus jika sudah ada)
 * - Cek apakah produk tertentu sudah difavoritkan
 */
class FavoriteController extends Controller
{
    use ApiResponseTrait;

    /**
     * GET /api/favorites
     *
     * Mengambil daftar semua produk favorit user yang sedang login.
     * Response produk sudah menyertakan field `is_favorited: true`.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $favorites = $user->favoriteProducts()
            ->with('category')
            ->latest('favorites.created_at')
            ->get()
            ->map(function ($product) {
                $productData = $product->toArray();
                $productData['is_favorited'] = true;
                return $productData;
            });

        return $this->successResponse($favorites, 'Daftar Produk Favorit');
    }

    /**
     * POST /api/favorites/toggle/{product_id}
     *
     * Toggle favorit: tambah jika belum, hapus jika sudah.
     * Mengembalikan status terbaru (is_favorited) dan jumlah favorit produk.
     */
    public function toggle(Request $request, $productId): JsonResponse
    {
        $user = $request->user();

        $product = Product::find($productId);

        if (!$product) {
            return $this->notFoundResponse('Produk tidak ditemukan');
        }

        $existing = Favorite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            // Sudah difavoritkan → hapus (unfavorite)
            $existing->delete();
            $isFavorited = false;
            $message = 'Produk dihapus dari favorit';
        } else {
            // Belum difavoritkan → tambah
            Favorite::create([
                'user_id'    => $user->id,
                'product_id' => $productId,
            ]);
            $isFavorited = true;
            $message = 'Produk ditambahkan ke favorit';
        }

        return $this->successResponse([
            'product_id'    => (int) $productId,
            'is_favorited'  => $isFavorited,
            'favorite_count' => Favorite::where('product_id', $productId)->count(),
        ], $message);
    }

    /**
     * GET /api/favorites/check/{product_id}
     *
     * Cek apakah produk tertentu sudah difavoritkan oleh user yang login.
     * Berguna untuk mengisi state ikon hati saat membuka halaman detail produk.
     */
    public function check(Request $request, $productId): JsonResponse
    {
        $user = $request->user();

        $isFavorited = Favorite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        return $this->successResponse([
            'product_id'   => (int) $productId,
            'is_favorited' => $isFavorited,
        ], 'Status Favorit');
    }

    /**
     * DELETE /api/favorites/{product_id}
     *
     * Hapus produk dari favorit secara eksplisit (alternatif toggle).
     */
    public function destroy(Request $request, $productId): JsonResponse
    {
        $user = $request->user();

        $deleted = Favorite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();

        if (!$deleted) {
            return $this->notFoundResponse('Produk tidak ada di daftar favorit');
        }

        return $this->successResponse(null, 'Produk dihapus dari favorit');
    }
}
