<div class="p-4" x-data="{ show: false, showPwd: false, profileOpen: false, passwordOpen: false, darkMode: false }" x-init="darkMode = (localStorage.getItem('darkMode') === 'true' || (localStorage.getItem('darkMode') === null && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)); if(darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')" x-on:profile-updated.window="show = true; setTimeout(() => show = false, 2500); profileOpen = false" x-on:password-updated.window="showPwd = true; setTimeout(() => showPwd = false, 2500); passwordOpen = false">
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
    <div class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6 text-accent-text">
        <div class="lg:col-span-1">
            <div class="border border-accent-ghost-dark bg-accent-foreground rounded-xl p-6 space-y-4">
                <div class="mx-auto text-white rounded-full size-24 bg-accent-main text-accent flex items-center justify-center text-3xl font-semibold">
                    <span>{{ method_exists($user, 'initials') ? $user->initials() : substr($user?->name ?? 'U', 0, 2) }}</span>
                </div>
                <div class="text-center space-y-1">
                    <h2 class="text-lg font-semibold">{{ $user?->name ?? 'No name' }}</h2>
                    @if(method_exists($user, 'isFaculty') && $user->isFaculty())
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-accent-main text-accent-hover">Faculty</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-accent-ghost-dark text-accent-desc">User</span>
                    @endif
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-accent-desc font-semibold">Email</span>
                        <span class="">{{ $user?->email ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-accent-desc font-semibold">Position</span>
                        <span class="">{{ $user?->position ?: '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-accent-desc font-semibold">College</span>
                        <span class="">{{ optional($user?->college)->code ?? optional($user?->college)->name ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-2">
            <div class="border border-accent-ghost-dark bg-accent-foreground text-accent-text rounded-xl p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold">Account</h3>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="profileOpen = true" class="px-3 py-1.5 rounded-xl border border-accent-ghost-dark text-sm text-accent-text hover:bg-accent-ghost-dark bg-accent-ghost">Edit profile</button>
                        <button type="button" @click="passwordOpen = true" class="px-3 py-1.5 rounded-xl bg-accent-main text-white text-sm hover:bg-accent-hover">Change password</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <span class="text-xs uppercase tracking-wide text-accent-desc">First name</span>
                        <div class="text-accent-text">{{ $user?->firstname ?? '-' }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs uppercase tracking-wide text-accent-desc">Last name</span>
                        <div class="text-accent-text">{{ $user?->lastname ?? '-' }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs uppercase tracking-wide text-accent-desc">Middle name</span>
                        <div class="text-accent-text">{{ $user?->middlename ?? '-' }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs uppercase tracking-wide text-accent-desc">Email</span>
                        <div class="text-accent-text">{{ $user?->email ?? '-' }}</div>
                    </div>
                </div>

                @if(method_exists($user, 'isFaculty') && $user->isFaculty())
                <div class="pt-2 border-t border-accent-ghost-dark text-accent-text">
                    <h4 class="text-sm font-semibold mb-3">Faculty information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <span class="text-xs uppercase tracking-wide text-accent-desc">College name</span>
                            <div class="">{{ optional($user?->college)->name ?? '-' }}</div>
                        </div>
                        <div class="space-y-1">
                            <span class="text-xs uppercase tracking-wide text-accent-desc">College code</span>
                            <div class="">{{ optional($user?->college)->code ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Edit Profile Modal -->
    <div x-cloak x-show="profileOpen" x-transition.opacity class="fixed inset-0 z-80 flex items-center justify-center">
        <div @click="profileOpen = false" class="absolute inset-0 bg-black/40"></div>
        <div class="relative bg-accent-foreground rounded-xl shadow-lg w-full max-w-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-accent-text text-lg">Edit Profile</h3>
                <button type="button" @click="profileOpen = false" class="text-accent-text hover:text-accent-desc">✕</button>
            </div>
            <form wire:submit.prevent="updateProfileInformation" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-accent-text">
                <div class="space-y-1">
                    <label class="text-xs uppercase tracking-wide text-accent-desc font-bold">First name</label>
                    <input type="text" wire:model.defer="firstname" class="w-full border border-accent-ghost-dark px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-accent-main" />
                    @error('firstname') <span class="text-xs text-accent-error">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs uppercase tracking-wide text-accent-desc font-bold">Last name</label>
                    <input type="text" wire:model.defer="lastname" class="w-full border border-accent-ghost-dark px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-accent-main" />
                    @error('lastname') <span class="text-xs text-accent-error">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs uppercase tracking-wide text-accent-desc font-bold">Middle name</label>
                    <input type="text" wire:model.defer="middlename" class="w-full border border-accent-ghost-dark px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-accent-main" />
                    @error('middlename') <span class="text-xs text-accent-error">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs uppercase tracking-wide text-accent-desc font-bold">Email</label>
                    <input type="email" wire:model.defer="email" class="w-full border border-accent-ghost-dark px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-accent-main" />
                    @error('email') <span class="text-xs text-accent-error">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2 flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="profileOpen = false" class="px-3 py-2 rounded-xl border border-accent-ghost-dark text-accent-desc text-sm bg-accent-ghost hover:bg-accent-ghost-dark">Cancel</button>
                    <button type="submit" class="px-3 py-2 rounded-xl bg-accent-main text-white text-sm hover:bg-accent-hover">Save changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div x-cloak x-show="passwordOpen" x-transition.opacity class="fixed inset-0 z-80 flex items-center justify-center">
        <div @click="passwordOpen = false" class="absolute inset-0 bg-black/40"></div>
        <div class="relative bg-accent-foreground rounded-xl shadow-lg w-full max-w-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-accent-text text-lg">Change Password</h3>
                <button type="button" @click="passwordOpen = false" class="text-accent-text hover:text-accent-desc">✕</button>
            </div>
            <form wire:submit.prevent="updatePassword" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1 md:col-span-2">
                    <label class="text-xs uppercase tracking-wide text-accent-desc font-bold">Current password</label>
                    <input type="password" wire:model.defer="current_password" class="w-full border border-accent-ghost-dark px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-accent-main" />
                    @error('current_password') <span class="text-xs text-accent-error">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs uppercase tracking-wide text-accent-desc font-bold">New password</label>
                    <input type="password" wire:model.defer="password" class="w-full border border-accent-ghost-dark px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-accent-main" />
                    @error('password') <span class="text-xs text-accent-error">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-1">
                    <label class="text-xs uppercase tracking-wide text-accent-desc font-bold">Confirm password</label>
                    <input type="password" wire:model.defer="password_confirmation" class="w-full border border-accent-ghost-dark px-3 py-2 rounded-xl focus:outline-none focus:ring-1 focus:ring-accent-main" />
                </div>
                <div class="md:col-span-2 flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="passwordOpen = false" class="px-3 py-2 rounded-xl border border-accent-ghost-dark bg-accent-ghost text-accent-desc text-sm hover:bg-accent-ghost-dark">Cancel</button>
                    <button type="submit" class="px-3 py-2 rounded-xl bg-accent-main text-white text-sm hover:bg-accent-hover">Update password</button>
                </div>
            </form>
        </div>
    </div>
</div>