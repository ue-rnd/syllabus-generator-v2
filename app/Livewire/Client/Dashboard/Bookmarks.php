<?php

namespace App\Livewire\Client\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('livewire.client.dashboard.base')]
class Bookmarks extends Component
{
    public function render()
    {
        return view('livewire.client.dashboard.bookmarks');
    }
}
