<?php

namespace App\Livewire\Shared\Product;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductEdit extends Component
{
    use WithFileUploads;

    public $productId;
    public $name, $price, $categoryId, $discount, $stock, $slug;
    public $image, $old_image;
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
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->price = $product->price;
        $this->discount = $product->discount;
        $this->stock = $product->stock;
        $this->categoryId = $product->category_id;
        $this->old_image = $product->image;

        // Ambil semua kategori
        $this->categories = Category::orderBy('name')->get();

        // Tampilkan modal edit
        $this->dispatch('showEditModal');
    }

    public function updatedName()
    {
        // Slug otomatis
        $this->slug = Str::slug($this->name);
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
            'slug' => 'required|unique:products,slug,' . $this->productId,
            'categoryId' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|max:2048', // max 2MB
        ]);

        $product = Product::findOrFail($this->productId);

        // Simpan gambar baru jika ada
        if ($this->image) {
            // Hapus gambar lama jika ada
            if ($this->old_image && Storage::disk('public')->exists($this->old_image)) {
                Storage::disk('public')->delete($this->old_image);
            }

            // Simpan gambar baru
            $imagePath = $this->image->store('products', 'public');
        } else {
            $imagePath = $this->old_image;
        }

        // Normalisasi nilai discount sebelum disimpan
        $discount = $this->discount === '' ? null : $this->discount;

        $product->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'category_id' => $this->categoryId,
            'price' => $this->price,
            'stock' => $this->stock,
            'discount' => $discount, // sudah aman
            'image' => $imagePath,
        ]);


        // Tutup modal dan beri notifikasi
        $this->dispatch('hideEditModal');
        $this->dispatch('productUpdated');
        $this->dispatch('notify', ['message' => 'Produk berhasil diperbarui.']);

        // Reset field
        $this->reset(['productId', 'name', 'slug', 'price', 'categoryId', 'discount', 'stock', 'image', 'old_image']);
    }
}
