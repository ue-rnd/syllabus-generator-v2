<?php

namespace App\Livewire\Client\Dashboard;

use App\Models\Syllabus;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('livewire.client.dashboard.base')]
class Home extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount()
    {
        // Any initialization logic can go here
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        // Get syllabi where user is the principal preparer or assigned in prepared_by
        $syllabiQuery = Syllabus::with(['course.college', 'principalPreparer'])
            ->where(function ($query) use ($user) {
                $query->where('principal_prepared_by', $user->id)
                    ->orWhereJsonContains('prepared_by', [['user_id' => $user->id]]);
            });

        // Apply search filter
        if ($this->search) {
            $syllabiQuery->where(function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhereHas('course', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('code', 'like', '%'.$this->search.'%');
                    });
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            $syllabiQuery->where('status', $this->statusFilter);
        }

        // Order by latest first
        $syllabi = $syllabiQuery->latest()->paginate(12);

        return view('livewire.client.dashboard.home', [
            'syllabi' => $syllabi,
        ]);
    }
}
