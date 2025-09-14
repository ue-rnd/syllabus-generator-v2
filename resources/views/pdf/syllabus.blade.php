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
            'logoPath' => public_path('images/logo_ue.png')
        ])
        
        {{-- College Constants --}}
        @include('pdf.components.info-college', [
            'college' => $college
        ])
        
        {{-- Program Outcomes --}}
        @include('pdf.components.info-program', [
            'programOutcomes' => $programOutcomes ?? []
        ])
        
        {{-- Course Syllabus Details --}}
        @include('pdf.components.syllabus-details', [
            'course' => $course,
            'syllabus' => $syllabus,
            'course_outcomes' => $course_outcomes,
            'prerequisites' => $prerequisites ?? [],
            'academicYear' => $academicYear ?? date('Y') . '-' . (date('Y') + 1)
        ])
        
        {{-- Learning Matrix --}}
        @include('pdf.components.syllabus-learning-matrix', [
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
