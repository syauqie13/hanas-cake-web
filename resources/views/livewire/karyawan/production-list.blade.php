@push('styles')
    <style>
        /* (Style Anda dari file lain) */
        thead tr th { border: none !important; letter-spacing: 0.3px; }
        tbody tr:hover { background-color: #f7f9fc !important; transition: 0.2s ease; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: #fcfcfd; }
    </style>
@endpush

<div wire:poll.5s>
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex justify-content-between align-items-center">
                <div>
                    <h1>Daftar Produksi (PO)</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item">Daftar ini menjumlahkan semua produk dari pesanan PO yang berstatus "Sedang Diproses".</div>
                    </div>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4 class="card-title"><i class="mr-2 fas fa-fire-alt text-danger animate-pulse"></i> Total Produk (Perlu Dibuat)</h4>
                            </div>
                            <div class="p-0 card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-striped table-md">
                                        <thead class="text-white">
                                            <tr>
                                                <th scope="col" class="align-middle text-dark">Nama Produk</th>
                                                <th scope="col" class="text-center align-middle text-dark">Total Kuantitas</th>
                                                <th scope="col" class="text-center align-middle text-dark">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($processingList as $item)
                                                <tr class="hover-highlight">
                                                    <td class="align-middle font-weight-600 text-dark">
                                                        {{ $item->product_name }}
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span class="badge badge-primary" style="font-size: 1rem;">
                                                            {{ $item->total_quantity_needed }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <a href="{{ route('karyawan.list-product') }}" wire:navigate class="btn btn-sm btn-outline-secondary">
                                                            Lihat Produk
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="py-5 text-center text-muted">
                                                        <i class="mb-3 fas fa-check-circle fa-2x text-success"></i>
                                                        <div class="mb-0 h6">Tidak ada produk yang perlu dibuat saat ini.</div>
                                                        <small>Semua pesanan lunas sudah "Selesai" atau "Dibatalkan".</small>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Instruksi</h4>
                            </div>
                            <div class="card-body">
                                <p>Halaman ini berfungsi sebagai **Daftar Kerja Dapur**.</p>
                                <p>Angka di tabel adalah total jumlah produk yang perlu Anda buat dari semua pesanan PO yang statusnya **"Sedang Diproses"**.</p>
                                <hr>
                                <p class="mb-0">Setelah produk selesai dibuat, Anda bisa langsung menandai selesai pada masing-masing card pesanan di bawah, atau mengubah statusnya menjadi **"Siap diambil / dikirim"** di halaman <a href="{{ route('karyawan.orders.list') }}">Manajemen Pesanan</a>.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Detail Per Pesanan Aktif -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-info border-top-0 shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                                <h4 class="card-title text-dark mb-0 font-weight-bold" style="font-size: 1.05rem;">
                                    <i class="mr-2 fas fa-receipt text-primary"></i> Detail Per Pesanan Aktif
                                </h4>
                                <span class="badge badge-info shadow-sm font-weight-bold">{{ count($activeOrders) }} Pesanan</span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @forelse($activeOrders as $order)
                                        @php
                                            $latitude = null;
                                            $longitude = null;
                                            $matchedAddress = null;
                                            if ($order->delivery_type === 'delivery' || ($order->delivery_type !== 'pickup' && $order->shipping_zone_name !== 'Ambil di Toko (Pickup)')) {
                                                $shippingAddressText = $order->shipping_address;
                                                $addresses = optional(optional($order->user)->customer)->addresses ?? collect();
                                                
                                                $matchedAddress = $addresses->first(function($addr) use ($shippingAddressText) {
                                                    return $shippingAddressText && (
                                                        str_contains(strtolower($shippingAddressText), strtolower($addr->detail_address)) || 
                                                        str_contains(strtolower($addr->detail_address), strtolower($shippingAddressText))
                                                    );
                                                });
                                                
                                                if (!$matchedAddress) {
                                                    $matchedAddress = $addresses->firstWhere('is_primary', true) ?? $addresses->first();
                                                }
                                                
                                                if ($matchedAddress) {
                                                    $latitude = $matchedAddress->latitude;
                                                    $longitude = $matchedAddress->longitude;
                                                }
                                            }
                                        @endphp
                                        <div class="col-12 col-md-6 col-lg-4 mb-4" wire:key="production-order-{{ $order->id }}">
                                            <div class="card h-100 shadow-sm border border-light rounded" style="transition: transform 0.2s ease;">
                                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                                    <div>
                                                        <!-- Card Header -->
                                                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                                            <span class="font-weight-bold text-primary" style="font-size: 0.95rem;">
                                                                #{{ $order->merchant_order_id }}
                                                            </span>
                                                            <small class="text-muted font-weight-bold">
                                                                {{ $order->tanggal->diffForHumans() }}
                                                            </small>
                                                        </div>

                                                        <!-- Customer Info -->
                                                        <div class="mb-3">
                                                            <div class="small text-muted font-weight-600" style="font-size: 0.75rem;">PELANGGAN:</div>
                                                            <div class="font-weight-bold text-dark" style="font-size: 0.92rem;">{{ $order->shipping_name ?? $order->user->name ?? 'N/A' }}</div>
                                                            <div class="small text-muted"><i class="fas fa-phone mr-1"></i> {{ $order->shipping_phone ?? $order->user->phone ?? '-' }}</div>
                                                        </div>

                                                        <!-- Shipping Method Badge -->
                                                        <div class="mb-3">
                                                            <div class="small text-muted font-weight-600 mb-1" style="font-size: 0.75rem;">METODE:</div>
                                                            @if($order->delivery_type == 'pickup' || $order->shipping_zone_name == 'Ambil di Toko (Pickup)')
                                                                <span class="badge badge-success py-1 px-2"><i class="fas fa-walking mr-1"></i> Ambil Sendiri (Pickup)</span>
                                                            @else
                                                                <span class="badge badge-info py-1 px-2"><i class="fas fa-truck mr-1"></i> Antar Kurir (Delivery)</span>
                                                                @if($order->shipping_zone_name)
                                                                    <div class="mt-1 small text-muted font-weight-bold">
                                                                        {{ $order->shipping_zone_name }}
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>

                                                        <!-- Ordered Items -->
                                                        <div class="mb-3 p-2 bg-light rounded" style="border: 1px dashed #e4e6ef;">
                                                            <div class="small text-muted font-weight-600 mb-1" style="font-size: 0.75rem;">DAFTAR PESANAN:</div>
                                                            <ul class="list-unstyled mb-0">
                                                                @forelse($order->items as $item)
                                                                    <li class="d-flex justify-content-between align-items-center py-1 border-bottom border-white">
                                                                        <span class="font-weight-600 small text-dark">{{ $item->product->name ?? 'Produk Dihapus' }}</span>
                                                                        <span class="badge badge-primary px-2 py-0.5" style="border-radius: 20px;">{{ $item->jumlah }}x</span>
                                                                    </li>
                                                                @empty
                                                                    <li class="text-muted small">Tidak ada item</li>
                                                                @endforelse
                                                            </ul>
                                                        </div>

                                                        <!-- Shipping Address & Maps -->
                                                        @if($order->delivery_type == 'delivery' || ($order->delivery_type != 'pickup' && $order->shipping_zone_name != 'Ambil di Toko (Pickup)'))
                                                            <div class="mb-3">
                                                                <div class="small text-muted font-weight-600" style="font-size: 0.75rem;">ALAMAT KIRIM:</div>
                                                                <p class="small text-dark mb-2" style="line-height: 1.4;">{{ $order->shipping_address ?? '-' }}</p>
                                                                
                                                                @if($latitude && $longitude)
                                                                    <div class="mt-2 d-flex flex-wrap align-items-center">
                                                                        <span class="badge badge-light border text-muted mr-2 mb-2 py-1 px-2" style="font-size: 0.72rem;">
                                                                            <i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $latitude }}, {{ $longitude }}
                                                                        </span>
                                                                        <a href="https://maps.google.com/?q={{ $latitude }},{{ $longitude }}" target="_blank" class="btn btn-xs btn-primary mb-2 shadow-sm font-weight-bold px-2 py-1" style="font-size: 0.75rem; border-radius: 4px;">
                                                                            <i class="fas fa-directions mr-1"></i> Buka Peta
                                                                        </a>
                                                                    </div>
                                                                @else
                                                                    <div class="small text-warning" style="font-size: 0.75rem;">
                                                                        <i class="fas fa-exclamation-triangle mr-1"></i> Koordinat lokasi tidak ditemukan.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Actions (Status Update) -->
                                                    <div class="mt-3 border-top pt-3">
                                                        <button type="button" 
                                                                wire:click="setStatus({{ $order->id }}, 'dikirim')" 
                                                                class="btn btn-block btn-success btn-sm font-weight-bold py-2 shadow-sm"
                                                                style="border-radius: 5px;">
                                                                <i class="fas fa-check-circle mr-1"></i> Siap Diambil / Dikirim
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 py-5 text-center text-muted">
                                            <i class="mb-3 fas fa-cookie-bite fa-3x text-muted" style="opacity: 0.4;"></i>
                                            <div class="mb-0 h5">Tidak ada detail pesanan aktif saat ini.</div>
                                            <small>Pesanan baru yang lunas dan perlu diproses akan muncul di sini secara otomatis.</small>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
