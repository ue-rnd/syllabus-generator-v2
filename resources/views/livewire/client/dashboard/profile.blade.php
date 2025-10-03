<div class="p-4" x-data="{ show: false, showPwd: false, profileOpen: false, passwordOpen: false }" x-on:profile-updated.window="show = true; setTimeout(() => show = false, 2500); profileOpen = false" x-on:password-updated.window="showPwd = true; setTimeout(() => showPwd = false, 2500); passwordOpen = false">
    <div class="fixed top-4 right-4 z-50 space-y-2">
        <div x-show="show" x-transition.opacity x-transition.duration.300ms class="rounded-md bg-green-600 text-white shadow-lg px-4 py-2 flex items-center gap-2">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414l2.293 2.293 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            <span>Profile updated</span>
        </div>
        <div x-show="showPwd" x-transition.opacity x-transition.duration.300ms class="rounded-md bg-green-600 text-white shadow-lg px-4 py-2 flex items-center gap-2">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414l2.293 2.293 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            <span>Password updated</span>
        </div>
    </div>
    @php($user = auth()->user())
    <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="border bg-white rounded-xl p-6 space-y-4">
                <div class="mx-auto rounded-full size-24 bg-red-600 text-white flex items-center justify-center text-3xl font-semibold">
                    <span>{{ method_exists($user, 'initials') ? $user->initials() : substr($user?->name ?? 'U', 0, 2) }}</span>
                </div>
                <div class="text-center space-y-1">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $user?->name ?? '-' }}</h2>
                    @if(method_exists($user, 'isFaculty') && $user->isFaculty())
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Faculty</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">User</span>
                    @endif
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Email</span>
                        <span class="text-gray-900">{{ $user?->email ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Position</span>
                        <span class="text-gray-900">{{ $user?->position ?: '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">College</span>
                        <span class="text-gray-900">{{ optional($user?->college)->code ?? optional($user?->college)->name ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-2">
            <div class="border bg-white rounded-xl p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Account</h3>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="profileOpen = true" class="px-3 py-1.5 rounded-xl border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">Edit profile</button>
                        <button type="button" @click="passwordOpen = true" class="px-3 py-1.5 rounded-xl bg-red-700 text-white text-sm hover:bg-red-800">Change password</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <span class="text-xs uppercase tracking-wide text-gray-500">First name</span>
                        <div class="text-gray-900">{{ $user?->firstname ?? '-' }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs uppercase tracking-wide text-gray-500">Last name</span>
                        <div class="text-gray-900">{{ $user?->lastname ?? '-' }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs uppercase tracking-wide text-gray-500">Middle name</span>
                        <div class="text-gray-900">{{ $user?->middlename ?? '-' }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs uppercase tracking-wide text-gray-500">Email</span>
                        <div class="text-gray-900">{{ $user?->email ?? '-' }}</div>
                    </div>
                </div>

                @if(method_exists($user, 'isFaculty') && $user->isFaculty())
                <div class="pt-2 border-t">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Faculty information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <span class="text-xs uppercase tracking-wide text-gray-500">College name</span>
                            <div class="text-gray-900">{{ optional($user?->college)->name ?? '-' }}</div>
                        </div>
                        <div class="space-y-1">
                            <span class="text-xs uppercase tracking-wide text-gray-500">College code</span>
                            <div class="text-gray-900">{{ optional($user?->college)->code ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Edit Profile Modal -->
    <div x-cloak x-show="profileOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center">
        <div @click="profileOpen = false" class="absolute inset-0 bg-black/40"></div>
        <div class="relative bg-white rounded-xl shadow-lg w-full max-w-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Edit profile</h3>
                <button type="button" @click="profileOpen = false" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>
            <form wire:submit.prevent="updateProfileInformation" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs uppercase tracking-wide text-gray-500">First name</label>
                    <input type="text" wire:model.defer="firstname" class="w-full border px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-red-400" />
                    @error('firstname') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs uppercase tracking-wide text-gray-500">Last name</label>
                    <input type="text" wire:model.defer="lastname" class="w-full border px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-red-400" />
                    @error('lastname') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs uppercase tracking-wide text-gray-500">Middle name</label>
                    <input type="text" wire:model.defer="middlename" class="w-full border px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-red-400" />
                    @error('middlename') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs uppercase tracking-wide text-gray-500">Email</label>
                    <input type="email" wire:model.defer="email" class="w-full border px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-red-400" />
                    @error('email') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2 flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="profileOpen = false" class="px-3 py-2 rounded-xl border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-3 py-2 rounded-xl bg-red-700 text-white text-sm hover:bg-red-800">Save changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div x-cloak x-show="passwordOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center">
        <div @click="passwordOpen = false" class="absolute inset-0 bg-black/40"></div>
        <div class="relative bg-white rounded-xl shadow-lg w-full max-w-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900">Change password</h3>
                <button type="button" @click="passwordOpen = false" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>
            <form wire:submit.prevent="updatePassword" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1 md:col-span-2">
                    <label class="text-xs uppercase tracking-wide text-gray-500">Current password</label>
                    <input type="password" wire:model.defer="current_password" class="w-full border px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-red-400" />
                    @error('current_password') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs uppercase tracking-wide text-gray-500">New password</label>
                    <input type="password" wire:model.defer="password" class="w-full border px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-red-400" />
                    @error('password') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs uppercase tracking-wide text-gray-500">Confirm password</label>
                    <input type="password" wire:model.defer="password_confirmation" class="w-full border px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-red-400" />
                </div>
                <div class="md:col-span-2 flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="passwordOpen = false" class="px-3 py-2 rounded-xl border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-3 py-2 rounded-xl bg-red-700 text-white text-sm hover:bg-red-800">Update password</button>
                </div>
            </form>
        </div>
    </div>
</div>