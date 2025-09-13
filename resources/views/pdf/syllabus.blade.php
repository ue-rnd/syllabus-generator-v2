<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->name ?? 'Course' }} - Syllabus</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .university-logo {
            max-height: 80px;
            margin-bottom: 10px;
        }
        
        .university-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .college-name {
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .department-name {
            font-size: 12px;
            margin-bottom: 15px;
        }
        
        .syllabus-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
        }
        
        .course-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .course-info-row {
            display: table-row;
        }
        
        .course-info-label {
            display: table-cell;
            font-weight: bold;
            width: 150px;
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }
        
        .course-info-value {
            display: table-cell;
            padding: 5px 0;
            vertical-align: top;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2563eb;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        .subsection-title {
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0 8px 0;
            color: #4b5563;
        }
        
        .outcome-list {
            counter-reset: outcome-counter;
            padding-left: 0;
        }
        
        .outcome-item {
            counter-increment: outcome-counter;
            margin-bottom: 8px;
            padding-left: 25px;
            position: relative;
        }
        
        .outcome-item:before {
            content: counter(outcome-counter) ".";
            position: absolute;
            left: 0;
            font-weight: bold;
        }
        
        .outcome-verb {
            font-weight: bold;
            color: #2563eb;
        }
        
        .learning-matrix-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        
        .learning-matrix-table th,
        .learning-matrix-table td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        .learning-matrix-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 10px;
        }
        
        .week-cell {
            width: 80px;
            text-align: center;
            font-weight: bold;
        }
        
        .activities-cell {
            width: 200px;
        }
        
        .outcomes-cell {
            width: 180px;
        }
        
        .assessments-cell {
            width: 120px;
        }
        
        .modality-badge {
            display: inline-block;
            background-color: #e5e7eb;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 9px;
            margin: 1px;
        }
        
        .reference-content {
            margin-top: 8px;
        }
        
        .grading-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        .grading-table th,
        .grading-table td {
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: left;
        }
        
        .grading-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        
        .signatures-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        
        .signature-grid {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        
        .signature-row {
            display: table-row;
        }
        
        .signature-cell {
            display: table-cell;
            width: 50%;
            padding: 20px 10px;
            vertical-align: top;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            height: 40px;
        }
        
        .signature-label {
            font-size: 10px;
            font-weight: bold;
        }
        
        .signature-name {
            font-size: 9px;
            margin-top: 2px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #6b7280;
            text-align: center;
        }
        
        .approval-status {
            background-color: #f8fafc;
            padding: 10px;
            border-left: 4px solid #2563eb;
            margin-bottom: 20px;
        }
        
        .approval-status.approved {
            border-left-color: #059669;
            background-color: #f0fdf4;
        }
        
        .approval-status.rejected {
            border-left-color: #dc2626;
            background-color: #fef2f2;
        }
        
        @page {
            margin: 20mm 15mm;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        @if($college && $college->logo_url)
            <img src="{{ public_path($college->logo_url) }}" alt="{{ $college->name }} Logo" class="university-logo">
        @endif
        
        <div class="university-name">
            {{ $college->name ?? 'University Name' }}
        </div>
        
        <div class="college-name">{{ $college->name ?? '' }}</div>
        
        <div class="syllabus-title">COURSE SYLLABUS</div>
    </div>

    <!-- Approval Status -->
    @if($approval_details['status'] !== 'draft')
        <div class="approval-status {{ $approval_details['status'] === 'approved' ? 'approved' : ($approval_details['status'] === 'rejected' ? 'rejected' : '') }}">
            <strong>Status:</strong> {{ $approval_details['status_label'] }}
            @if($approval_details['version'] > 1)
                | <strong>Version:</strong> {{ $approval_details['version'] }}
            @endif
            @if($approval_details['dean_approved_at'])
                | <strong>Approved:</strong> {{ \Carbon\Carbon::parse($approval_details['dean_approved_at'])->format('M j, Y') }}
            @endif
        </div>
    @endif

    <!-- Course Information -->
    <div class="section">
        <div class="section-title">Course Information</div>
        <div class="course-info">
            <div class="course-info-row">
                <div class="course-info-label">Course Title:</div>
                <div class="course-info-value">{{ $course->name ?? 'N/A' }}</div>
            </div>
            <div class="course-info-row">
                <div class="course-info-label">Course Code:</div>
                <div class="course-info-value">{{ $course->code ?? 'N/A' }}</div>
            </div>
            @if($course->units)
                <div class="course-info-row">
                    <div class="course-info-label">Units:</div>
                    <div class="course-info-value">{{ $course->units }}</div>
                </div>
            @endif
            @if($course->prerequisites)
                <div class="course-info-row">
                    <div class="course-info-label">Prerequisites:</div>
                    <div class="course-info-value">{{ $course->prerequisites }}</div>
                </div>
            @endif
            @if($course->corequisites)
                <div class="course-info-row">
                    <div class="course-info-label">Corequisites:</div>
                    <div class="course-info-value">{{ $course->corequisites }}</div>
                </div>
            @endif
            <div class="course-info-row">
                <div class="course-info-label">Lecture Hours/Week:</div>
                <div class="course-info-value">{{ $syllabus->default_lecture_hours ?? 0 }} hours</div>
            </div>
            <div class="course-info-row">
                <div class="course-info-label">Laboratory Hours/Week:</div>
                <div class="course-info-value">{{ $syllabus->default_laboratory_hours ?? 0 }} hours</div>
            </div>
            <div class="course-info-row">
                <div class="course-info-label">Total Semester Hours:</div>
                <div class="course-info-value">{{ $total_hours['total'] ?? 0 }} hours ({{ $total_hours['weeks'] ?? 0 }} weeks)</div>
            </div>
        </div>
        
        @if($syllabus->description)
            <div class="subsection-title">Course Description</div>
            <p>{{ $syllabus->description }}</p>
        @endif
    </div>

    <!-- Course Outcomes -->
    @if(!empty($course_outcomes))
        <div class="section">
            <div class="section-title">Course Learning Outcomes</div>
            <div class="outcome-list">
                @foreach($course_outcomes as $outcome)
                    <div class="outcome-item">
                        <span class="outcome-verb">{{ ucfirst($outcome['verb'] ?? '') }}</span>
                        {!! strip_tags($outcome['content'] ?? '') !!}
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Learning Matrix -->
    @if(!empty($learning_matrix))
        <div class="section page-break">
            <div class="section-title">Weekly Learning Matrix</div>
            <table class="learning-matrix-table">
                <thead>
                    <tr>
                        <th class="week-cell">Week(s)</th>
                        <th class="outcomes-cell">Learning Outcomes</th>
                        <th class="activities-cell">Learning Activities</th>
                        <th class="assessments-cell">Assessments</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($learning_matrix as $item)
                        <tr>
                            <td class="week-cell">{{ $item['week_display'] }}</td>
                            <td class="outcomes-cell">
                                @foreach($item['learning_outcomes'] as $outcome)
                                    <div style="margin-bottom: 5px;">
                                        <span class="outcome-verb">{{ $outcome['verb'] }}</span> {{ $outcome['content'] }}
                                    </div>
                                @endforeach
                            </td>
                            <td class="activities-cell">
                                @foreach($item['learning_activities'] as $activity)
                                    <div style="margin-bottom: 8px;">
                                        @if($activity['modality'])
                                            <div>
                                                @foreach(explode(',', $activity['modality']) as $modality)
                                                    <span class="modality-badge">{{ trim($modality) }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($activity['description'])
                                            <div style="margin-top: 3px;"><strong>Activity:</strong> {{ $activity['description'] }}</div>
                                        @endif
                                        @if($activity['reference'])
                                            <div style="margin-top: 3px;"><strong>Reference:</strong> {{ $activity['reference'] }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                            <td class="assessments-cell">{{ $item['assessments'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- References & Resources -->
    <div class="section">
        <div class="section-title">References & Resources</div>
        
        @if($syllabus->textbook_references)
            <div class="subsection-title">Textbook References</div>
            <div>{!! $syllabus->textbook_references !!}</div>
        @endif
        
        @if($syllabus->adaptive_digital_solutions)
            <div class="subsection-title">Adaptive Digital Solutions</div>
            <div>{!! $syllabus->adaptive_digital_solutions !!}</div>
        @endif
        
        @if($syllabus->online_references)
            <div class="subsection-title">Online References</div>
            <div>{!! $syllabus->online_references !!}</div>
        @endif
        
        @if($syllabus->other_references)
            <div class="subsection-title">Other References</div>
            <div>{!! $syllabus->other_references !!}</div>
        @endif
    </div>

    <!-- Grading System -->
    @if($syllabus->grading_system)
        <div class="section">
            <div class="section-title">Grading System</div>
            <div>{!! $syllabus->grading_system !!}</div>
        </div>
    @endif

    <!-- Classroom Policies -->
    @if($syllabus->classroom_policies)
        <div class="section">
            <div class="section-title">Classroom Policies</div>
            <div>{!! $syllabus->classroom_policies !!}</div>
        </div>
    @endif

    <!-- Consultation Hours -->
    @if($syllabus->consultation_hours)
        <div class="section">
            <div class="section-title">Consultation Hours</div>
            <div>{!! $syllabus->consultation_hours !!}</div>
        </div>
    @endif

    <!-- Signatures Section -->
    <div class="signatures-section no-break">
        <div class="section-title">Approval Signatures</div>
        
        <div class="signature-grid">
            <!-- Preparers -->
            @foreach($preparers as $index => $preparer)
                @if($index % 2 == 0)
                    <div class="signature-row">
                @endif
                        <div class="signature-cell">
                            <div class="signature-line"></div>
                            <div class="signature-label">{{ $preparer['role'] }}</div>
                            <div class="signature-name">{{ $preparer['name'] }}</div>
                            @if($preparer['position'])
                                <div class="signature-name">{{ $preparer['position'] }}</div>
                            @endif
                        </div>
                @if($index % 2 == 1 || $index == count($preparers) - 1)
                    </div>
                @endif
            @endforeach

            <!-- Reviewers and Approvers -->
            <div class="signature-row">
                <div class="signature-cell">
                    <div class="signature-line"></div>
                    <div class="signature-label">Reviewed by (Department Chair)</div>
                    <div class="signature-name">{{ $syllabus->reviewer->full_name ?? '__________________' }}</div>
                    @if($approval_details['dept_chair_reviewed_at'])
                        <div class="signature-name">Date: {{ \Carbon\Carbon::parse($approval_details['dept_chair_reviewed_at'])->format('M j, Y') }}</div>
                    @endif
                </div>
                <div class="signature-cell">
                    <div class="signature-line"></div>
                    <div class="signature-label">Recommending Approval (Associate Dean)</div>
                    <div class="signature-name">{{ $syllabus->recommendingApprover->full_name ?? '__________________' }}</div>
                    @if($approval_details['assoc_dean_reviewed_at'])
                        <div class="signature-name">Date: {{ \Carbon\Carbon::parse($approval_details['assoc_dean_reviewed_at'])->format('M j, Y') }}</div>
                    @endif
                </div>
            </div>
            
            <div class="signature-row">
                <div class="signature-cell">
                    <div class="signature-line"></div>
                    <div class="signature-label">Approved by (Dean)</div>
                    <div class="signature-name">{{ $syllabus->approver->full_name ?? '__________________' }}</div>
                    @if($approval_details['dean_approved_at'])
                        <div class="signature-name">Date: {{ \Carbon\Carbon::parse($approval_details['dean_approved_at'])->format('M j, Y') }}</div>
                    @endif
                </div>
                <div class="signature-cell">
                    <!-- Empty cell for spacing -->
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Generated on {{ $generated_at->format('M j, Y \a\t g:i A') }} by {{ $generated_by->full_name ?? $generated_by->name }}</p>
        <p>Syllabus Version {{ $syllabus->version ?? 1 }} | Document ID: {{ $syllabus->id }}</p>
    </div>
</body>
</html>
