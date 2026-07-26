<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Profile')]
class Profile extends Component
{
    public function render()
    {
        return view('livewire.pages.profile');
    }
}
