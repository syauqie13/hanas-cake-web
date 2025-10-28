@push('styles')
    <style>
        .avatar img {
            object-fit: cover;
        }

        .dropdown-menu {
            min-width: 130px;
            font-size: 0.9rem;
        }
    </style>

    <style>
        thead tr th {
            border: none !important;
            letter-spacing: 0.3px;
        }

        tbody tr:hover {
            background-color: #f7f9fc !important;
            transition: 0.2s ease;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #fcfcfd;
        }
    </style>
@endpush

<div>
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex justify-content-between align-items-center">
                <div>
                    <h1>Data Produk</h1>
                </div>

                <!-- Tombol Tambah Produk -->
                <button wire:click="create" class="shadow-sm btn btn-primary d-flex align-items-center">
                    <i class="mr-2 fas fa-box"></i> Tambah Produk
                </button>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="p-0 card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-striped table-md">
                                        <thead class="text-white">
                                            <tr>
                                                <th scope="col" class="text-center align-middle" style="width: 60px;">No
                                                </th>
                                                <th scope="col" class="align-middle">Nama Produk</th>
                                                <th scope="col" class="text-center align-middle">Kategori</th>
                                                <th scope="col" class="text-center align-middle">Harga</th>
                                                <th scope="col" class="text-center align-middle">Stok</th>
                                                <th scope="col" class="text-center align-middle">Diskon</th>
                                                <th scope="col" class="text-center align-middle" style="width: 100px;">
                                                    Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($products as $product)
                                                <tr class="hover-highlight">
                                                    <td class="text-center align-middle text-muted font-weight-bold">
                                                        {{ $loop->iteration }}
                                                    </td>

                                                    <td class="align-middle font-weight-600 text-dark">
                                                        {{ $product->name }}
                                                    </td>

                                                    <td class="text-center align-middle">
                                                        <span class="px-3 py-2 shadow-sm badge badge-info">
                                                            {{ $product->category->name ?? '-' }}
                                                        </span>
                                                    </td>

                                                    <td class="text-center align-middle">
                                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                                    </td>

                                                    <td class="text-center align-middle">
                                                        {{ $product->stock }}
                                                    </td>

                                                    <td class="text-center align-middle">
                                                        {{ $product->discount }}%
                                                    </td>

                                                    <td class="text-center align-middle">
                                                        <div class="dropdown">
                                                            <button class="border btn btn-sm btn-light dropdown-toggle"
                                                                type="button" id="dropdownMenuButton{{ $product->id }}"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false" style="border-radius: 10px;">
                                                                <i class="fas fa-ellipsis-v text-secondary"></i>
                                                            </button>
                                                            <div class="shadow-sm dropdown-menu dropdown-menu-right animated--fade-in"
                                                                aria-labelledby="dropdownMenuButton{{ $product->id }}">
                                                                <button type="button"
                                                                    class="cursor-pointer dropdown-item d-flex align-items-center"
                                                                    wire:click="edit({{ $product->id }})">
                                                                    <i class="mr-2 fas fa-edit text-warning"></i> Edit
                                                                </button>

                                                                <button type="button"
                                                                    class="cursor-pointer dropdown-item d-flex align-items-center text-danger"
                                                                    wire:click="deleteConfirm({{ $product->id }})">
                                                                    <i class="mr-2 fas fa-trash-alt"></i> Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="py-5 text-center text-muted">
                                                        <i class="mb-3 fas fa-box-open fa-2x"></i>
                                                        <div class="mb-0 h6">Belum ada data produk</div>
                                                        <small>Tambahkan produk baru untuk mulai mengelola data.</small>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-right card-footer">
                                <nav class="d-inline-block">
                                    <ul class="mb-0 pagination">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                        <li class="page-item active">
                                            <a class="page-link" href="#">1 <span class="sr-only">(current)</span></a>
                                        </li>
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="#">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Create & Edit Produk -->
    <livewire:shared.product.product-create />
    <livewire:shared.product.product-edit />

    @push('js')
        <!-- JS Libraries -->
        <script src="{{ asset('assets/modules/jquery-ui/jquery-ui.min.js') }}"></script>

        <!-- Page Specific JS File -->
        <script src="{{ asset('assets/js/page/components-table.js') }}"></script>

        <!-- Notifikasi sukses -->
        <script>
            window.addEventListener('notify', event => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: event.detail.message || 'Aksi berhasil dijalankan!',
                    timer: 2000,
                    showConfirmButton: false,
                });
            });
        </script>

        <!-- Konfirmasi hapus -->
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('confirmDelete', (data) => {
                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: "Data produk akan dihapus permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.dispatch('deleteConfirmed', { id: data.id });
                        }
                    });
                });
            });
        </script>
    @endpush
</div>
