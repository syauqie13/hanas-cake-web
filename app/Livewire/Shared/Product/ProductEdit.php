<?php

namespace App\Livewire\Shared\Product;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;

class ProductEdit extends Component
{
    public $productId, $name, $price, $categoryId, $discount, $stock;
    public $categories = [];

    protected $listeners = ['openEditModal' => 'loadData'];

    public function render()
    {
        return view('livewire.shared.product.product-edit', [
            'categories' => $this->categories,
        ]);
    }

    public function loadData($id)
    {
        $product = Product::findOrFail($id);

        // Isi semua field sesuai data produk
        $this->productId   = $product->id;
        $this->name        = $product->name;
        $this->price       = $product->price;
        $this->discount    = $product->discount;
        $this->stock       = $product->stock;
        $this->categoryId  = $product->category_id;

        // Ambil semua kategori
        $this->categories = Category::orderBy('name')->get();

        // Tampilkan modal edit
        $this->dispatch('showEditModal');
    }

    public function update()
    {
        $this->validate([
            'name'       => 'required|min:3',
            'categoryId' => 'required|exists:categories,id',
            'price'      => 'required|numeric|min:0',
            'stock'      => 'required|integer|min:0',
            'discount'   => 'nullable|numeric|min:0|max:100',
        ]);

        $product = Product::findOrFail($this->productId);

        // Update data produk
        $product->update([
            'name'        => $this->name,
            'category_id' => $this->categoryId, // ← disamakan dengan variabel public
            'price'       => $this->price,
            'stock'       => $this->stock,
            'discount'    => $this->discount,
        ]);

        // Tutup modal dan beri notifikasi
        $this->dispatch('hideEditModal');
        $this->dispatch('productUpdated');
        $this->dispatch('notify', ['message' => 'Produk berhasil diperbarui.']);

        // Reset field
        $this->reset(['productId', 'name', 'price', 'categoryId', 'discount', 'stock']);
    }
}
