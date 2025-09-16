@php use App\Constants\SyllabusConstants; @endphp

<style>
    .center {
        text-align: center;
        vertical-align: middle;
    }
</style>

{{-- Learning Matrix Component --}}
@if (!empty($learning_matrix))
    <div>
        <table>
            <thead>
                <tr>
                    <th colspan="11">
                        Learning Matrix
                    </th>
                </tr>
                <tr>
                    <th rowspan="2">Week</th>
                    <th colspan="2">Hours</th>
                    <th rowspan="2">Learning Outcome</th>
                    <th rowspan="2">Content</th>
                    <th colspan="4">Teaching-Learning Activity</th>
                    <th rowspan="2">Resource</th>
                    <th rowspan="2">Assessment</th>
                </tr>
                <tr>
                    <th>Lec</th>
                    <th>Lab</th>
                    <th>Title</th>
                    <th>O</th>
                    <th>A</th>
                    <th>S</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($learning_matrix as $weekItem)
                    @php
                        // Parse week range
                        $weekRange = $weekItem['week_display'] ?? 'N/A';
                        preg_match('/(\d+)(?:-(\d+))?/', $weekRange, $matches);
                        $startWeek = isset($matches[1]) ? (int) $matches[1] : 1;
                        $endWeek = isset($matches[2]) ? (int) $matches[2] : $startWeek;

                        // Calculate total activities to determine rowspan
                        $totalActivities = max(1, count($weekItem['learning_activities'] ?? []));
                        $totalOutcomes = max(1, count($weekItem['learning_outcomes'] ?? []));
                        $maxRows = $totalActivities;

                        $prelim = $startWeek <= $week_prelim && $week_prelim <= $endWeek;
                        $midterm = $startWeek <= $week_midterm && $week_midterm <= $endWeek;
                        $finals = $startWeek <= $week_finals && $week_finals <= $endWeek;
                    @endphp

                    <tr>
                        <td rowspan="{{ $prelim || $midterm || $finals ? $maxRows + 1 : $maxRows }}" class="center">
                            @if ($startWeek === $endWeek)
                                {{ $startWeek }}<sup>{{ $startWeek === 1 ? 'st' : ($startWeek === 2 ? 'nd' : ($startWeek === 3 ? 'rd' : 'th')) }}</sup>
                            @else
                                {{ $startWeek }}<sup>{{ $startWeek === 1 ? 'st' : ($startWeek === 2 ? 'nd' : ($startWeek === 3 ? 'rd' : 'th')) }}</sup>
                                -
                                {{ $endWeek }}<sup>{{ $endWeek === 1 ? 'st' : ($endWeek === 2 ? 'nd' : ($endWeek === 3 ? 'rd' : 'th')) }}</sup>
                            @endif
                        </td>
                        <td rowspan="{{ $maxRows }}" class="center">
                            {{ $syllabus->default_lecture_hours }}
                        </td>
                        <td rowspan="{{ $maxRows }}" class="center">
                            {{ $syllabus->default_laboratory_hours }}
                        </td>
                        {{-- Learning Outcome --}}
                        <td rowspan="{{ $maxRows }}">
                            <div>
                                At the end of the lesson, the learner will be able to:
                                <ul>
                                    @foreach ($weekItem['learning_outcomes'] as $outcome)
                                        <li>
                                            {!! SyllabusConstants::renderVerbAndContent($outcome['verb'], $outcome['content']) !!}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </td>

                        {{-- Content --}}
                        <td rowspan="{{ $maxRows }}" class="center">
                            No content
                            {{-- @foreach ($weekItem['content'] as $content)
                                {{ $content }}
                            @endforeach --}}
                        </td>

                        @for ($row = 0; $row < $maxRows; $row++)
                            {{-- Teaching-Learning Activity --}}
                            @if (isset($weekItem['learning_activities'][$row]))
                                @php
                                    $activity = $weekItem['learning_activities'][$row];
                                    $modalities = $activity['modality'] ?? [];
                                    $hasOnsite = in_array('onsite', $modalities);
                                    $hasAsync = in_array('offsite_asynchronous', $modalities);
                                    $hasSync = in_array('offsite_synchronous', $modalities);
                                @endphp

                                <td class="center">
                                    {!! $activity['description'] ?? 'Lecture' !!}
                                </td>
                                <td class="center">{!! $hasOnsite ? '&check;' : '' !!}</td>
                                <td class="center">{!! $hasAsync ? '&check;' : '' !!}</td>
                                <td class="center">{!! $hasSync ? '&check;' : '' !!}</td>
                            @else
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif

                            {{-- Resource --}}
                            <td>
                                @if (isset($weekItem['learning_activities'][$row]))
                                    @php $activity = $weekItem['learning_activities'][$row]; @endphp
                                    {!! $activity['reference'] ?? '' !!}
                                @endif
                            </td>

                            {{-- Assessment --}}
                            @if ($row === 0)
                                <td rowspan="{{ $maxRows }}">
                                    @if (isset($weekItem['assessments']))
                                        <ul>
                                            @foreach ($weekItem['assessments'] as $assessment)
                                                <li>{{ SyllabusConstants::getAssessmentTypeOptions()[$assessment] }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        No assessment
                                    @endif

                                </td>
                            @endif
                    </tr>

                    {{-- if prelim or midterm or finals --}}
                    <tr>
                        @if ($prelim)
                            <td colspan="11" class="center">Preliminary Examination</td>
                        @elseif ($midterm)
                            <td colspan="11" class="center">Midterm Examination</td>
                        @elseif ($finals)
                            <td colspan="11" class="center">Final Examination</td>
                        @endif
                    </tr>
                @endfor
@endforeach
</tbody>
</table>

{{-- Modality Legend --}}
<div class="note">
    <strong>Modality:</strong> O = Onsite, A = Offline Asynchronous, S = Offline Synchronous
</div>


</div>
@else
<div>
    <div>
        <table>
            <thead>
                <tr>
                    <th colspan="11">
                        Learning Matrix
                    </th>
                </tr>
                <tr>
                    <th rowspan="2">Week</th>
                    <th colspan="2" rowspan="2">Hours</th>
                    <th rowspan="2">Learning Outcome</th>
                    <th rowspan="2">Content</th>
                    <th colspan="4">Teaching-Learning Activity</th>
                    <th rowspan="2">Resource</th>
                    <th rowspan="2">Assessment</th>
                </tr>
                <tr>
                    <th>Title</th>
                    <th>O</th>
                    <th>A</th>
                    <th>S</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        No data available
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
