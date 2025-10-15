<div class="max-w-4xl mx-auto p-6">
    <!-- Back Button and Action Buttons -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('home') }}" 
           wire:navigate 
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-accent-desc bg-accent-foreground border border-accent-ghost-dark rounded-xl hover:bg-accent-ghost-dark hover:border-accent-ghost-dark focus:outline-none focus:ring-2 focus:ring-accent-main focus:border-transparent transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
        
        <div class="flex items-center gap-3">
            @if(!empty($canEdit) && $canEdit)
                <a href="{{ route('syllabus.edit', $syllabus) }}" wire:navigate class="px-3 py-1.5 text-sm font-medium bg-gray-800 text-white rounded hover:bg-gray-900">Edit</a>
            @endif

            <button type="button" wire:click="duplicateSyllabus" class="px-3 py-1.5 text-sm font-medium bg-blue-500 text-white rounded-xl hover:bg-blue-600 cursor-pointer transition-colors duration-200">Duplicate</button>
            @if(!empty($canSubmit) && $canSubmit)
                <button type="button" wire:click="confirmSubmitForApproval" class="px-3 py-1.5 text-sm font-medium bg-amber-500 text-white rounded hover:bg-amber-600 cursor-pointer transition-colors duration-200">Submit for Approval</button>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-accent-foreground rounded-xl shadow p-6 text-accent-text border border-accent-ghost-dark">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-accent-text">{{ $syllabus->name }}</h1>
            <span class="px-2 py-1 text-xs rounded-full {{ 
            $syllabus->status === 'approved' ? 'bg-accent-positive text-accent-positive-foreground' : 
            ($syllabus->status === 'under_review' ? 'bg-accent-warning text-accent-warning-foreground' : 
            ($syllabus->status === 'rejected' ? 'bg-accent-negative text-accent-negative-foreground' : 
            'bg-accent-desc text-accent-desc-foreground')) 
        }}">{{ ucfirst(str_replace('_', ' ', $syllabus->status)) }}</span>
        </div>

    <div class="text-sm text-accent-desc mb-6">
        <div>Course: {{ $syllabus->course->name ?? 'N/A' }} ({{ $syllabus->course->code ?? 'N/A' }})</div>
        <div>College: {{ $syllabus->course->college->name ?? 'N/A' }}</div>
        <div>Academic Year: {{ $syllabus->ay_start }} - {{ $syllabus->ay_end }}</div>
        <div>Last updated: {{ $syllabus->updated_at->diffForHumans() }}</div>
    </div>
    <div class="prose max-w-none">
        <h2>Description</h2>
        <div class="prose max-w-none p-4 border rounded bg-accent-ghost text-accent-desc border-accent-ghost-dark">
            {!! $syllabus->description ?? ($syllabus->course->description ?? '<span class="text-accent-desc">No description provided.</span>') !!}
        </div>

        <h2>Course Learning Outcomes</h2>
        <div class="p-4 border rounded bg-accent-foreground border-accent-ghost-dark text-accent-text">
            @if(!empty($syllabus->course_outcomes))
                <p class="text-sm text-accent-desc mb-2"><strong>Upon completion of the course, the learner will be able to:</strong></p>
                <ol class="list-decimal list-inside space-y-1">
                    @foreach($syllabus->course_outcomes as $outcome)
                        <li>
                            <span class="font-medium capitalize">{{ $outcome['verb'] ?? '' }}</span>
                            {!! $outcome['content'] ?? '' !!}
                        </li>
                    @endforeach
                </ol>
            @else
                <div class="text-sm text-accent-desc italic">No course outcomes defined.</div>
            @endif
        </div>

        <h2 class="mt-8">Pre-requisites</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm border rounded">
                <thead class="bg-accent-ghost-dark">
                    <tr>
                        <th class="px-3 py-2 text-accent-desc">Course Code</th>
                        <th class="px-3 py-2 text-accent-desc">Course Title</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($prerequisites))
                        @foreach($prerequisites as $p)
                            <tr class="border-t">
                                <td class="px-3 py-2 text-accent-text">{{ $p['code'] }}</td>
                                <td class="px-3 py-2 text-accent-text">{{ $p['name'] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="border-t">
                            <td class="px-3 py-2 text-accent-desc italic" colspan="2">No prerequisites</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <h2>Learning Matrix</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm border rounded">
                <thead class="bg-accent-ghost-dark">
                    <tr class="text-accent-desc">
                        <th class="px-3 py-2">Week</th>
                        <th class="px-3 py-2">Lec Hrs</th>
                        <th class="px-3 py-2">Lab Hrs</th>
                        <th class="px-3 py-2">Learning Outcomes</th>
                        <th class="px-3 py-2">Content</th>
                        <th class="px-3 py-2">Activity</th>
                        <th class="px-3 py-2">O</th>
                        <th class="px-3 py-2">A</th>
                        <th class="px-3 py-2">S</th>
                        <th class="px-3 py-2">Resource</th>
                        <th class="px-3 py-2">Assessment</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($syllabus->learning_matrix))
                        @foreach($syllabus->learning_matrix as $item)
                            @php
                                $range = $item['week_range'] ?? [];
                                $isRange = $range['is_range'] ?? false;
                                $start = intval($range['start'] ?? 0);
                                $end = $isRange ? intval($range['end'] ?? 0) : $start;
                                $weekMultiplier = ($start && $end && $end >= $start) ? ($end - $start + 1) : 1;
                                $activities = $item['learning_activities'] ?? [];
                                $rows = max(1, count($activities));
                            @endphp
                            @for($r=0;$r<$rows;$r++)
                                @php
                                    $activity = $activities[$r] ?? null;
                                    $modalities = $activity['modality'] ?? [];
                                    $hasOnsite = in_array('Onsite', $modalities ?? []) || in_array('onsite', $modalities ?? []);
                                    $hasAsync = in_array('Offsite Asynchronous', $modalities ?? []) || in_array('offsite_asynchronous', $modalities ?? []);
                                    $hasSync = in_array('Offsite Synchronous', $modalities ?? []) || in_array('offsite_synchronous', $modalities ?? []);
                                    $assessments = $item['assessments'] ?? [];
                                @endphp
                                <tr class="border-t align-top">
                                    @if($r===0)
                                        <td class="px-3 py-2" rowspan="{{ $rows }}">
                                            @if($start && $end && $end > $start)
                                                {{ $start }}-{{ $end }}
                                            @elseif($start)
                                                {{ $start }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-3 py-2" rowspan="{{ $rows }}">{{ ($syllabus->default_lecture_hours ?? 0) * $weekMultiplier }}</td>
                                        <td class="px-3 py-2" rowspan="{{ $rows }}">{{ ($syllabus->default_laboratory_hours ?? 0) * $weekMultiplier }}</td>
                                        <td class="px-3 py-2" rowspan="{{ $rows }}">
                                            @if(!empty($item['learning_outcomes']))
                                                <ul class="list-disc list-inside space-y-1">
                                                    @foreach($item['learning_outcomes'] as $lo)
                                                        <li>
                                                            <span class="font-medium capitalize">{{ $lo['verb'] ?? '' }}</span> {!! $lo['content'] ?? '' !!}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-gray-500 italic">No outcomes</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2" rowspan="{{ $rows }}">{!! $item['content'] ?? '<span class=\"text-accent-desc italic\">No content</span>' !!}</td>
                                    @endif
                                    <td class="px-3 py-2">{!! $activity['description'] ?? '<span class=\"text-accent-desc italic\">—</span>' !!}</td>
                                    <td class="px-3 py-2">{!! !empty($hasOnsite) ? '&#10003;' : '' !!}</td>
                                    <td class="px-3 py-2">{!! !empty($hasAsync) ? '&#10003;' : '' !!}</td>
                                    <td class="px-3 py-2">{!! !empty($hasSync) ? '&#10003;' : '' !!}</td>
                                    <td class="px-3 py-2">{!! $activity['reference'] ?? '' !!}</td>
                                    @if($r===0)
                                        <td class="px-3 py-2 text-accent-text" rowspan="{{ $rows }}">
                                            @if(!empty($assessments))
                                                @if(is_array($assessments))
                                                    <ul class="list-disc list-inside space-y-1">
                                                        @foreach($assessments as $a)
                                                            <li>{{ \App\Constants\SyllabusConstants::getAssessmentTypeOptions()[$a] ?? $a }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <div class="prose prose-sm max-w-none text-accent-text">{!! $assessments !!}</div>
                                                @endif
                                            @else
                                                <span class="text-accent-desc italic">No assessment</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endfor
                        @endforeach
                        @else
                        <tr class="border-t">
                            <td class="px-3 py-4 text-accent-desc italic" colspan="11">No data available</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <h2 class="mt-8">References</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm border rounded">
                <tbody>
                    <tr class="border-t">
                        <th class="px-3 py-2 w-56 bg-accent-ghost">Adaptive Digital Solutions</th>
                        <td class="px-3 py-2">{!! $syllabus->adaptive_digital_solutions ?? '<span class="text-accent-desc">Not specified</span>' !!}</td>
                    </tr>
                    <tr class="border-t">
                        <th class="px-3 py-2 w-56 bg-accent-ghost">Textbook</th>
                        <td class="px-3 py-2">{!! $syllabus->textbook_references ?? '<span class="text-accent-desc">Not specified</span>' !!}</td>
                    </tr>
                    <tr class="border-t">
                        <th class="px-3 py-2 w-56 bg-accent-ghost">Online References</th>
                        <td class="px-3 py-2">{!! $syllabus->online_references ?? '<span class="text-accent-desc">Not specified</span>' !!}</td>
                    </tr>
                    <tr class="border-t">
                        <th class="px-3 py-2 w-56 bg-accent-ghost">Others</th>
                        <td class="px-3 py-2">{!! $syllabus->other_references ?? '<span class="text-accent-desc">Not specified</span>' !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2 class="mt-8">Policies & Grading</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm border rounded">
                <tbody>
                    <tr class="border-t">
                        <th class="px-3 py-2 w-56 bg-accent-ghost">Grading System</th>
                        <td class="px-3 py-2">{!! $syllabus->grading_system ?? '<span class="text-accent-desc">Standard University grading system applies.</span>' !!}</td>
                    </tr>
                    <tr class="border-t">
                        <th class="px-3 py-2 w-56 bg-accent-ghost">Classroom Policies</th>
                        <td class="px-3 py-2">{!! $syllabus->classroom_policies ?? '<span class="text-accent-desc">Standard classroom policies apply.</span>' !!}</td>
                    </tr>
                    <tr class="border-t">
                        <th class="px-3 py-2 w-56 bg-accent-ghost">Consultation Hours</th>
                        <td class="px-3 py-2">{!! $syllabus->consultation_hours ?? '<span class="text-accent-desc">By appointment.</span>' !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2 class="mt-8">Signatories</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 border rounded bg-accent-foreground text-accent-text border-accent-ghost-dark">
                <div class="text-xs text-accent-desc mb-2 font-medium">PREPARED BY</div>
                <div class="font-semibold">{{ $syllabus->principalPreparer->full_name ?? $syllabus->principalPreparer->name ?? '[Principal Preparer]' }}</div>
                <div class="text-sm text-accent-desc">Principal Preparer</div>
            </div>
            <div class="p-4 border rounded bg-accent-foreground text-accent-text border-accent-ghost-dark">
                <div class="text-xs text-accent-desc mb-2 font-medium">VERIFIED BY</div>
                <div class="font-semibold">{{ $syllabus->reviewer->full_name ?? $syllabus->reviewer->name ?? '[Department Chair]' }}</div>
                <div class="text-sm text-accent-desc">Department Chair</div>
            </div>
            <div class="p-4 border rounded bg-accent-foreground text-accent-text border-accent-ghost-dark">
                <div class="text-xs text-accent-desc mb-2 font-medium">RECOMMENDING APPROVAL</div>
                <div class="font-semibold">{{ $syllabus->recommendingApprover->full_name ?? $syllabus->recommendingApprover->name ?? '[Associate Dean]' }}</div>
                <div class="text-sm text-accent-desc">Associate Dean</div>
            </div>
            <div class="p-4 border rounded bg-accent-foreground text-accent-text md:col-span-1 border-accent-ghost-dark">
                <div class="text-xs text-accent-desc mb-2 font-medium">APPROVED BY</div>
                <div class="font-semibold">{{ $syllabus->approver->full_name ?? $syllabus->approver->name ?? '[Dean]' }}</div>
                <div class="text-sm text-accent-desc">Dean</div>
            </div>
        </div>
    </div>
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
                        class="px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-md hover:bg-amber-700 cursor-pointer transition-colors duration-200">
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


