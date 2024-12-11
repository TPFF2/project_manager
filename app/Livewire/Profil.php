<?php

namespace App\Livewire;

use App\Services\Discord\Discord;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Profil extends Component
{
    public array $userInfo;
    public array|string|null $serverInfo = [];
    protected $discord;

    public function __construct()
    {
        $this->discord = new Discord();
    }


    public function loadUserInfo()
    {
        try {
            $this->userInfo = $this->discord->makeRequest('GET', '/users/@me');
        } catch (\Exception $e) {
            $this->addError('discord', "Erreur lors de la récupération des informations de compte !:".$e->getMessage());
        }
    }

    #[Layout('components.layouts.app')]
    #[Title('Mon profil')]
    public function render()
    {
        $this->loadUserInfo();
        return view('livewire.profil', ['discord' => $this->discord]);
    }
}
