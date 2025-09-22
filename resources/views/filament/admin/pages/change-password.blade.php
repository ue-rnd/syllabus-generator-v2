<x-filament-panels::page>
    <div class="max-w-2xl mx-auto">
        <x-filament::section>
            <x-slot name="heading">
                {{ $this->getHeading() }}
            </x-slot>

            @if($this->getSubheading())
                <x-slot name="description">
                    {{ $this->getSubheading() }}
                </x-slot>
            @endif

            <x-filament-panels::form wire:submit="changePassword">
                {{ $this->form }}

                <x-slot name="footer">
                    <div class="flex justify-end">
                        {{ $this->getFormActions() }}
                    </div>
                </x-slot>
            </x-filament-panels::form>
        </x-filament::section>

        <x-filament::section class="mt-6">
            <x-slot name="heading">
                Password Security Requirements
            </x-slot>

            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <p>Your password must meet the following requirements:</p>
                <ul class="list-disc list-inside space-y-1 ml-4">
                    <li>At least 8 characters long</li>
                    <li>Contains at least one uppercase letter (A-Z)</li>
                    <li>Contains at least one lowercase letter (a-z)</li>
                    <li>Contains at least one number (0-9)</li>
                    <li>Contains at least one special character (!@#$%^&*)</li>
                    <li>Cannot be the same as your current password</li>
                </ul>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>