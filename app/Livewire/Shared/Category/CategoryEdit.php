<?php

namespace App\Livewire\Shared\Category;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryEdit extends Component
{
    public $categoryId, $name, $slug;
    protected $listeners = ['openEditModal' => 'loadData'];

    public function render()
    {
        return view('livewire.shared.category.category-edit');
    }

    public function loadData($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;

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
        ]);

        $category = Category::findOrFail($this->categoryId);
        $category->update([
            'name' => $this->name,
            'slug' => $this->slug,
        ]);

        $this->dispatch('hideEditModal');
        $this->dispatch('categoryUpdated');
        $this->dispatch('notify', ['message' => 'Kategori berhasil diperbarui.']);

        $this->reset(['categoryId', 'name', 'slug']); // biar form bersih lagi
    }
}
