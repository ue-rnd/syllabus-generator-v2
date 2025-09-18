<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900">{{ $syllabus->name }}</h1>
        <div class="flex items-center gap-3">
            @if(!empty($canEdit) && $canEdit)
                <a href="{{ route('syllabus.edit', $syllabus) }}" wire:navigate class="px-3 py-1.5 text-sm font-medium bg-gray-800 text-white rounded hover:bg-gray-900">Edit</a>
            @endif
            @if(!empty($canSubmit) && $canSubmit)
                <button type="button" wire:click="confirmSubmitForApproval" class="px-3 py-1.5 text-sm font-medium bg-amber-500 text-white rounded hover:bg-amber-600">Submit for Approval</button>
            @endif
            <span class="px-2 py-1 text-xs rounded-full {{ 
            $syllabus->status === 'approved' ? 'bg-green-100 text-green-800' : 
            ($syllabus->status === 'under_review' ? 'bg-yellow-100 text-yellow-800' : 
            ($syllabus->status === 'rejected' ? 'bg-red-100 text-red-800' : 
            'bg-gray-100 text-gray-800')) 
        }}">{{ ucfirst(str_replace('_', ' ', $syllabus->status)) }}</span>
        </div>
    </div>

    <div class="text-sm text-gray-600 mb-6">
        <div>Course: {{ $syllabus->course->name ?? 'N/A' }} ({{ $syllabus->course->code ?? 'N/A' }})</div>
        <div>College: {{ $syllabus->course->college->name ?? 'N/A' }}</div>
        <div>Last updated: {{ $syllabus->updated_at->diffForHumans() }}</div>
    </div>

    <div class="prose max-w-none">
        @if($syllabus->description)
            <h2>Description</h2>
            <p>{!! $syllabus->description !!}</p>
        @endif

        @if(!empty($syllabus->course_outcomes))
            <h2>Course Outcomes</h2>
            <ul>
                @foreach($syllabus->course_outcomes as $outcome)
                    <li><strong>{{ ucfirst($outcome['verb'] ?? '') }}:</strong> {!! $outcome['content'] ?? '' !!}</li>
                @endforeach
            </ul>
        @endif

        @if(!empty($syllabus->learning_matrix))
            <h2>Learning Matrix</h2>
            <div class="space-y-4">
                @foreach($syllabus->learning_matrix as $item)
                    <div class="p-4 border rounded">
                        <div class="text-sm text-gray-600 mb-2">
                            @php
                                $range = $item['week_range'] ?? [];
                                $isRange = $range['is_range'] ?? false;
                                $start = $range['start'] ?? null;
                                $end = $range['end'] ?? null;
                            @endphp
                            @if($isRange && $start && $end)
                                Weeks {{ $start }}-{{ $end }}
                            @elseif($start)
                                Week {{ $start }}
                            @endif
                        </div>
                        <div class="prose max-w-none">{!! $item['content'] ?? '' !!}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
        
    <!-- Confirm Submit Modal -->
    <div x-data="{ open: @entangle('showConfirmModal') }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-black/40" x-on:click="open = false"></div>
        <div class="relative bg-white rounded-lg shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-medium text-gray-900">Submit Syllabus for Approval</h2>
            <p class="mt-2 text-sm text-gray-600">
                Are you sure you want to submit this syllabus for approval? You will not be able to edit it until the review process is complete.
            </p>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button"
                        x-on:click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button type="button"
                        x-on:click="open = false; $wire.performSubmitForApproval()"
                        class="px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-md hover:bg-amber-700">
                    Submit
                </button>
            </div>
        </div>
    </div>

    <!-- Result Modal -->
    <div x-data="{ open: @entangle('showResultModal') }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-black/40" x-on:click="open = false"></div>
        <div class="relative bg-white rounded-lg shadow-lg w-full max-w-lg mx-4 p-6">
            <h2 class="text-lg font-medium" :class="@js($resultSuccess ? 'text-green-700' : 'text-red-700')">
                {{ $resultSuccess ? 'Submission Successful' : 'Submission Failed' }}
            </h2>
            <p class="mt-2 text-sm text-gray-700">{{ $resultMessage }}</p>

            @if(!$resultSuccess && !empty($resultErrors))
                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded">
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1 max-h-56 overflow-auto">
                        @foreach($resultErrors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="open = false" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-md hover:bg-gray-900">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>


