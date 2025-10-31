

<div>
    <!-- Main Content -->
    <div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <div>
                <h1>Point of Sale (Kasir)</h1>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="mb-4 col-md-7">
                    <div class="shadow-sm card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Daftar Produk</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 form-row">
                                <div class="mb-2 col-md-8 mb-md-0">
                                    <input wire:model.live.debounce.300ms="search" type="text"
                                        class="form-control" placeholder="Cari produk...">
                                </div>
                                <div class="col-md-4">
                                    <select wire:model.live="selectedCategory" class="form-control">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row" style="max-height: 70vh; overflow-y: auto;">
                                @forelse ($products as $product)
                                    <div class="mb-3 col-6 col-md-4 col-lg-3">
                                        <div wire:click="addToCart({{ $product->id }})"
                                            class="text-center border-0 shadow-sm card h-100 hover-shadow"
                                            style="cursor:pointer;">
                                            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/150' }}"
                                                class="card-img-top rounded-top"
                                                alt="{{ $product->name }}"
                                                style="height:100px; object-fit:cover;">
                                            <div class="p-2 card-body">
                                                <h6 class="mb-1 card-title text-truncate">{{ $product->name }}</h6>
                                                <small class="mb-1 text-muted d-block">Stok: {{ $product->stock }}</small>
                                                <div class="font-weight-bold text-primary">
                                                    Rp {{ number_format($product->price - ($product->price * $product->discount / 100), 0, ',', '.') }}
                                                </div>
                                                @if ($product->discount > 0)
                                                    <small class="text-danger"><del>Rp {{ number_format($product->price, 0, ',', '.') }}</del></small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-3 text-center col-12 text-muted">
                                        <i class="mb-2 fas fa-box-open fa-2x"></i>
                                        <div>Produk tidak ditemukan</div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4 col-md-5">
                    <div class="shadow-sm card sticky-top" style="top: 20px;">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Keranjang</h4>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label>Pelanggan</label>
                                @if($selectedCustomerId)
                                    <div class="input-group" wire:key="customer-selected-block">
                                        <input type="text" class="form-control" value="{{ $selectedCustomerName }}" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-danger" wire:click="clearCustomer" type="button">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="position-relative" wire:key="customer-search-block">
                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="Cari nama atau no. HP..."
                                            wire:model.live.debounce.300ms="customerSearch"
                                            wire:keydown.escape="clearCustomer"
                                        >

                                        <button
                                            type="button"
                                            class="btn btn-success btn-sm"
                                            style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%);"
                                            wire:click="create">
                                            <i class="fas fa-plus"></i>
                                        </button>

                                        @if(count($customerResults) > 0)
                                            <div class="bg-white shadow position-absolute w-100" style="z-index: 1000; max-height: 200px; overflow-y: auto; border: 1px solid #ddd;">
                                                <ul class="list-group list-group-flush">
                                                    @foreach($customerResults as $customer)
                                                        <li
                                                            class="list-group-item list-group-item-action"
                                                            wire:click="selectCustomer({{ $customer->id }})"
                                                            style="cursor: pointer;"
                                                            wire:key="customer-result-{{ $customer->id }}"
                                                        >
                                                            <strong>{{ $customer->name }}</strong> - {{ $customer->phone }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3" style="max-height: 40vh; overflow-y: auto;">
                                @forelse ($cart as $productId => $item)
                                    <div class="py-2 d-flex justify-content-between align-items-center border-bottom">
                                        <div>
                                            <strong>{{ $item['name'] }}</strong>
                                            <div class="text-muted small">
                                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <input type="number"
                                                wire:model.live="cart.{{ $productId }}.jumlah"
                                                wire:change="updateCartQuantity({{ $productId }}, $event.target.value)"
                                                class="mr-2 text-center form-control form-control-sm"
                                                style="width:70px;"
                                                min="1"
                                                max="{{ $item['stock'] }}">
                                            <button type="button" wire:click="removeFromCart({{ $productId }})"
                                                class="btn btn-sm btn-outline-danger">&times;</button>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-muted">Keranjang kosong</p>
                                @endforelse
                            </div>

                            <hr>

                            <div class="mb-2 d-flex justify-content-between">
                                <span class="font-weight-bold">Total</span>
                                <span class="h5 font-weight-bold text-primary">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </span>
                            </div>

                            <form wire:submit.prevent="processPayment">
                                <div class="form-group">
                                    <label>Metode Pembayaran</label>
                                    <select wire:model="payment_method" class="form-control">
                                        <option value="tunai">Tunai</option>
                                        <option value="qris">QRIS</option>
                                        <option value="transfer">Transfer Bank</option>
                                        <option value="debit">Kartu Debit</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Jumlah Bayar</label>
                                    <input wire:model.live.debounce.300ms="paid_amount" type="number"
                                        class="form-control" placeholder="0">
                                </div>

                                <div class="mb-3 d-flex justify-content-between">
                                    <span class="font-weight-bold">Kembalian</span>
                                    <span class="h5 text-success">
                                        Rp {{ number_format($change_amount, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="d-flex">
                                    <button type="submit"
                                        class="mr-2 btn btn-primary btn-block"
                                        {{ empty($cart) || $paid_amount < $total ? 'disabled' : '' }}>
                                        Proses Pembayaran
                                    </button>
                                    <button type="button" wire:click="clearCart" class="btn btn-danger">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <livewire:karyawan.pos.create-customer-modal />

    </div>

    <!-- Script tambahan (optional, jika ingin alert dll) -->
    @push('js')
        <script>
            window.addEventListener('notify', event => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: event.detail.message || 'Transaksi berhasil.',
                    timer: 2000,
                    showConfirmButton: false,
                });
            });
        </script>
    @endpush
</div>
