{{-- References Component --}}
<div>
    <table class="document-references-table">
        <tbody>
            <tr>
                <th colspan="2">References</th>
            </tr>
            <tr>
                <th style="width: 1%; white-space: nowrap; ">Adaptive Digital Solutions</th>
                <td>{!! $references['adaptiveDigitalSolutions'] ?? $syllabus->adaptive_digital_solutions ?? 'Not specified' !!}</td>
            </tr>
            <tr>
                <th style="width: 1%; white-space: nowrap; ">Textbook</th>
                <td>{!! $references['textbook'] ?? $syllabus->textbook_references ?? 'Not specified' !!}</td>
            </tr>
            <tr>
                <th style="width: 1%; white-space: nowrap; ">Online References</th>
                <td>{!! $references['onlineReferences'] ?? $syllabus->online_references ?? 'Not specified' !!}</td>
            </tr>
            <tr>
                <th style="width: 1%; white-space: nowrap; ">Others</th>
                <td>{!! $references['otherReferences'] ?? $syllabus->other_references ?? 'Not specified' !!}</td>
            </tr>
        </tbody>
    </table>
</div>
