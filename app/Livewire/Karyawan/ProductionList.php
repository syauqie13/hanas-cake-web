<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')] // Menggunakan layout Stisla/Admin Anda
class ProductionList extends Component
{
    // Properti untuk menyimpan hasil
    public $processingList;
    public $activeOrders;

    public function mount()
    {
        $this->loadProductionList();
    }

    /**
     * Query ini adalah inti dari fitur Anda.
     * Ini menjumlahkan semua item dari order PO yang LUNAS dan PERLU DIBUAT.
     */
    public function loadProductionList()
    {
        // 1. Ringkasan Agregat Total Produk
        $this->processingList = OrderItem::query()
            // Gabungkan dengan tabel 'orders'
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            // Gabungkan dengan tabel 'products' untuk dapat nama
            ->join('products', 'order_items.product_id', '=', 'products.id')

            // Filter HANYA untuk:
            ->where('orders.order_type', 'online')     // (a) Order E-commerce (PO)
            ->where('orders.payment_status', 'paid')   // (b) Yang Lunas
            ->whereIn('orders.status', ['processing', 'diproses']) // (c) Yang SEDANG DIPROSES

            // Pilih kolom yang kita butuhkan
            ->select(
                'products.name as product_name',
                'products.id as product_id',
                // Jumlahkan total kuantitasnya
                DB::raw('SUM(order_items.jumlah) as total_quantity_needed')
            )

            // Kelompokkan berdasarkan produk
            ->groupBy('products.name', 'products.id')

            // Urutkan dari yang paling banyak dipesan
            ->orderBy('total_quantity_needed', 'desc')
            ->get();

        // 2. Daftar Detail Per-Pesanan Aktif
        $this->activeOrders = Order::query()
            ->where('order_type', 'online')
            ->where('payment_status', 'paid')
            ->whereIn('status', ['processing', 'diproses'])
            ->with(['items.product', 'user.customer.addresses'])
            ->latest('tanggal')
            ->get();
    }

    /**
     * Update status pesanan langsung dari halaman produksi
     */
    public function setStatus($orderId, $status)
    {
        $validStatuses = ['diproses', 'dikirim', 'selesai', 'dibatalkan'];
        if (!in_array($status, $validStatuses)) {
            $this->dispatch('notify', [
                'message' => 'Status tidak valid.',
                'icon' => 'error'
            ]);
            return;
        }

        try {
            $order = Order::with('user')->findOrFail($orderId);
            $order->status = $status;
            $order->save();

            // Kirim notifikasi real-time ke user
            if ($order->user) {
                try {
                    $order->user->notify(new \App\Notifications\OrderStatusNotification($order, $status));
                } catch (\Exception $ne) {
                    \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi status order: ' . $ne->getMessage());
                }
            }

            $this->loadProductionList();

            $this->dispatch('notify', [
                'message' => 'Status pesanan berhasil diperbarui.',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Gagal memperbarui status: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.karyawan.production-list');
    }
}
