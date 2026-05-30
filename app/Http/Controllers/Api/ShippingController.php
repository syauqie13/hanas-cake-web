<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\Store;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ShippingController
 *
 * Endpoint untuk menghitung estimasi ongkir berdasarkan jarak
 * antara alamat pelanggan dan toko yang dipilih.
 *
 * Digunakan oleh Flutter untuk menampilkan preview ongkir
 * di halaman checkout SEBELUM tombol "Bayar" ditekan.
 *
 * Rumus ongkir (sama dengan versi web):
 * - Pickup         → Rp 0
 * - ≤ 1 km         → Rp 2.000
 * - 1 – 10 km      → ceil(jarak) × Rp 2.000
 * - > 10 km        → Ditolak (di luar jangkauan)
 */
class ShippingController extends Controller
{
    use ApiResponseTrait;

    /**
     * POST /api/shipping/calculate
     *
     * Menghitung estimasi ongkir berdasarkan store_id & address_id.
     *
     * Request Body:
     * {
     *   "store_id": 1,
     *   "address_id": 2
     * }
     *
     * Response:
     * {
     *   "distance": 3.25,
     *   "shipping_cost": 8000,
     *   "is_out_of_bounds": false,
     *   "store_name": "Hana's Cake Pusat",
     *   "address_title": "Rumah"
     * }
     */
    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'store_id'   => 'required|exists:stores,id',
            'address_id' => 'required|exists:customer_addresses,id',
        ], [
            'store_id.required'   => 'Toko wajib dipilih.',
            'store_id.exists'     => 'Toko tidak ditemukan.',
            'address_id.required' => 'Alamat wajib dipilih.',
            'address_id.exists'   => 'Alamat tidak ditemukan.',
        ]);

        $store = Store::findOrFail($request->store_id);

        // Pastikan alamat milik user yang login
        $user = $request->user();
        $address = CustomerAddress::where('id', $request->address_id)
            ->whereHas('customer', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if (!$address) {
            return $this->notFoundResponse('Alamat tidak ditemukan atau bukan milik Anda.');
        }

        // Cek apakah koordinat tersedia
        if (!$store->latitude || !$store->longitude || !$address->latitude || !$address->longitude) {
            return $this->successResponse([
                'distance'         => null,
                'shipping_cost'    => 0,
                'is_out_of_bounds' => false,
                'message'          => 'Koordinat tidak tersedia, ongkir tidak bisa dihitung.',
                'store_name'       => $store->name,
                'address_title'    => $address->title,
            ], 'Estimasi ongkir (koordinat tidak tersedia)');
        }

        // Hitung jarak menggunakan rumus Haversine
        $distance = $this->calculateDistance(
            $address->latitude,
            $address->longitude,
            $store->latitude,
            $store->longitude
        );

        // Tentukan ongkir berdasarkan jarak
        $isOutOfBounds = $distance > 10;
        $shippingCost = 0;

        if ($isOutOfBounds) {
            $shippingCost = 0;
        } elseif ($distance <= 1) {
            $shippingCost = 2000;
        } else {
            $shippingCost = (int) (ceil($distance) * 2000);
        }

        return $this->successResponse([
            'distance'         => $distance,
            'shipping_cost'    => $shippingCost,
            'is_out_of_bounds' => $isOutOfBounds,
            'max_distance'     => 10,
            'store_name'       => $store->name,
            'address_title'    => $address->title,
        ], $isOutOfBounds
            ? "Lokasi di luar jangkauan (jarak: {$distance} km, maks: 10 km)"
            : "Estimasi ongkir berhasil dihitung"
        );
    }

    /**
     * Hitung jarak antara 2 titik koordinat (Haversine formula).
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad((float)$lat2 - (float)$lat1);
        $dLon = deg2rad((float)$lon2 - (float)$lon1);
        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2))
           * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 2);
    }
}
