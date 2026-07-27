<?php

namespace App\Livewire\Pages;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Perfil')]
class Profile extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public function mount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->name = $user->name ?? '';
            $this->email = $user->email ?? '';
            $this->phone = $user->phone ?? '';
        }
    }

    public function updateProfile()
    {
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        session()->flash('message', 'Perfil actualizado con éxito.');
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }

    public function render()
    {
        return view('livewire.pages.profile');
    }
}
