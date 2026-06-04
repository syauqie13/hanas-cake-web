<div class="w-full min-h-screen bg-[#f8f5f2] font-sans pb-24">
    
    {{-- Header --}}
    <div class="bg-white sticky top-0 z-20 border-b border-gray-100 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center gap-3">
            <a href="{{ route('ecommerce') }}" wire:navigate class="text-gray-400 hover:text-[#5c4033] transition">
                <i class="fas fa-chevron-left text-lg"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-lg font-bold text-[#2d3748]">Produk Favoritku</h1>
                <p class="text-xs text-gray-400 mt-0.5">{{ $favorites->count() }} produk tersimpan</p>
            </div>
            <div class="w-9 h-9 bg-red-50 rounded-full flex items-center justify-center">
                <i class="fas fa-heart text-red-400"></i>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 pt-6">

        @if($favorites->isEmpty())
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-md mb-5 border border-gray-100">
                    <i class="far fa-heart text-4xl text-gray-200"></i>
                </div>
                <h3 class="text-base font-bold text-gray-700 mb-2">Belum ada produk favorit</h3>
                <p class="text-sm text-gray-400 leading-relaxed mb-6 max-w-xs">
                    Tap ikon ❤️ di kartu produk untuk menyimpan produk kesukaanmu di sini.
                </p>
                <a href="{{ route('ecommerce') }}" wire:navigate
                    class="inline-flex items-center gap-2 bg-[#5c4033] text-white text-sm font-bold px-6 py-3 rounded-2xl shadow-md hover:bg-[#4a3328] transition-all active:scale-[0.98]">
                    <i class="fas fa-store"></i> Jelajahi Produk
                </a>
            </div>
        @else
            {{-- Favorites Grid --}}
            <div class="grid grid-cols-2 gap-3 md:gap-5 md:grid-cols-3 lg:grid-cols-4">
                @foreach($favorites as $product)
                    <div wire:key="fav-{{ $product->id }}"
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col relative group hover:shadow-md transition-shadow">

                        {{-- Favorite Toggle Button --}}
                        <button wire:click.stop="toggleFavorite({{ $product->id }})"
                            class="absolute top-2 right-2 z-10 w-8 h-8 bg-white/80 backdrop-blur-sm rounded-full flex items-center justify-center shadow-sm border border-red-100 hover:scale-110 transition-transform active:scale-95 group/btn">
                            <i class="fas fa-heart text-red-500 text-sm group-hover/btn:text-red-400 transition-colors"></i>
                        </button>

                        {{-- Badge --}}
                        @if($loop->index < 3)
                            <div class="absolute top-2 left-2 z-10">
                                <span class="bg-[#eedcd3] text-[#5c4033] text-[9px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1 shadow-sm">
                                    <i class="fas fa-star text-yellow-500"></i> Most Popular
                                </span>
                            </div>
                        @endif

                        {{-- Product Image --}}
                        <div class="h-36 md:h-44 bg-gray-100 relative overflow-hidden shrink-0">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/400x400/eedcd3/5c4033?text=Kue' }}"
                                alt="{{ $product->name }}"
                                class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105">
                        </div>

                        {{-- Content --}}
                        <div class="p-3 flex flex-col flex-1">
                            <p class="text-[10px] text-gray-400 mb-0.5">{{ $product->category->name ?? 'Kategori' }}</p>
                            <h3 class="text-xs md:text-sm font-bold text-gray-800 line-clamp-2 leading-tight mb-1 flex-1">
                                {{ $product->name }}
                            </h3>
                            @if($product->description)
                                <p class="text-[10px] text-gray-400 leading-snug line-clamp-2 mb-2">
                                    {{ $product->description }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-50">
                                <span class="text-sm font-bold text-[#5c4033]">
                                    Rp {{ number_format($product->price - ($product->price * $product->discount / 100), 0, ',', '.') }}
                                </span>
                                <a href="{{ route('ecommerce') }}" wire:navigate
                                    class="w-7 h-7 rounded-full bg-[#5c4033] text-white flex items-center justify-center hover:bg-[#4a332a] hover:scale-110 transition-transform active:scale-95"
                                    title="Pesan sekarang">
                                    <i class="fas fa-plus text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
