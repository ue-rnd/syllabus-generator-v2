

@php
    $data = $this->getData();
    $user = $data['user'];
    $college = $data['college'];
    $department = $data['department'];
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold text-lg">
                    {{ strtoupper(substr($user->firstname ?? $user->name, 0, 1)) }}{{ strtoupper(substr($user->lastname ?? '', 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Welcome, {{ $user->full_name ?? $user->name }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ now()->format('l, F j, Y') }}
                    </p>
                </div>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Personal Information -->
            <div class="space-y-3">
                <h3 class="font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                    Personal Information
                </h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Full Name:</span>
                        <p class="font-medium">{{ $user->full_name ?? $user->name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Email:</span>
                        <p class="font-medium">{{ $user->email }}</p>
                    </div>
                    @if($user->middlename)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Middle Name:</span>
                        <p class="font-medium">{{ $user->middlename }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Role & Position -->
            <div class="space-y-3">
                <h3 class="font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                    Role & Position
                </h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Position:</span>
                        <p class="font-medium">
                            <x-filament::badge
                                :color="match($user->position) {
                                    'faculty' => 'primary',
                                    'department_chair' => 'success',
                                    'associate_dean' => 'warning',
                                    'dean' => 'danger',
                                    'superadmin' => 'purple',
                                    default => 'gray'
                                }"
                            >
                                {{ ucwords(str_replace('_', ' ', $user->position ?? 'Faculty')) }}
                            </x-filament::badge>
                        </p>
                    </div>
                    @if($user->primary_role && $user->primary_role !== $user->position)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Primary Role:</span>
                        <p class="font-medium">{{ ucwords(str_replace('_', ' ', $user->primary_role)) }}</p>
                    </div>
                    @endif
                    @if($user->job_title)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Job Title:</span>
                        <p class="font-medium">{{ $user->job_title }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- College Information -->
            <div class="space-y-3">
                <h3 class="font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                    College Information
                </h3>
                <div class="space-y-2 text-sm">
                    @if($college)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">College:</span>
                        <p class="font-medium">{{ $college->name }}</p>
                    </div>
                    @if($college->code)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">College Code:</span>
                        <p class="font-medium">{{ $college->code }}</p>
                    </div>
                    @endif
                    @else
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">College:</span>
                        <p class="text-gray-400 italic">Not assigned</p>
                    </div>
                    @endif

                    @if($department)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Department:</span>
                        <p class="font-medium">{{ $department->name }}</p>
                    </div>
                    @if($department->code)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Dept. Code:</span>
                        <p class="font-medium">{{ $department->code }}</p>
                    </div>
                    @endif
                    @else
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Department:</span>
                        <p class="text-gray-400 italic">Not assigned</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Account Status -->
            <div class="space-y-3">
                <h3 class="font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                    Account Status
                </h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Status:</span>
                        <p class="font-medium">
                            <x-filament::badge
                                :color="$user->is_active ? 'success' : 'danger'"
                            >
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </x-filament::badge>
                        </p>
                    </div>
                    @if($user->last_login_at)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Last Login:</span>
                        <p class="font-medium">{{ $user->last_login_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @endif
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Member Since:</span>
                        <p class="font-medium">{{ $user->created_at->format('M j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>