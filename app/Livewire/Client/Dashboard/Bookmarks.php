<?php

namespace App\Livewire\Client\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('livewire.client.dashboard.base')]
class Bookmarks extends Component
{
    public function render()
    {
        return view('livewire.client.dashboard.bookmarks');
    }
}
