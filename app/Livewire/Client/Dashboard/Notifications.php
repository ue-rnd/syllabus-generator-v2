<?php

namespace App\Livewire\Client\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('livewire.client.dashboard.base')]
class Notifications extends Component
{
    public function render()
    {
        return view('livewire.client.dashboard.notifications');
    }
}
