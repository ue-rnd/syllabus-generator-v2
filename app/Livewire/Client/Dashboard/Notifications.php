<?php

namespace App\Livewire\Client\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('livewire.client.dashboard.base')]
class Notifications extends Component
{
    public function render()
    {
        return view('livewire.client.dashboard.notifications');
    }
}
