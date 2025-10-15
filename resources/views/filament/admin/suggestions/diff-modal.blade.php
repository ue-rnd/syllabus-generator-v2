<div class="space-y-6">
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2">Suggestion Details</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium">Field:</span> {{ $suggestion->change_description }}
            </div>
            <div>
                <span class="font-medium">Suggested by:</span> {{ $suggestion->suggestedBy->name }}
            </div>
            <div>
                <span class="font-medium">Date:</span> {{ $suggestion->created_at->format('M j, Y g:i A') }}
            </div>
            <div>
                <span class="font-medium">Status:</span>
                <span class="px-2 py-1 rounded text-xs font-medium
                    @if($suggestion->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif($suggestion->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @endif">
                    {{ $suggestion->status_label }}
                </span>
            </div>
        </div>
    </div>

    @if($suggestion->reason)
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
        <h4 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">Reason for Change</h4>
        <p class="text-blue-800 dark:text-blue-200">{{ $suggestion->reason }}</p>
    </div>
    @endif

    <div class="space-y-4">
        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
            <h4 class="font-semibold text-red-900 dark:text-red-100 mb-2">Current Value</h4>
            <div class="prose prose-sm max-w-none text-red-800 dark:text-red-200">
                @if($suggestion->current_value)
                    {!! $suggestion->current_value !!}
                @else
                    <em class="text-gray-500">(empty)</em>
                @endif
            </div>
        </div>

        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
            <h4 class="font-semibold text-green-900 dark:text-green-100 mb-2">Suggested Value</h4>
            <div class="prose prose-sm max-w-none text-green-800 dark:text-green-200">
                {!! $suggestion->suggested_value !!}
            </div>
        </div>
    </div>

    @if($suggestion->review_comments)
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h4 class="font-semibold mb-2">Review Comments</h4>
        <p class="text-gray-700 dark:text-gray-300">{{ $suggestion->review_comments }}</p>
        @if($suggestion->reviewedBy)
        <p class="text-sm text-gray-500 mt-2">
            Reviewed by {{ $suggestion->reviewedBy->name }} on {{ $suggestion->reviewed_at->format('M j, Y g:i A') }}
        </p>
        @endif
    </div>
    @endif
</div>