<?php

namespace App\Livewire\Shared\Product;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductCreate extends Component
{
    use WithFileUploads;

    public $category_id, $name, $slug, $price, $stock, $discount, $image;
    public $showModal = false;

    protected $listeners = [
        'openCreateModal' => 'showCreateModal',
    ];

    /**
     * Tampilkan modal dan reset form
     */
    public function showCreateModal()
    {
        $this->reset(['category_id', 'name', 'slug', 'price', 'stock', 'discount', 'image']);
        $this->showModal = true;

        $this->dispatch('show-create-modal');
    }

    /**
     * Generate slug otomatis saat mengetik nama
     */
    public function generateSlug()
    {
        $this->slug = Str::slug($this->name);
    }

    public function updatedName()
    {
        $this->generateSlug();
    }

    /**
     * Simpan produk baru
     */
    public function save()
    {
        $validated = $this->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|min:3|max:255|unique:products,name',
            'slug' => 'required|string|unique:products,slug',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|max:2048', // maksimal 2MB
        ]);

        // Simpan gambar jika diupload
        $imagePath = $this->image
            ? $this->image->store('products', 'public')
            : null;

        // Buat slug unik (jika slug bentrok)
        $slug = $validated['slug'];
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        // Simpan ke database
        Product::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'discount' => $validated['discount'] ?? 0,
            'image' => $imagePath,
        ]);

        // Tutup modal & refresh
        $this->dispatch('hide-create-modal');
        $this->dispatch('productCreated');
        $this->dispatch('notify', ['message' => 'Produk berhasil ditambahkan!']);

        // Reset form agar bersih
        $this->reset(['category_id', 'name', 'slug', 'price', 'stock', 'discount', 'image']);
    }

    public function render()
    {
        return view('livewire.shared.product.product-create', [
            'categories' => Category::orderBy('name')->get()
        ]);
    }
}
