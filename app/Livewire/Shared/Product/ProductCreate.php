<?php

namespace App\Livewire\Shared\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;

class ProductCreate extends Component
{
    public $category_id, $name, $price, $stock, $discount;
    public $showModal = false;

    protected $listeners = [
        'openCreateModal' => 'showCreateModal',
    ];

    /**
     * Tampilkan modal dan reset form
     */
    public function showCreateModal()
    {
        $this->reset(['category_id', 'name', 'price', 'stock', 'discount']);
        $this->showModal = true;

        $this->dispatch('show-create-modal');
    }

    /**
     * Simpan produk baru
     */
    public function save()
    {
        $validated = $this->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|min:3|max:255|unique:products,name',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        Product::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'discount' => $validated['discount'] ?? 0,
        ]);

        // Tutup modal
        $this->dispatch('hide-create-modal');

        // Refresh daftar produk
        $this->dispatch('productCreated');

        // Notifikasi sukses
        $this->dispatch('notify', [
            'message' => 'Produk berhasil ditambahkan!'
        ]);
    }

    public function render()
    {
        return view('livewire.shared.product.product-create', [
            'categories' => Category::all() // untuk dropdown kategori
        ]);
    }
}
