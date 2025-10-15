<div class="fi-section-content space-y-3">
    @foreach($history as $entry)
        <div class="fi-infolist-entry-wrapper">
            <div class="fi-infolist-entry">
                <div class="flex items-start gap-x-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <!-- Status Icon -->
                    <div class="flex-shrink-0 mt-0.5">
                        @if($entry['action'] === 'submitted')
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-50 ring-1 ring-primary-600/20 dark:bg-primary-400/10 dark:ring-primary-400/30">
                                <svg class="h-4 w-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </div>
                        @elseif($entry['action'] === 'approved')
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-success-50 ring-1 ring-success-600/20 dark:bg-success-400/10 dark:ring-success-400/30">
                                <svg class="h-4 w-4 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        @elseif($entry['action'] === 'rejected')
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-danger-50 ring-1 ring-danger-600/20 dark:bg-danger-400/10 dark:ring-danger-400/30">
                                <svg class="h-4 w-4 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        @else
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-50 ring-1 ring-gray-600/20 dark:bg-gray-800 dark:ring-gray-400/30">
                                <svg class="h-4 w-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="min-w-0 flex-1">
                        <!-- Header -->
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-x-3">
                                <h4 class="text-sm font-semibold text-gray-950 dark:text-white">
                                    {{ $entry['user_name'] }}
                                </h4>
                                <span class="fi-badge fi-badge-xs fi-color-gray">
                                    <span class="fi-badge-label">
                                        {{ ucwords(str_replace('_', ' ', $entry['user_role'])) }}
                                    </span>
                                </span>
                            </div>
                            <time class="text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($entry['timestamp'])->format('M j, Y g:i A') }}
                            </time>
                        </div>

                        <!-- Action -->
                        <div class="mt-2">
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <span class="font-semibold capitalize {{ $entry['action'] === 'approved' ? 'text-success-600 dark:text-success-400' : ($entry['action'] === 'rejected' ? 'text-danger-600 dark:text-danger-400' : 'text-primary-600 dark:text-primary-400') }}">
                                    {{ ucfirst($entry['action']) }}
                                </span>
                                <span class="ml-1">the syllabus</span>
                            </p>
                        </div>

                        <!-- Comments -->
                        @if(!empty($entry['comments']))
                            <div class="mt-3 rounded-lg bg-gray-50 p-3 dark:bg-white/5">
                                <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">
                                    "{{ $entry['comments'] }}"
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
