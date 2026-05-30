<?php

namespace App\Livewire\Karyawan\Order; // Sesuai path Anda

use App\Models\Order;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Exports\OrdersExport;
use Livewire\Attributes\Layout;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB; // Pastikan DB di-import

#[Layout('components.layouts.app')]
class OrderManagement extends Component
{
    use WithPagination;

    #[Url(keep: true, as: 'search')]
    public $search = '';
    public $filterStatus = '';
    #[Url(keep: true)]
    public $filterTanggal = 'today';

    public $detailModalOpen = false;
    public $selectedOrder;

    public $statusOptions = [
        'diproses' => 'Sedang Diproses',
        'dikirim' => 'Siap Diambil/Dikirim',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function setStatus($orderId, $status)
    {
        if (!array_key_exists($status, $this->statusOptions)) {
            $this->dispatch('notify', [
                'message' => 'Status tidak valid.',
                'icon' => 'error'
            ]);
            return;
        }

        try {
            $order = Order::with('user')->findOrFail($orderId);
            if ($order->status == $status) {
                return; // Tidak ada perubahan
            }
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

    public $latitude = null;
    public $longitude = null;

    public function showDetailModal($orderId)
    {
        // Muat order beserta relasi item, produk, dan user
        $this->selectedOrder = Order::with('items.product', 'user.customer.addresses')->findOrFail($orderId);
        $this->detailModalOpen = true;

        $this->latitude = null;
        $this->longitude = null;
        if ($this->selectedOrder->delivery_type === 'delivery' || ($this->selectedOrder->delivery_type !== 'pickup' && $this->selectedOrder->shipping_zone_name !== 'Ambil di Toko (Pickup)')) {
            $shippingAddressText = $this->selectedOrder->shipping_address;
            $addresses = optional(optional($this->selectedOrder->user)->customer)->addresses ?? collect();
            
            // Cari kecocokan alamat
            $matchedAddress = $addresses->first(function($addr) use ($shippingAddressText) {
                return $shippingAddressText && (
                    str_contains(strtolower($shippingAddressText), strtolower($addr->detail_address)) || 
                    str_contains(strtolower($addr->detail_address), strtolower($shippingAddressText))
                );
            });
            
            // Fallback ke alamat primary atau alamat pertama
            if (!$matchedAddress) {
                $matchedAddress = $addresses->firstWhere('is_primary', true) ?? $addresses->first();
            }
            
            if ($matchedAddress) {
                $this->latitude = $matchedAddress->latitude;
                $this->longitude = $matchedAddress->longitude;
            }
        }
    }

    public function closeDetailModal()
    {
        $this->detailModalOpen = false;
        $this->selectedOrder = null;
        $this->latitude = null;
        $this->longitude = null;
    }

    public function export()
    {
        // Tentukan nama file
        $filename = 'laporan_pesanan_po_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        // Panggil Export Class, kirim filter saat ini
        return Excel::download(new OrdersExport($this->search, $this->filterStatus), $filename);
    }

    public function resetTanggal()
    {
        $this->filterTanggal = 'today';
        $this->resetPage();
    }

    // --- HANYA SATU FUNGSI RENDER() YANG MENGGABUNGKAN SEMUANYA ---
    public function render()
    {
        // 1. Logika Statistik (dari kode Anda)
        $statsQuery = Order::where('order_type', 'online');

        switch ($this->filterTanggal) {
            case 'today':
                $statsQuery->whereDate('tanggal', Carbon::today());
                break;
            case 'week':
                $statsQuery->whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $statsQuery->whereMonth('tanggal', Carbon::now()->month)->whereYear('tanggal', Carbon::now()->year);
                break;
        }

        // --- INI LOGIKA YANG DITAMBAHKAN ---
        $totalPendapatan = (clone $statsQuery)
            ->where('payment_status', 'paid') // Sesuai DB Anda ('paid')
            ->sum('total');
        // --- AKHIR TAMBAHAN ---

        $stats = [
            'total' => (clone $statsQuery)
                ->where(function ($q) {
                    $q->where('payment_status', '!=', 'pending')
                        ->orWhere(function ($pendingQ) {
                            $pendingQ->where('payment_status', 'pending')
                                ->where('created_at', '>', now()->subHour());
                        });
                })
                ->count(),

            'pending' => (clone $statsQuery)
                ->where('payment_status', 'pending')
                ->where('created_at', '>', now()->subHour()) // Sembunyikan pending > 1 jam
                ->count(),
            'processing' => (clone $statsQuery)->where('status', 'diproses')->count(),
            'shipped' => (clone $statsQuery)->where('status', 'dikirim')->count(),
            'completed' => (clone $statsQuery)->where('status', 'selesai')->count(),
            'cancelled' => (clone $statsQuery)->whereIn('status', ['dibatalkan'])->count(),
        ];

        // 2. Logika Query Tabel (dari kode Anda)
        $query = Order::query()
            ->where('order_type', 'online')
            ->with('user');

        switch ($this->filterTanggal) {
            case 'today':
                $query->whereDate('tanggal', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('tanggal', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereMonth('tanggal', Carbon::now()->month)
                    ->whereYear('tanggal', Carbon::now()->year);
                break;
        }

        // 3. Logika Search (dari kode Anda)
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('merchant_order_id', 'like', '%' . $this->search . '%')
                    ->orWhere('shipping_name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // 4. Logika Filter Status (dari kode Anda)
        $query->when($this->filterStatus, function ($q) {
            // Jika user MEMILIH 'pending'
            if ($this->filterStatus == 'pending') {
                $q->where('payment_status', 'pending')
                    ->where('created_at', '>', now()->subHour()); // Tampilkan pending < 1 jam

                // Jika user MEMILIH status lain
            } else {
                $q->where('status', $this->filterStatus);
            }

            // Logika DEFAULT (Jika user TIDAK mem-filter)
        }, function ($q) {
            // Tampilkan SEMUA order, KECUALI pending yang sudah lama
            $q->where(function ($subQ) {
                $subQ->where('payment_status', '!=', 'pending') // Tampilkan semua yg bukan pending
                    ->orWhere(function ($pendingQ) {
                        // Atau yg pending, TAPI masih baru (kurang dari 1 jam)
                        $pendingQ->where('payment_status', 'pending')
                            ->where('created_at', '>', now()->subHour());
                    });
            });
        });

        if ($this->filterStatus !== 'all') {
            $statsQuery->where('status', $this->filterStatus);
        }

        // 5. Eksekusi Query
        $orders = $query->latest('tanggal')->paginate(10);

        // 6. Return View
        return view('livewire.karyawan.order.order-management', [
            'orders' => $orders,
            'stats' => $stats,
            'totalPendapatan' => $totalPendapatan, // <-- Variabel baru dikirim ke Blade
        ]);
    }
}
