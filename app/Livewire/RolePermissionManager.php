<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RolePermissionManager extends Component
{
    use WithPagination;

    public $activeTab = 'roles';
    public $showModal = false;
    public $modalType = '';
    
    // Role properties
    public $roleName = '';
    public $selectedPermissions = [];
    public $editingRole = null;
    
    // Permission properties
    public $permissionName = '';
    public $editingPermission = null;
    
    // User assignment properties
    public $selectedUser = '';
    public $selectedRole = '';
    public $selectedPermission = '';
    
    // Search and filter
    public $search = '';
    public $roleFilter = '';

    protected $rules = [
        'roleName' => 'required|string|max:255',
        'permissionName' => 'required|string|max:255',
        'selectedUser' => 'required|email',
        'selectedRole' => 'required|string',
        'selectedPermission' => 'required|string',
    ];

    public function mount()
    {
        $this->loadPermissions();
    }

    public function render()
    {
        $roles = Role::with('permissions')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        $permissions = Permission::when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        $users = User::with('roles', 'permissions')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.role-permission-manager', [
            'roles' => $roles,
            'permissions' => $permissions,
            'users' => $users,
        ]);
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->reset(['search', 'showModal', 'modalType']);
        $this->resetForm();
    }

    public function showModal($type, $id = null)
    {
        $this->modalType = $type;
        $this->showModal = true;
        $this->resetForm();

        if ($id) {
            $this->edit($type, $id);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function edit($type, $id)
    {
        if ($type === 'role') {
            $role = Role::with('permissions')->find($id);
            $this->editingRole = $role;
            $this->roleName = $role->name;
            $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
        } elseif ($type === 'permission') {
            $permission = Permission::find($id);
            $this->editingPermission = $permission;
            $this->permissionName = $permission->name;
        }
    }

    public function saveRole()
    {
        $this->validate(['roleName' => 'required|string|max:255']);

        if ($this->editingRole) {
            $this->editingRole->update(['name' => $this->roleName]);
            $this->editingRole->syncPermissions($this->selectedPermissions);
            session()->flash('message', 'Role updated successfully!');
        } else {
            $role = Role::create(['name' => $this->roleName]);
            $role->givePermissionTo($this->selectedPermissions);
            session()->flash('message', 'Role created successfully!');
        }

        $this->closeModal();
    }

    public function savePermission()
    {
        $this->validate(['permissionName' => 'required|string|max:255']);

        if ($this->editingPermission) {
            $this->editingPermission->update(['name' => $this->permissionName]);
            session()->flash('message', 'Permission updated successfully!');
        } else {
            Permission::create(['name' => $this->permissionName]);
            session()->flash('message', 'Permission created successfully!');
        }

        $this->closeModal();
    }

    public function assignRole()
    {
        $this->validate([
            'selectedUser' => 'required|email',
            'selectedRole' => 'required|string',
        ]);

        $user = User::where('email', $this->selectedUser)->first();
        $role = Role::where('name', $this->selectedRole)->first();

        if ($user && $role) {
            $user->assignRole($role);
            session()->flash('message', 'Role assigned successfully!');
        } else {
            session()->flash('error', 'User or role not found!');
        }

        $this->closeModal();
    }

    public function assignPermission()
    {
        $this->validate([
            'selectedUser' => 'required|email',
            'selectedPermission' => 'required|string',
        ]);

        $user = User::where('email', $this->selectedUser)->first();
        $permission = Permission::where('name', $this->selectedPermission)->first();

        if ($user && $permission) {
            $user->givePermissionTo($permission);
            session()->flash('message', 'Permission assigned successfully!');
        } else {
            session()->flash('error', 'User or permission not found!');
        }

        $this->closeModal();
    }

    public function deleteRole($id)
    {
        $role = Role::find($id);
        if ($role) {
            $role->delete();
            session()->flash('message', 'Role deleted successfully!');
        }
    }

    public function deletePermission($id)
    {
        $permission = Permission::find($id);
        if ($permission) {
            $permission->delete();
            session()->flash('message', 'Permission deleted successfully!');
        }
    }

    public function removeUserRole($userId, $roleName)
    {
        $user = User::find($userId);
        if ($user) {
            $user->removeRole($roleName);
            session()->flash('message', 'Role removed from user successfully!');
        }
    }

    public function removeUserPermission($userId, $permissionName)
    {
        $user = User::find($userId);
        if ($user) {
            $user->revokePermissionTo($permissionName);
            session()->flash('message', 'Permission removed from user successfully!');
        }
    }

    private function loadPermissions()
    {
        // This method can be used to load permissions for the role form
        // The permissions are loaded in the view via the render method
    }

    private function resetForm()
    {
        $this->roleName = '';
        $this->permissionName = '';
        $this->selectedPermissions = [];
        $this->selectedUser = '';
        $this->selectedRole = '';
        $this->selectedPermission = '';
        $this->editingRole = null;
        $this->editingPermission = null;
    }
}
