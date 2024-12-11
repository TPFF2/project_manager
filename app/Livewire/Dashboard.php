<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Dashboard extends Component
{
    #[Layout('components.layouts.app')]
    #[Title('Tableau de Bord')]
    public function render()
    {
        return view('livewire.dashboard');
    }
}
