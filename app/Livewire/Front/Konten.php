<?php

namespace App\Livewire\Front;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.front')]

class Konten extends Component
{
    public function render()
    {
        return view('livewire.front.konten');
    }
}
