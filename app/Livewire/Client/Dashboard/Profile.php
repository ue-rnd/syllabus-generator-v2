<?php

namespace App\Livewire\Client\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('livewire.client.dashboard.base')]
class Profile extends Component
{
    public function render()
    {
        return view('livewire.client.dashboard.profile');
    }
}
