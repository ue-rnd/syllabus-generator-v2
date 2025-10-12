<div class="max-w-4xl mx-auto p-6">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('home') }}" 
           wire:navigate 
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ (isset($isEdit) && $isEdit) ? 'Edit Syllabus' : 'Create New Syllabus' }}</h1>
            <span class="text-sm text-gray-500">Step {{ $currentStep }} of {{ $totalSteps }}</span>
        </div>
        
        <!-- Progress Steps -->
        <div class="relative">
            <!-- Progress line background -->
            <div class="absolute top-4 left-4 right-4 h-0.5 bg-gray-200"></div>
            
            <!-- Progress line fill -->
            <div class="absolute top-4 left-4 h-0.5 bg-red-600 transition-all duration-300" 
                 style="width: calc({{ (($currentStep - 1) / ($totalSteps - 1)) * 100 }}% - {{ ($currentStep - 1) * 8 }}px);"></div>
            
            <!-- Step circles -->
            <div class="relative flex justify-between items-start">
                @for($i = 1; $i <= $totalSteps; $i++)
                    <div class="flex flex-col items-center relative">
                        <!-- Circle -->
                        <div class="relative z-10 flex items-center justify-center w-8 h-8 rounded-full border-2 {{ $i <= $currentStep ? 'bg-red-600 border-red-600 text-white' : 'bg-white border-gray-300 text-gray-400' }} transition-colors duration-200">
                            @if($i < $currentStep)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                {{ $i }}
                            @endif
                        </div>
                        
                        <!-- Step label -->
                        <span class="mt-2 text-xs font-medium text-center leading-tight {{ $i <= $currentStep ? 'text-red-600' : 'text-gray-400' }}">
                            @switch($i)
                                @case(1) Basic<br>Info @break
                                @case(2) Program<br>Outcomes @break
                                @case(3) Course<br>Outcomes @break
                                @case(4) Learning<br>Matrix @break
                                @case(5) References @break
                                @case(6) Policies @break
                                @case(7) Approval @break
                            @endswitch
                        </span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="bg-white rounded-xl shadow-md p-6">
        @if($currentStep == 1)
            <!-- Step 1: Basic Information and Curricular Details -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information and Curricular Details</h2>
                    <p class="text-sm text-gray-600 mb-6">Provide the basic information about the syllabus and academic year details.</p>
                </div>

                <!-- Curricular Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-xl">
                    <h3 class="col-span-full text-md font-medium text-gray-900 mb-3">Academic Year and Schedule</h3>
                    
                    <div>
                        <label for="ay_start" class="block text-sm font-medium text-gray-700 mb-1">Academic Year Start</label>
                        <input type="number" 
                               wire:model="ay_start" 
                               id="ay_start"
                               class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('ay_start') border-red-500 @enderror">
                        @error('ay_start') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="ay_end" class="block text-sm font-medium text-gray-700 mb-1">Academic Year End</label>
                        <input type="number" 
                               wire:model="ay_end" 
                               id="ay_end"
                               class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('ay_end') border-red-500 @enderror">
                        @error('ay_end') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div></div> <!-- Empty div for grid alignment -->
                    
                    <div>
                        <label for="week_prelim" class="block text-sm font-medium text-gray-700 mb-1">Prelims Exam Week</label>
                        <input type="number" 
                               wire:model="week_prelim" 
                               id="week_prelim"
                               min="1" max="20"
                               class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('week_prelim') border-red-500 @enderror">
                        @error('week_prelim') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="week_midterm" class="block text-sm font-medium text-gray-700 mb-1">Midterms Exam Week</label>
                        <input type="number" 
                               wire:model="week_midterm" 
                               id="week_midterm"
                               min="1" max="20"
                               class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('week_midterm') border-red-500 @enderror">
                        @error('week_midterm') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="week_final" class="block text-sm font-medium text-gray-700 mb-1">Finals Exam Week</label>
                        <input type="number" 
                               wire:model="week_final" 
                               id="week_final"
                               min="1" max="20"
                               class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('week_final') border-red-500 @enderror">
                        @error('week_final') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-md font-medium text-gray-900">Course Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Course <span class="text-red-500">*</span></label>
                            <select wire:model.live="course_id" 
                                    id="course_id"
                                    class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('course_id') border-red-500 @enderror">
                                <option value="">Select a course...</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->code }})</option>
                                @endforeach
                            </select>
                            @error('course_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Syllabus Name <span class="text-red-500">*</span></label>
                            <input type="text" 
                                   wire:model="name" 
                                   id="name"
                                   class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('name') border-red-500 @enderror">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Auto-filled from course)</label>
                        <div class="w-full px-3 py-2 border rounded-xl bg-gray-50 text-gray-700 min-h-[100px]">
                            {!! $description ?: 'No description available for this course.' !!}
                        </div>
                        <input type="hidden" wire:model="description" />
                    </div>
                </div>

            </div>
        @elseif($currentStep == 2)
            <!-- Step 2: Program Outcomes -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Program Outcomes</h2>
                    <p class="text-sm text-gray-600 mb-6">Identify the program outcomes that this course fulfills and specify how each outcome is addressed.</p>
                    
                    @error('program_outcomes')
                        <div class="p-4 bg-red-50 border border-red-200 rounded-xl mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Validation Error</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>{{ $message }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @enderror
                </div>

                @if(empty($program_outcomes))
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.725-1.36 3.49 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">No Program Outcomes Available</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Please select a course first to load the program outcomes for that course.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($program_outcomes as $index => $outcome)
                            <div class="p-4 border border-gray-200 rounded-xl bg-gray-50">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Program Outcome {{ $index + 1 }}
                                    </label>
                                    <div class="p-3 bg-white border border-gray-300 rounded-xl text-sm text-gray-700">
                                        {{ $outcome['content'] }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        How is this outcome addressed? <span class="text-red-500">*</span>
                                    </label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   wire:model.live="program_outcomes.{{ $index }}.addressed" 
                                                   value="introduced"
                                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                            <span class="ml-2 text-sm text-gray-700">Introduced</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   wire:model.live="program_outcomes.{{ $index }}.addressed" 
                                                   value="enhanced"
                                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                            <span class="ml-2 text-sm text-gray-700">Enhanced</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   wire:model.live="program_outcomes.{{ $index }}.addressed" 
                                                   value="demonstrated"
                                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                            <span class="ml-2 text-sm text-gray-700">Demonstrated</span>
                                        </label>
                                    </div>
                                    @error("program_outcomes.{$index}.addressed") 
                                        <span class="text-red-500 text-xs mt-1 block">
                                            Please select how this program outcome is addressed.
                                        </span> 
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @elseif($currentStep == 3)
            <!-- Step 3: Course Outcomes -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Outcomes</h2>
                    <p class="text-sm text-gray-600 mb-6">Define the learning outcomes for this course. Each outcome should start with an action verb and clearly describe what students will be able to do.</p>
                    
                    @error('course_outcomes')
                        <div class="p-4 bg-red-50 border border-red-200 rounded-xl mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Validation Error</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>{{ $message }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @enderror
                </div>

                <div class="space-y-4">
                    @forelse($course_outcomes as $index => $outcome)
                        <div class="p-4 border border-gray-200 rounded-xl bg-gray-50" wire:key="course-outcome-{{ $index }}">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-sm font-medium text-gray-900">Course Outcome {{ $index + 1 }}</h4>
                                <button type="button" 
                                        wire:click="removeCourseOutcome({{ $index }})"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Remove
                                </button>
                            </div>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Action Verb <span class="text-red-500">*</span>
                                    </label>
                                    <x-select 
                                        wire:model.live="course_outcomes.{{ $index }}.verb"
                                        :options="array_values($actionVerbs)"
                                        placeholder="Select an action verb..."
                                        searchable
                                        class="@error('course_outcomes.'.$index.'.verb') border-red-500 @enderror" />
                                    @error('course_outcomes.'.$index.'.verb') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Outcome Description <span class="text-red-500">*</span>
                                    </label>
                                    <x-quill-editor 
                                        wire-model="course_outcomes.{{ $index }}.content"
                                        placeholder="Complete the outcome statement..."
                                        toolbar="basic"
                                        :rows="3"
                                        :error-class="'@error(\'course_outcomes.'.$index.'.content\') border-red-500 @enderror'"
                                        :initial-content="$outcome['content'] ?? ''"
                                        wire:key="quill-course-outcome-{{ $index }}" />
                                    @error('course_outcomes.'.$index.'.content') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                            
                            @if($outcome['verb'] && $outcome['content'] && config('app.debug'))
                                <div class="mt-3 p-3 bg-white border border-gray-200 rounded-xl">
                                    <h5 class="text-sm font-medium text-gray-700 mb-1">Preview:</h5>
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium capitalize">{{ $outcome['verb'] }}</span> {!! strip_tags($outcome['content'], '<strong><em><u><s><a><ul><ol><li>') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No course outcomes</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by adding your first course outcome.</p>
                        </div>
                    @endforelse
                    
                    <div class="flex justify-center">
                        <button type="button" 
                                wire:click="addCourseOutcome"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Course Outcome
                        </button>
                    </div>
                </div>
            </div>
        @elseif($currentStep == 4)
            <!-- Step 4: Learning Matrix -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Learning Matrix</h2>
                    <p class="text-sm text-gray-600 mb-6">Define the learning activities, outcomes, and assessments for each week or week range.</p>
                    
                    @error('learning_matrix')
                        <div class="p-4 bg-red-50 border border-red-200 rounded-xl mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Validation Error</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>{{ $message }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @enderror
                </div>

                <!-- Default Hours -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-xl">
                    <h3 class="col-span-full text-md font-medium text-gray-900 mb-3">Default Hours per Week</h3>
                    
                    <div>
                        <label for="default_lecture_hours" class="block text-sm font-medium text-gray-700 mb-1">Default Lecture Hours per Week</label>
                        <input type="number" 
                               wire:model="default_lecture_hours" 
                               id="default_lecture_hours"
                               step="0.5"
                               min="0"
                               class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('default_lecture_hours') border-red-500 @enderror">
                        @error('default_lecture_hours') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="default_laboratory_hours" class="block text-sm font-medium text-gray-700 mb-1">Default Laboratory Hours per Week</label>
                        <input type="number" 
                               wire:model="default_laboratory_hours" 
                               id="default_laboratory_hours"
                               step="0.5"
                               min="0"
                               class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('default_laboratory_hours') border-red-500 @enderror">
                        @error('default_laboratory_hours') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Learning Matrix Items -->
                <div class="space-y-4">
                    @forelse($learning_matrix as $index => $item)
                        <div class="p-4 border border-gray-200 rounded-xl bg-gray-50" wire:key="learning-matrix-{{ $index }}">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-sm font-medium text-gray-900">
                                    Learning Matrix Item {{ $index + 1 }}
                                    @if($item['week_range']['is_range'] && $item['week_range']['start'] && $item['week_range']['end'])
                                        - Weeks {{ $item['week_range']['start'] }}-{{ $item['week_range']['end'] }}
                                    @elseif($item['week_range']['start'])
                                        - Week {{ $item['week_range']['start'] }}
                                    @endif
                                </h4>
                                <button type="button" 
                                        wire:click="removeLearningMatrixItem({{ $index }})"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Remove
                                </button>
                            </div>
                            
                            <!-- Week Range -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           wire:model.live="learning_matrix.{{ $index }}.week_range.is_range"
                                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                    <label class="ml-2 text-sm text-gray-700">Week Range</label>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ $item['week_range']['is_range'] ? 'Week Start' : 'Week' }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           wire:model.live="learning_matrix.{{ $index }}.week_range.start"
                                           min="1" max="20"
                                           class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('learning_matrix.'.$index.'.week_range.start') border-red-500 @enderror">
                                    @error('learning_matrix.'.$index.'.week_range.start') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>
                                
                                @if($item['week_range']['is_range'])
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Week End <span class="text-red-500">*</span></label>
                                        <input type="number" 
                                               wire:model.live="learning_matrix.{{ $index }}.week_range.end"
                                               min="{{ $item['week_range']['start'] ?? 1 }}" max="20"
                                               class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('learning_matrix.'.$index.'.week_range.end') border-red-500 @enderror">
                                        @error('learning_matrix.'.$index.'.week_range.end') 
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Content -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Content <span class="text-red-500">*</span></label>
                                <x-quill-editor 
                                    wire-model="learning_matrix.{{ $index }}.content"
                                    placeholder="Add item content..."
                                    toolbar="basic"
                                    :rows="3"
                                    :error-class="'@error(\'learning_matrix.'.$index.'.content\') border-red-500 @enderror'"
                                    :initial-content="$item['content'] ?? ''"
                                    wire:key="quill-matrix-content-{{ $index }}" />
                                @error('learning_matrix.'.$index.'.content') 
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                @enderror
                            </div>
                            
                            <!-- Learning Outcomes -->
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">Learning Outcomes for this Week/Range</h5>
                                    <button type="button" 
                                            wire:click="addLearningOutcome({{ $index }})"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        + Add Learning Outcome
                                    </button>
                                </div>
                                
                                @forelse($item['learning_outcomes'] as $outcomeIndex => $outcome)
                                    <div class="p-3 bg-white border border-gray-200 rounded-xl mb-2" wire:key="learning-outcome-{{ $index }}-{{ $outcomeIndex }}">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs text-gray-500">Outcome {{ $outcomeIndex + 1 }}</span>
                                            <button type="button" 
                                                    wire:click="removeLearningOutcome({{ $index }}, {{ $outcomeIndex }})"
                                                    class="text-red-600 hover:text-red-800 text-xs">
                                                Remove
                                            </button>
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <div>
                                                <x-select 
                                                    wire:model.live="learning_matrix.{{ $index }}.learning_outcomes.{{ $outcomeIndex }}.verb"
                                                    :options="array_values($actionVerbs)"
                                                    placeholder="Select verb..."
                                                    searchable
                                                    class="text-sm" />
                                            </div>
                                            <div>
                                                <x-quill-editor 
                                                    wire-model="learning_matrix.{{ $index }}.learning_outcomes.{{ $outcomeIndex }}.content"
                                                    placeholder="Outcome description..."
                                                    toolbar="basic"
                                                    :rows="2"
                                                    :initial-content="$outcome['content'] ?? ''"
                                                    wire:key="quill-learning-outcome-{{ $index }}-{{ $outcomeIndex }}" />
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 italic">No learning outcomes added yet.</p>
                                @endforelse
                            </div>
                            
                            <!-- Learning Activities -->
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h5 class="text-sm font-medium text-gray-700">Learning Activities</h5>
                                    <button type="button" 
                                            wire:click="addLearningActivity({{ $index }})"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        + Add Learning Activity
                                    </button>
                                </div>
                                
                                @forelse($item['learning_activities'] as $activityIndex => $activity)
                                    <div class="p-3 bg-white border border-gray-200 rounded-xl mb-2" wire:key="learning-activity-{{ $index }}-{{ $activityIndex }}">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs text-gray-500">Activity {{ $activityIndex + 1 }}</span>
                                            <button type="button" 
                                                    wire:click="removeLearningActivity({{ $index }}, {{ $activityIndex }})"
                                                    class="text-red-600 hover:text-red-800 text-xs">
                                                Remove
                                            </button>
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <x-quill-editor 
                                                wire-model="learning_matrix.{{ $index }}.learning_activities.{{ $activityIndex }}.description"
                                                placeholder="Activity description..."
                                                toolbar="basic"
                                                :rows="2"
                                                :initial-content="$activity['description'] ?? ''"
                                                wire:key="quill-activity-desc-{{ $index }}-{{ $activityIndex }}" />
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">Modality</label>
                                                    <x-select 
                                                        wire:model.live="learning_matrix.{{ $index }}.learning_activities.{{ $activityIndex }}.modality"
                                                        :options="array_values($learningModalities)"
                                                        placeholder="Select modalities..."
                                                        multiselect
                                                        class="text-sm" />
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">Reference/Resource</label>
                                                    <x-quill-editor 
                                                        wire-model="learning_matrix.{{ $index }}.learning_activities.{{ $activityIndex }}.reference"
                                                        placeholder="Reference details..."
                                                        toolbar="basic"
                                                        :rows="2"
                                                        :initial-content="$activity['reference'] ?? ''"
                                                        wire:key="quill-activity-ref-{{ $index }}-{{ $activityIndex }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 italic">No learning activities added yet.</p>
                                @endforelse
                            </div>
                            
                            <!-- Assessments -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Weekly Assessments</label>
                                <x-rich-editor
                                    wire:model.live="learning_matrix.{{ $index }}.assessments"
                                    placeholder="Describe the assessments for this week..."
                                    class="text-sm" />
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No learning matrix items</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by adding your first learning matrix item.</p>
                        </div>
                    @endforelse
                    
                    <div class="flex justify-center">
                        <button type="button" 
                                wire:click="addLearningMatrixItem"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Learning Matrix Item
                        </button>
                    </div>
                </div>
            </div>
        @elseif($currentStep == 5)
            <!-- Step 5: References & Resources -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">References & Resources</h2>
                    <p class="text-sm text-gray-600 mb-6">Add references and resources for this course.</p>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Textbook References</label>
                        <x-quill-editor 
                            wire:key="quill-textbook-references"
                            wire-model="textbook_references"
                            placeholder="Add textbook references..."
                            toolbar="full"
                            :rows="4"
                            :initial-content="$textbook_references" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Adaptive Digital Solutions</label>
                        <x-quill-editor 
                            wire:key="quill-adaptive-digital-solutions"
                            wire-model="adaptive_digital_solutions"
                            placeholder="Add digital solutions..."
                            toolbar="full"
                            :rows="4"
                            :initial-content="$adaptive_digital_solutions" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Online References</label>
                        <x-quill-editor 
                            wire:key="quill-online-references"
                            wire-model="online_references"
                            placeholder="Add online references..."
                            toolbar="full"
                            :rows="4"
                            :initial-content="$online_references" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Other References</label>
                        <x-quill-editor 
                            wire:key="quill-other-references"
                            wire-model="other_references"
                            placeholder="Add other references..."
                            toolbar="full"
                            :rows="4"
                            :initial-content="$other_references" />
                    </div>
                </div>
            </div>
        @elseif($currentStep == 6)
            <!-- Step 6: Policies & Grading -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Policies & Grading</h2>
                    <p class="text-sm text-gray-600 mb-6">Define grading system and classroom policies.</p>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grading System</label>
                        <x-quill-editor 
                            wire:key="quill-grading-system"
                            wire-model="grading_system"
                            placeholder="Define grading system..."
                            toolbar="full"
                            :rows="6" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Classroom Policies</label>
                        <x-quill-editor 
                            wire:key="quill-classroom-policies"
                            wire-model="classroom_policies"
                            placeholder="Define classroom policies..."
                            toolbar="full"
                            :rows="6" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Consultation Hours</label>
                        <x-quill-editor 
                            wire:key="quill-consultation-hours"
                            wire-model="consultation_hours"
                            placeholder="Define consultation hours..."
                            toolbar="basic"
                            :rows="3" />
                    </div>
                </div>
            </div>
        @elseif($currentStep == 7)
            <!-- Step 7: Approval & Signers -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Approval & Signers</h2>
                    <p class="text-sm text-gray-600 mb-6">Assign approvers and signers for this syllabus.</p>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Principal Prepared By</label>
                        <div class="w-full px-3 py-2 border border-gray-300 rounded-xl bg-gray-50 text-gray-700">
                            {{ $principalPreparerName ?? 'Auto-selected: current user' }}
                        </div>
                        <input type="hidden" wire:model="principal_prepared_by">
                        @error('principal_prepared_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Verified By (Department Chair)</label>
                        <div class="w-full px-3 py-2 border border-gray-300 rounded-xl bg-gray-50 text-gray-700">
                            {{ $reviewerName ?? 'Auto-selected from department' }}
                        </div>
                        <input type="hidden" wire:model="reviewed_by">
                        @error('reviewed_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recommending Approval (Associate Dean)</label>
                        <div class="w-full px-3 py-2 border border-gray-300 rounded-xl bg-gray-50 text-gray-700">
                            {{ $recommendingName ?? 'Auto-selected from college' }}
                        </div>
                        <input type="hidden" wire:model="recommending_approval">
                        @error('recommending_approval') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Approved By (Dean)</label>
                        <div class="w-full px-3 py-2 border border-gray-300 rounded-xl bg-gray-50 text-gray-700">
                            {{ $approverName ?? 'Auto-selected from college' }}
                        </div>
                        <input type="hidden" wire:model="approved_by">
                        @error('approved_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-6">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">Co-editors / Additional Preparers</label>
                            <button type="button"
                                    wire:click="addPreparer"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                + Add Preparer
                            </button>
                        </div>

                        @forelse($prepared_by as $index => $preparer)
                            <div class="p-4 border border-gray-200 rounded-xl bg-gray-50 mb-3" wire:key="preparer-{{ $index }}">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="text-xs text-gray-500">No. {{ $index + 1 }}</div>
                                    <button type="button"
                                            wire:click="removePreparer({{ $index }})"
                                            class="text-red-600 hover:text-red-800 text-xs font-medium">
                                        Remove
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Faculty Member <span class="text-red-500">*</span></label>
                                        <x-select 
                                            wire:model.live="prepared_by.{{ $index }}.user_id"
                                            :options="$facultyOptions"
                                            placeholder="Select a faculty member..."
                                            searchable
                                        />
                                        @error('prepared_by.'.$index.'.user_id') 
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Role/Position</label>
                                        <input type="text"
                                               wire:model="prepared_by.{{ $index }}.role"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                               placeholder="e.g., Faculty, Distinguished Faculty, Library Officer">
                                        @error('prepared_by.'.$index.'.role') 
                                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <x-quill-editor 
                                        wire-model="prepared_by.{{ $index }}.description"
                                        placeholder="Additional details about their contribution"
                                        toolbar="basic"
                                        :rows="3"
                                        :initial-content="$preparer['description'] ?? ''"
                                        wire:key="quill-preparer-desc-{{ $index }}" />
                                    @error('prepared_by.'.$index.'.description') 
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 italic">No co-editors added yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Message Display -->
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
            <button type="button" 
                    wire:click="previousStep"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    @if($currentStep == 1) disabled @endif>
                Previous
            </button>
            
            <div class="flex space-x-2">
                @for($i = 1; $i <= $totalSteps; $i++)
                    @php
                        $isCurrentStep = $i == $currentStep;
                        $isValidated = in_array($i, $validatedSteps);
                        $isAccessible = $i <= $currentStep || $isValidated;
                        $isCompleted = $i < $currentStep;
                        
                        $buttonClasses = 'w-8 h-8 text-xs font-medium rounded-full transition-colors duration-200 ';
                        
                        if ($isCurrentStep) {
                            $buttonClasses .= 'bg-red-600 text-white';
                        } elseif ($isCompleted) {
                            $buttonClasses .= 'bg-red-600 text-white';
                        } elseif ($isAccessible) {
                            $buttonClasses .= 'bg-gray-200 text-gray-500 hover:bg-gray-300';
                        } else {
                            $buttonClasses .= 'bg-gray-100 text-gray-400 cursor-not-allowed';
                        }
                    @endphp
                    <button type="button" 
                            wire:click="goToStep({{ $i }})"
                            class="{{ $buttonClasses }} flex items-center justify-center"
                            @if(!$isAccessible) disabled @endif
                            title="{{ $isCurrentStep ? 'Current Step' : ($isCompleted ? 'Completed Step' : ($isAccessible ? 'Go to Step ' . $i : 'Complete previous steps first')) }}">
                        @if($isCompleted)
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            {{ $i }}
                        @endif
                    </button>
                @endfor
            </div>
            
            @if($currentStep == $totalSteps)
                <button type="button" 
                        wire:click="{{ (isset($isEdit) && $isEdit) ? 'confirmUpdate' : 'confirmSubmit' }}"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-xl hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    {{ (isset($isEdit) && $isEdit) ? 'Save Changes' : 'Save Draft' }}
                </button>
            @else
                <button type="button" 
                        wire:click="nextStep"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-xl hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Next
                </button>
            @endif
        </div>
    </div>
    <!-- Confirm Submit Modal (Alpine + Livewire) -->
    <div x-data="{ open: $wire.entangle('showConfirmModal') }" x-show="open" class="fixed inset-0 z-90 flex items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-black/40" x-on:click="open = false"></div>
        <div class="relative bg-white rounded-xl shadow-lg w-full max-w-md mx-4 p-6">
            <h2 class="text-lg font-medium text-gray-900">{{ (isset($isEdit) && $isEdit) ? 'Save Changes' : 'Save Draft' }}</h2>
            <p class="mt-2 text-sm text-gray-600">
                {{ (isset($isEdit) && $isEdit)
                    ? 'Are you sure you want to save these changes?'
                    : 'Save this syllabus as a draft. You and your co-editors can continue editing later.' }}
            </p>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button"
                        x-on:click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-xl hover:bg-gray-300">
                    Cancel
                </button>
                <button type="button"
                        x-on:click="open = false; {{ (isset($isEdit) && $isEdit) ? '$wire.update()' : '$wire.submit()' }}"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700">
                    {{ (isset($isEdit) && $isEdit) ? 'Save' : 'Save Draft' }}
                </button>
            </div>
        </div>
    </div>
</div>
