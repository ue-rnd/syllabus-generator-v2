{{-- Course Syllabus Details Component --}}
<div>
    <div>
        <h1>Course Syllabus in</h1>
        <h2>{{ strtoupper($course->name ?? '') }}</h2>
        <h2>Academic Year {{ $academicYear ?? 'to' }}</h2>
    </div>

    <table>
        <tbody>
            <tr>
                <th colspan="2" class="course-info-header">Course Code</th>
                <td colspan="12">
                    {{ $course->code ?? '' }}
                </td>
            </tr>
            <tr>
                <th colspan="2" class="course-info-header">Course Title</th>
                <td colspan="12">
                    {{ $course->name ?? '' }}
                </td>
            </tr>
            <tr>
                <th rowspan="2" colspan="2" class="course-info-header">
                    Credit Units
                </th>
                <td colspan="1">Lecture</td>
                <td colspan="11">
                    {{ $course->credit_units_lecture ?? 0 }} unit/s
                </td>
            </tr>
            <tr>
                <td colspan="1">Laboratory/Studio</td>
                <td colspan="11">
                    {{ $course->credit_units_laboratory ?? 0 }} unit/s
                </td>
            </tr>
            <tr>
                <th colspan="2" class="course-info-header">Course Type</th>
                <td colspan="3">
                    <div class="course-type-option">
                        <input type="checkbox" name="course-type"
                            {{ ($course->course_type ?? '') === 'onsite' ? 'checked' : '' }} disabled />
                        <p>
                            PURE ONSITE<br />
                            [Face-to-Face]
                        </p>
                    </div>
                </td>
                <td colspan="3">
                    <div class="course-type-option">
                        <input type="checkbox" name="course-type"
                            {{ ($course->course_type ?? '') === 'offsite' ? 'checked' : '' }} disabled />
                        <p>
                            PURE OFFSITE<br />
                            [Online Distance Learning]
                        </p>
                    </div>
                </td>
                <td colspan="3">
                    <div class="course-type-option">
                        <input type="checkbox" name="course-type"
                            {{ ($course->course_type ?? '') === 'hybrid' ? 'checked' : '' }} disabled />
                        <p>
                            HYBRID<br />
                            [Onsite + Offsite]
                        </p>
                    </div>
                </td>
                <td colspan="3">
                    <div class="course-type-option">
                        <input type="checkbox" name="course-type"
                            {{ ($course->course_type ?? '') === 'others' ? 'checked' : '' }} disabled />
                        <p>Others. Please specify.</p>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Prerequisites Table --}}
    <table>
        <tbody>
            @if (!empty($prerequisites))
                <tr>
                    <th rowspan="2" class="course-info-header">Pre-requisite(s)</th>
                    <th class="course-info-header">Course Code</th>
                    @php
                        $prerequisiteCount = count($prerequisites);
                        $totalColumns = 12; // Full 12 columns available for prerequisites

                        // Calculate base columns per prerequisite
                        $baseColsPerPrereq = intval(floor($totalColumns / $prerequisiteCount));

                        // Calculate how many extra columns to distribute
                        $extraColumns = $totalColumns % $prerequisiteCount;

                        // Create an array of column spans for each prerequisite
                        $columnSpans = [];
                        for ($i = 0; $i < $prerequisiteCount; $i++) {
                            // First few prerequisites get an extra column if there are remainder columns
                            $columnSpans[$i] = $baseColsPerPrereq + ($i < $extraColumns ? 1 : 0);
                        }
                    @endphp
                    @foreach ($prerequisites as $index => $prereq)
                        <th colspan="{{ $columnSpans[$index] }}">
                            {{ $prereq['courseCode'] ?? ($prereq['code'] ?? '') }}
                        </th>
                    @endforeach
                </tr>
                <tr>
                    <th class="course-info-header">Course Title</th>
                    @foreach ($prerequisites as $index => $prereq)
                        <td colspan="{{ $columnSpans[$index] }}">
                            {{ $prereq['courseTitle'] ?? ($prereq['name'] ?? '') }}
                        </td>
                    @endforeach
                </tr>
            @else
                <tr>
                    <th rowspan="2" class="course-info-header">Pre-requisite(s)</th>
                    <th class="course-info-header">Course Code</th>
                    <td rowspan="2" colspan="12" class="prerequisites-empty">No Prerequisites</td>
                </tr>
                <tr>
                    <th class="course-info-header">Course Title</th>
                </tr>
            @endif
        </tbody>
    </table>

    <table>
        <tr>
            <th colspan="2">Course Description</th>
        </tr>
        <tr>
            <td colspan="2">
                <p>
                    {!! $syllabus->description ?? ($course->description ?? '') !!}
                </p>
            </td>
        </tr>
    </table>

    <table>
        <tbody>
            <tr>
                <th>Course Learning Outcomes</th>
            </tr>
            <tr>
                <td>
                    <p>
                        <strong>Upon completion of the course, the learner will be able to:</strong>
                    </p>
                    <div>
                        @if (!empty($course_outcomes))
                            <ol>
                                @foreach ($course_outcomes as $outcome)
                                    <li>
                                        <span>{{ ucfirst($outcome['verb'] ?? '') }}</span>
                                        @php
                                            $content = $outcome['content'] ?? '';
                                            // Remove wrapping <p> tags if they exist to keep content inline
                                            $content = preg_replace('/^<p[^>]*>/', '', $content);
                                            $content = preg_replace('/<\/p>$/', '', $content);
                                        @endphp
                                        {!! $content !!}
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <p>Course outcomes to be defined.</p>
                        @endif
                    </div>
                </td>
            </tr>
        </tbody>

    </table>

</div>
