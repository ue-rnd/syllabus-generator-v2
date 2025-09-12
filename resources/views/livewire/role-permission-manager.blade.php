<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Role & Permission Management</h1>
        <p class="mt-2 text-sm text-gray-600">Manage user roles and permissions for your application</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button wire:click="switchTab('roles')" 
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'roles' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Roles
            </button>
            <button wire:click="switchTab('permissions')" 
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'permissions' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Permissions
            </button>
            <button wire:click="switchTab('users')" 
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'users' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                User Assignments
            </button>
        </nav>
    </div>

    <!-- Search Bar -->
    <div class="mt-6">
        <div class="max-w-md">
            <input wire:model.live="search" 
                   type="text" 
                   placeholder="Search {{ $activeTab }}..." 
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        </div>
    </div>

    <!-- Content based on active tab -->
    @if($activeTab === 'roles')
        <!-- Roles Tab -->
        <div class="mt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900">Roles</h2>
                <button wire:click="showModal('role')" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Create Role
                </button>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @forelse($roles as $role)
                        <li class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $role->name }}</h3>
                                    <div class="mt-2">
                                        <span class="text-sm text-gray-500">Permissions:</span>
                                        <div class="mt-1 flex flex-wrap gap-1">
                                            @forelse($role->permissions as $permission)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $permission->name }}
                                                </span>
                                            @empty
                                                <span class="text-sm text-gray-400">No permissions assigned</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button wire:click="showModal('role', {{ $role->id }})" 
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        Edit
                                    </button>
                                    <button wire:click="deleteRole({{ $role->id }})" 
                                            wire:confirm="Are you sure you want to delete this role?"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-4 text-center text-gray-500">No roles found</li>
                    @endforelse
                </ul>
            </div>

            {{ $roles->links() }}
        </div>

    @elseif($activeTab === 'permissions')
        <!-- Permissions Tab -->
        <div class="mt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900">Permissions</h2>
                <button wire:click="showModal('permission')" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Create Permission
                </button>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @forelse($permissions as $permission)
                        <li class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $permission->name }}</h3>
                                </div>
                                <div class="flex space-x-2">
                                    <button wire:click="showModal('permission', {{ $permission->id }})" 
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        Edit
                                    </button>
                                    <button wire:click="deletePermission({{ $permission->id }})" 
                                            wire:confirm="Are you sure you want to delete this permission?"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-4 text-center text-gray-500">No permissions found</li>
                    @endforelse
                </ul>
            </div>

            {{ $permissions->links() }}
        </div>

    @elseif($activeTab === 'users')
        <!-- Users Tab -->
        <div class="mt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900">User Assignments</h2>
                <div class="space-x-2">
                    <button wire:click="showModal('assign-role')" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Assign Role
                    </button>
                    <button wire:click="showModal('assign-permission')" 
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Assign Permission
                    </button>
                </div>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <li class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    
                                    <div class="mt-3">
                                        <div class="mb-2">
                                            <span class="text-sm text-gray-500">Roles:</span>
                                            <div class="mt-1 flex flex-wrap gap-1">
                                                @forelse($user->roles as $role)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ $role->name }}
                                                        <button wire:click="removeUserRole({{ $user->id }}, '{{ $role->name }}')" 
                                                                class="ml-1 text-green-600 hover:text-green-800">
                                                            ×
                                                        </button>
                                                    </span>
                                                @empty
                                                    <span class="text-sm text-gray-400">No roles assigned</span>
                                                @endforelse
                                            </div>
                                        </div>
                                        
<div>
                                            <span class="text-sm text-gray-500">Direct Permissions:</span>
                                            <div class="mt-1 flex flex-wrap gap-1">
                                                @forelse($user->permissions as $permission)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        {{ $permission->name }}
                                                        <button wire:click="removeUserPermission({{ $user->id }}, '{{ $permission->name }}')" 
                                                                class="ml-1 text-purple-600 hover:text-purple-800">
                                                            ×
                                                        </button>
                                                    </span>
                                                @empty
                                                    <span class="text-sm text-gray-400">No direct permissions</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-4 text-center text-gray-500">No users found</li>
                    @endforelse
                </ul>
            </div>

            {{ $users->links() }}
        </div>
    @endif

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    @if($modalType === 'role')
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            {{ $editingRole ? 'Edit Role' : 'Create Role' }}
                        </h3>
                        <form wire:submit.prevent="saveRole">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Role Name</label>
                                <input wire:model="roleName" 
                                       type="text" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                @error('roleName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Permissions</label>
                                <div class="mt-2 max-h-40 overflow-y-auto border border-gray-300 rounded-md p-2">
                                    @foreach(\Spatie\Permission\Models\Permission::all() as $permission)
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   wire:model="selectedPermissions" 
                                                   value="{{ $permission->id }}"
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-2">
                                <button type="button" 
                                        wire:click="closeModal" 
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                                    {{ $editingRole ? 'Update' : 'Create' }}
                                </button>
                            </div>
                        </form>

                    @elseif($modalType === 'permission')
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            {{ $editingPermission ? 'Edit Permission' : 'Create Permission' }}
                        </h3>
                        <form wire:submit.prevent="savePermission">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Permission Name</label>
                                <input wire:model="permissionName" 
                                       type="text" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                @error('permissionName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="flex justify-end space-x-2">
                                <button type="button" 
                                        wire:click="closeModal" 
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                                    {{ $editingPermission ? 'Update' : 'Create' }}
                                </button>
                            </div>
                        </form>

                    @elseif($modalType === 'assign-role')
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Assign Role to User</h3>
                        <form wire:submit.prevent="assignRole">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">User Email</label>
                                <input wire:model="selectedUser" 
                                       type="email" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                @error('selectedUser') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Role</label>
                                <select wire:model="selectedRole" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">Select a role</option>
                                    @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedRole') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="flex justify-end space-x-2">
                                <button type="button" 
                                        wire:click="closeModal" 
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md">
                                    Assign Role
                                </button>
                            </div>
                        </form>

                    @elseif($modalType === 'assign-permission')
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Assign Permission to User</h3>
                        <form wire:submit.prevent="assignPermission">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">User Email</label>
                                <input wire:model="selectedUser" 
                                       type="email" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                @error('selectedUser') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Permission</label>
                                <select wire:model="selectedPermission" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="">Select a permission</option>
                                    @foreach(\Spatie\Permission\Models\Permission::all() as $permission)
                                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedPermission') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="flex justify-end space-x-2">
                                <button type="button" 
                                        wire:click="closeModal" 
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-md">
                                    Assign Permission
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
