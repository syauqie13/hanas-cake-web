<?php

namespace App\Livewire\Shared\Product;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ProductList extends Component
{
    public $products = [];

    protected $listeners = [
        'productCreated' => 'loadProduct',   // ✅ nama method harus sama
        'productUpdated' => 'loadProduct',
        'deleteConfirmed' => 'delete'
    ];

    #[On('productCreated')]
    #[On('productUpdated')]
    public function mount()
    {
        // Load data saat pertama kali komponen dijalankan
        $this->loadProduct();
    }

    public function create()
    {
        $this->dispatch('openCreateModal');
    }

    public function loadProduct()
    {
        // Ambil semua data produk dengan relasi kategori
        $this->products = Product::with('category')
            ->latest()
            ->get();
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        // Refresh data
        $this->loadProduct();

        // Kirim notifikasi ke frontend
        $this->dispatch('notify', [
            'message' => 'Produk berhasil dihapus!'
        ]);
    }

    public function edit($id)
    {
        $this->dispatch('openEditModal', id: $id);
    }

    public function render()
    {
        return view('livewire.shared.product.product-list', [
            'products' => $this->products,
        ]);
    }
}
