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
                    <h1>Data Karyawan</h1>
                </div>

                <!-- Tombol Tambah Karyawan -->
                <button wire:click="create" class="shadow-sm btn btn-primary d-flex align-items-center">
                    <i class="mr-2 fas fa-user-plus"></i> Tambah Karyawan
                </button>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="p-0 card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-striped table-md">
                                        <thead class="text-white ">
                                            <tr>
                                                <th scope="col" class="text-center align-middle" style="width: 60px;">No
                                                </th>
                                                <th scope="col" class="align-middle">Nama</th>
                                                <th scope="col" class="text-center align-middle" style="width: 120px;">
                                                    Role</th>
                                                <th scope="col" class="text-center align-middle" style="width: 80px;">
                                                    Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse ($karyawan as $user)
                                                <tr class="hover-highlight">
                                                    <!-- No -->
                                                    <td class="text-center align-middle text-muted font-weight-bold">
                                                        {{ $loop->iteration }}
                                                    </td>

                                                    <!-- Nama + Email + Avatar -->
                                                    <td class="align-middle">
                                                        <div class="d-flex align-items-center">
                                                            <div class="mr-3 avatar">
                                                                <img alt="image"
                                                                    src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff"
                                                                    class="shadow-sm rounded-circle" width="42" height="42">
                                                            </div>
                                                            <div>
                                                                <div class="mb-0 font-weight-600 text-dark">
                                                                    {{ $user->name }}
                                                                </div>
                                                                <div class="text-xs text-muted">
                                                                    <i
                                                                        class="mr-1 fas fa-envelope text-primary"></i>{{ $user->email }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <!-- Role -->
                                                    <td class="text-center align-middle">
                                                        <span
                                                            class="px-3 py-2 shadow-sm badge badge-pill badge-success text-uppercase"
                                                            style="font-size: 11px; letter-spacing: 0.5px;">
                                                            {{ $user->role }}
                                                        </span>
                                                    </td>

                                                    <!-- Aksi -->
                                                    <td class="text-center align-middle">
                                                        <div class="dropdown">
                                                            <button class="border btn btn-sm btn-light dropdown-toggle"
                                                                type="button" id="dropdownMenuButton{{ $user->id }}"
                                                                data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false" style="border-radius: 10px;">
                                                                <i class="fas fa-ellipsis-v text-secondary"></i>
                                                            </button>
                                                            <div class="shadow-sm dropdown-menu dropdown-menu-right animated--fade-in"
                                                                aria-labelledby="dropdownMenuButton{{ $user->id }}">
                                                                <button type="button" class="cursor-pointer dropdown-item d-flex align-items-center"
                                                                    wire:click="edit({{ $user->id }})">
                                                                    <i class="mr-2 fas fa-edit text-warning"></i> Edit
                                                                </button>

                                                                <button type="button" class="cursor-pointer dropdown-item d-flex align-items-center text-danger"
                                                                    wire:click="deleteConfirm({{ $user->id }})">
                                                                    <i class="mr-2 fas fa-trash-alt"></i> Hapus
                                                                </button>

                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="py-5 text-center text-muted">
                                                        <i class="mb-3 fas fa-user-slash fa-2x"></i>
                                                        <div class="mb-0 h6">Belum ada data karyawan</div>
                                                        <small>Tambahkan karyawan baru untuk mulai mengelola data.</small>
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

    <!-- Modal Create Karyawan -->
    <livewire:admin.karyawan.karyawan-create />
    <livewire:admin.karyawan.karyawan-edit />

    @push('js')
        <!-- JS Libraries -->
        <script src="{{ asset('assets/modules/jquery-ui/jquery-ui.min.js') }}"></script>

        <!-- Page Specific JS File -->
        <script src="{{ asset('assets/js/page/components-table.js') }}"></script>

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

        <script>
            document.addEventListener('livewire:init', () => {

                // ✅ SweetAlert konfirmasi hapus
                Livewire.on('confirmDelete', (data) => {
                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: "Data karyawan akan dihapus permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // emit event ke Livewire agar proses hapus dijalankan
                            Livewire.dispatch('deleteConfirmed', { id: data.id });
                        }
                    });
                });
            });
        </script>
    @endpush
</div>
