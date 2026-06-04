<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Models\Favorite;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.ecommerce')]
#[Title('Produk Favorit Saya')]
class MyFavorites extends Component
{
    /**
     * Toggle favorit dari halaman manapun (shop / favorites page)
     * Dipanggil via wire:click.stop agar tidak trigger event parent
     */
    public function toggleFavorite($productId)
    {
        if (!Auth::check()) {
            $this->dispatch('showLoginWarning');
            return;
        }

        $user = Auth::user();
        $existing = Favorite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->dispatch('notify', message: 'Dihapus dari favorit', icon: 'info');
        } else {
            Favorite::create([
                'user_id'    => $user->id,
                'product_id' => $productId,
            ]);
            $this->dispatch('notify', message: 'Ditambahkan ke favorit ❤️', icon: 'success');
        }
    }

    public function render()
    {
        $user = Auth::user();

        $favorites = collect();
        $favoriteIds = collect();

        if ($user) {
            $favorites = $user->favoriteProducts()
                ->with('category')
                ->latest('favorites.created_at')
                ->get();

            $favoriteIds = $favorites->pluck('id');
        }

        return view('livewire.frontend.my-favorites', [
            'favorites'   => $favorites,
            'favoriteIds' => $favoriteIds,
        ]);
    }
}
