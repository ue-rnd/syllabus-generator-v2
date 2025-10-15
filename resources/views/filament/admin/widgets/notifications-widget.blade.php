<x-filament-widgets::widget>
    <x-filament::section class="h-full">
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-bell class="h-5 w-5" />
                Recent Notifications
            </div>
        </x-slot>

        @if($hasNotifications)
            {{-- Container fills available height and allows the list to scroll --}}
            <div class="flex flex-col h-[300px]"> {{-- match height visually to user profile widget --}}
                <div class="overflow-y-auto space-y-3 px-0">
                    @foreach($notifications as $notification)
                    <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                        <div class="flex-shrink-0">
                            @switch($notification->type)
                                @case('App\\Notifications\\SyllabusStatusUpdate')
                                    <x-heroicon-o-document-text class="h-5 w-5 text-blue-500" />
                                    @break
                                @case('App\\Notifications\\SyllabusApproved')
                                    <x-heroicon-o-check-circle class="h-5 w-5 text-green-500" />
                                    @break
                                @case('App\\Notifications\\SyllabusRejected')
                                    <x-heroicon-o-x-circle class="h-5 w-5 text-red-500" />
                                    @break
                                @default
                                    <x-heroicon-o-information-circle class="h-5 w-5 text-gray-500" />
                            @endswitch
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $notification->data['message'] ?? 'No message content' }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>

                        @if($notification->data['action_url'] ?? null)
                            <div class="flex-shrink-0">
                                <a
                                    href="{{ $notification->data['action_url'] }}"
                                    class="text-primary-600 hover:text-primary-500 text-sm font-medium"
                                >
                                    View
                                </a>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                {{-- footer --}}
                @if($notifications->count() >= 10)
                    <div class="mt-5 text-center">
                        @if(\Illuminate\Support\Facades\Route::has('filament.admin.pages.notifications'))
                            <x-filament::button
                                outlined
                                size="sm"
                                href="{{ route('filament.admin.pages.notifications') }}"
                            >
                                View All Notifications
                            </x-filament::button>
                        @else
                            <x-filament::button
                                outlined
                                size="sm"
                                disabled
                            >
                                View All Notifications
                            </x-filament::button>
                        @endif
                    </div>
                @endif
            </div>

            {{-- duplicate footer removed --}}
        @else
            <div class="text-center py-6 h-[300px] flex flex-col items-center justify-center"> {{-- keep empty state same height --}}
                <x-heroicon-o-bell-slash class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No notifications</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You're all caught up!</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>