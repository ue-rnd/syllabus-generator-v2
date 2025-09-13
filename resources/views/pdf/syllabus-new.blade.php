<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->name ?? 'Course' }} - Syllabus</title>
    <style>
        /* Base document styling */
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        /* Page layout */
        h1, h2, h3, h4, h5, h6 {
            margin: 8pt 0 4pt 0;
            padding: 0;
        }
        
        /* Typography */
        h1 {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase
        }

        h2 {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase
        }
        
        h3 {
            font-size: 12pt;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase
        }

        ul, ol, li {
            margin: 4pt 0;
            padding: 0;
        }

        p {
            margin: 4pt 0;
            text-align: justify;
        }
        
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8pt 0;
            font-size: 10pt;
        }
        
        th, td {
            border: 1pt solid #000;
            padding: 4pt 6pt;
            text-align: left;
            vertical-align: middle;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        table.signatures th, table.signatures td {
            border: none;
            padding: 0;
        }
        
        /* Lists */
        ol, ul {
            margin: 4pt 0;
            padding-left: 20pt;
        }
        
        li {
            margin: 2pt 0;
        }
        
        /* Logo */
        img {
            text-align: center
            display: block;
            margin: auto;   
            max-width: 60pt;
            max-height: 60pt;
        }
        
        .header-logo {
            text-align: center;
            margin-bottom: 8pt;
        }

        .note {
            font-size: 8pt;
            text-align: center;
            font-style: italic;
            color: #555555;
            margin: 0;
            padding: 0;
        }

        /* Page settings */
        @page {
            margin: 0.5in 0.5in 0.5in 0.5in;
            size: A4 landscape;
        }

        @media print {
            .page-break {
                break-after: page;
            }
        }
    </style>
    
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("Arial", "normal");
            $courseCode = "{{ $course->code ?? 'Course Code' }}";
            $courseName = "{{ $course->name ?? 'Course Name' }}";
            $universityName = "University of the East";
            $collegeName = "{{ $college->name ?? 'College Name' }}";
            
            // Get page dimensions
            $pageHeight = $pdf->get_height();
            $pageWidth = $pdf->get_width();
            
            // Footer positioning for landscape A4 with 0.5" margins (36pt = 0.5")
            $leftMargin = 36; // 0.5 inch = 36 points
            $rightMargin = 36;
            $footerY = $pageHeight - 50; // 50 points from bottom
            
            // Left side footer - two lines, respecting left margin
            $pdf->text($leftMargin, $footerY, $courseCode . " - " . $courseName, $font, 9);
            $pdf->text($leftMargin, $footerY + 12, $universityName . " - " . $collegeName, $font, 9);
            
            // Right side footer - respecting right margin
            $rightText = "$PAGE_NUM out of $PAGE_COUNT";
            $textWidth = $fontMetrics->get_text_width($rightText, $font, 9);
            $pdf->text($pageWidth - $rightMargin - $textWidth, $footerY + 6, $rightText, $font, 9);
        }
    </script>
</head>
<body>
    <div>
        {{-- Error Handling --}}
        @if($errors ?? false)
            <div>
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
            'learning_matrix' => $learning_matrix
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
            'gradingSystemImagePath' => public_path('images/GradingSystem.png')
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
