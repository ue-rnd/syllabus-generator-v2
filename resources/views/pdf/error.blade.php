<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Generation Error</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        
        .error-container {
            border: 2px solid #dc2626;
            background-color: #fef2f2;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .error-title {
            color: #dc2626;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .error-message {
            background-color: white;
            padding: 15px;
            border-radius: 4px;
            border-left: 4px solid #dc2626;
            margin: 10px 0;
        }
        
        .syllabus-info {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        
        .info-label {
            font-weight: bold;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-title">PDF Generation Error</div>
        
        <div class="error-message">
            <strong>Error:</strong> {{ $errorMessage }}
        </div>
        
        <div class="syllabus-info">
            <div class="info-label">Syllabus Information:</div>
            <p><strong>ID:</strong> {{ $syllabus->id ?? 'Unknown' }}</p>
            <p><strong>Name:</strong> {{ $syllabus->name ?? 'Unknown' }}</p>
            @if($course)
                <p><strong>Course:</strong> {{ $course->code ?? 'Unknown' }} - {{ $course->name ?? 'Unknown' }}</p>
            @endif
            <p><strong>Generated:</strong> {{ $generated_at->format('M j, Y \a\t g:i A') }}</p>
        </div>
        
        <p>Please contact the system administrator to resolve this issue.</p>
    </div>
</body>
</html>
