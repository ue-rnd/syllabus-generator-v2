<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->name ?? 'Course' }} - Syllabus</title>
    <style>
        {!! file_get_contents(resource_path('views/pdf/styles/syllabus-pdf.css')) !!}
    </style>
</head>
<body>
    <div>
        {{-- Error Handling --}}
        @if($errors ?? false)
            <div class="error-container">
                <strong>Errors found:</strong>
                <ul>
                    @foreach($errors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        {{-- University of the East Header --}}
        @include('pdf.components.info-university', [
            'logoPath' => public_path('images/logo_ue.png'),
            'university_mission' => $university_mission,
            'university_vision'=> $university_vision,
            'university_core_values' => $university_core_values,
            'university_guiding_principles' => $university_guiding_principles,
            'university_institutional_outcomes'=> $university_institutional_outcomes,
        ])
        
        {{-- College Constants --}}
        @include('pdf.components.info-college', [
            'college' => $college,
            'programObjectives' => $programObjectives,
        ])
        
        {{-- Program Outcomes --}}
        @include('pdf.components.info-program', [
            'programOutcomes' => $programOutcomes
        ])
        
        {{-- Course Syllabus Details --}}
        @include('pdf.components.syllabus-details', [
            'course' => $course,
            'syllabus' => $syllabus,
            'course_outcomes' => $course_outcomes,
            'prerequisites' => $prerequisites ?? [],
            'academicYear' => $syllabus->ay_start . '-' . $syllabus->ay_end,
        ])
        
        {{-- Learning Matrix --}}
        @include('pdf.components.syllabus-learning-matrix', [
            'week_prelim' => $syllabus->week_prelim,
            'week_midterm' => $syllabus->week_midterm,
            'week_finals' => $syllabus->week_finals,
            'learning_matrix' => $learning_matrix,
            'total_hours' => $total_hours,
        ])
        
        {{-- References --}}
        @include('pdf.components.syllabus-references', [
            'references' => $references ?? [],
            'syllabus' => $syllabus
        ])
        
        {{-- Other Elements --}}
        @include('pdf.components.syllabus-elements', [
            'otherElements' => $otherElements ?? [],
            'syllabus' => $syllabus,
        ])
        
        {{-- Signatures --}}
        @include('pdf.components.syllabus-signatures', [
            'preparers' => $preparers,
            'syllabus' => $syllabus,
            'approval_details' => $approval_details,
            'approvers' => $approvers ?? [],
            'college' => $college
        ])
    </div>
</body>
</html>
</body>
</html>
