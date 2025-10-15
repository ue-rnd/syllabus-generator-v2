<x-filament-panels::page>
    <div class="max-w-4xl mx-auto">
        <x-filament::section>
            <x-slot name="heading">
                Profile Management
            </x-slot>

            <x-slot name="description">
                Manage your personal information and preferences.
            </x-slot>

            <form wire:submit="save">
                {{ $this->form }}

                <div class="flex justify-between mt-6">
                    <div class="flex space-x-3">
                        {{ $this->getFormActions()[1] ?? '' }}
                        {{ $this->getFormActions()[2] ?? '' }}
                    </div>
                    <div>
                        {{ $this->getFormActions()[0] ?? '' }}
                    </div>
                </div>
            </form>
        </x-filament::section>

        <x-filament::section class="mt-6">
            <x-slot name="heading">
                Account Information
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Account Status</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                            <span class="@if(auth()->user()->is_active) text-green-600 @else text-red-600 @endif">
                                {{ auth()->user()->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Last Login:</span>
                            <span>{{ auth()->user()->last_login_at?->format('M j, Y g:i A') ?? 'Never' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Member Since:</span>
                            <span>{{ auth()->user()->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Two-Factor Auth:</span>
                            <span class="@if(auth()->user()->two_factor_enabled) text-green-600 @else text-orange-600 @endif">
                                {{ auth()->user()->two_factor_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">System Role</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Primary Role:</span>
                            <span class="font-medium">{{ auth()->user()->getPrimaryRoleAttribute() }}</span>
                        </div>
                        @if(auth()->user()->roles->count() > 1)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Additional Roles:</span>
                                <span>{{ auth()->user()->roles->skip(1)->pluck('name')->join(', ') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Academic Position:</span>
                            <span>{{ auth()->user()->getPositionTitleAttribute() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>