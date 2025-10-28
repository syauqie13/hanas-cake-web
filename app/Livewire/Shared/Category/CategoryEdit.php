<?php

namespace App\Livewire\Shared\Category;

use App\Models\Category;
use Livewire\Component;

class CategoryEdit extends Component
{
    public $categoryId, $name;
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

        $this->dispatch('showEditModal');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
        ]);

        $category = Category::findOrFail($this->categoryId);
        $category->update([
            'name' => $this->name,
        ]);

        $this->dispatch('hideEditModal');
        $this->dispatch('categoryUpdated');
        $this->dispatch('notify', ['message' => 'Kategori berhasil diperbarui.']);

        $this->reset(['categoryId', 'name']); // biar form bersih lagi
    }
}
