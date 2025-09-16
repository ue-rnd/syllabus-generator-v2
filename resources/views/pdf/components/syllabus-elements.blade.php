{{-- Other Elements Component --}}
<div>
    <table>
        <tbody>
            <tr>
                <th colspan="2">Other Elements</th>
            </tr>
            <tr>
                <th style="width: 1%; white-space: nowrap; ">Grading System</th>
                <td style="width: 99%;">
                    <div>
                        <p>Cumulative Grading System is prescribed by the University. As such, the following
                            computations are applied:</p>
                        <div>
                            <p>
                                {!! $otherElements['gradingSystem'] ??
                                    ($syllabus->grading_system ?? 'Standard University grading system applies.') !!}
                            </p>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th style="width: 1%; white-space: nowrap; ">Classroom Policies</th>
                <td style="width: 99%;">
                    <div>
                        <p>
                            {!! $otherElements['classroomPolicies'] ??
                                ($syllabus->classroom_policies ?? 'Standard classroom policies apply.') !!}
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <th style="width: 1%; white-space: nowrap; ">Consulting Hours</th>
                <td style="width: 99%;">

                    <div>
                        <p>
                            {!! $otherElements['consultationHours'] ?? ($syllabus->consultation_hours ?? 'By appointment.') !!}
                        </p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="page-break"></div>