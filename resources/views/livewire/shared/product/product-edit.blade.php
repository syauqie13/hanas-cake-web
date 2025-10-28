<div>
    <!-- resources/views/livewire/shared/product/product-edit.blade.php -->
    <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="border-0 shadow-lg modal-content rounded-3">
                <form wire:submit.prevent="update">
                    <!-- Header -->
                    <div class="pb-0 border-0 modal-header">
                        <h5 class="modal-title fw-bold text-dark" id="editModalLabel">
                            <i class="mr-2 fas fa-box-open text-primary"></i> Edit Produk
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            wire:click="$dispatch('hideEditModal')">
                            <span aria-hidden="true" class="fs-4">&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <!-- Nama Produk -->
                        <div class="mb-3 form-group">
                            <label class="fw-semibold">Nama Produk</label>
                            <input type="text" class="rounded-lg shadow-sm form-control"
                                placeholder="Masukkan nama produk" wire:model.defer="name">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Kategori -->
                        <div class="mb-3 form-group">
                            <label class="fw-semibold">Kategori</label>
                            <select class="rounded-lg shadow-sm form-control" wire:model.defer="categoryId">
                                <option value="">-- Pilih Kategori --</option>
                                @if(!empty($categories))
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('categoryId') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Harga -->
                        <div class="mb-3 form-group">
                            <label class="fw-semibold">Harga</label>
                            <input type="number" class="rounded-lg shadow-sm form-control" placeholder="Masukkan harga"
                                wire:model.defer="price" min="0" step="0.01">
                            @error('price') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Stok -->
                        <div class="mb-3 form-group">
                            <label class="fw-semibold">Stok</label>
                            <input type="number" class="rounded-lg shadow-sm form-control" placeholder="Masukkan stok"
                                wire:model.defer="stock" min="0" step="1">
                            @error('stock') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Diskon (%) -->
                        <div class="mb-3 form-group">
                            <label class="fw-semibold">Diskon (%)</label>
                            <input type="number" class="rounded-lg shadow-sm form-control"
                                placeholder="Masukkan diskon (0-100)" wire:model.defer="discount" min="0" max="100"
                                step="0.01">
                            @error('discount') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="pt-0 border-0 modal-footer">
                        <button type="button" class="border btn btn-light" data-dismiss="modal"
                            wire:click="$dispatch('hideEditModal')">
                            <i class="mr-1 fas fa-times"></i> Batal
                        </button>
                        <button type="submit" class="shadow-sm btn btn-primary">
                            <i class="mr-1 fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            window.addEventListener('showEditModal', () => {
                $('#editModal').modal('show');
            });

            window.addEventListener('hideEditModal', () => {
                $('#editModal').modal('hide');
            });

            // Tambahan: supaya backdrop hilang total setelah Livewire refresh
            document.addEventListener("livewire:navigated", () => {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            });
        </script>

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
    @endpush


</div>
