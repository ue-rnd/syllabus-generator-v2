<div class="space-y-4">
    @foreach($history as $entry)
    <div class="flex items-start space-x-2 p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div class="flex-shrink-0">
                @if($entry['action'] === 'submitted')
                    <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </div>
                @elseif($entry['action'] === 'approved')
                    <div class="w-6 h-6 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                @elseif($entry['action'] === 'rejected')
                    <div class="w-6 h-6 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                @else
                    <div class="w-6 h-6 bg-gray-100 dark:bg-gray-900 rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                @endif
            </div>
            
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $entry['user_name'] }}
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                            {{ ucwords(str_replace('_', ' ', $entry['user_role'])) }}
                        </span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ \Carbon\Carbon::parse($entry['timestamp'])->format('M j, Y g:i A') }}
                    </p>
                </div>
                
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    <span class="font-semibold capitalize text-{{ $entry['action'] === 'approved' ? 'green' : ($entry['action'] === 'rejected' ? 'red' : 'blue') }}-600">
                        {{ ucfirst($entry['action']) }}
                    </span>
                    the syllabus
                </p>
                
                @if(!empty($entry['comments']))
                    <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-md">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $entry['comments'] }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
