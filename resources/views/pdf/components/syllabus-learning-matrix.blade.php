{{-- Learning Matrix Component --}}
@if(!empty($learning_matrix))
    <div>
        <h1>LEARNING MATRIX</h1>
        
        <table class="learning-matrix-table">
            <thead>
                <tr>
                    <th rowspan="2" class="matrix-header-week">WEEK</th>
                    <th rowspan="2" class="matrix-header-hour">HOUR</th>
                    <th rowspan="2" class="matrix-header-outcome">LEARNING OUTCOME</th>
                    <th rowspan="2" class="matrix-header-content">CONTENT</th>
                    <th colspan="4" class="matrix-header-activity">TEACHING-LEARNING ACTIVITY</th>
                    <th rowspan="2" class="matrix-header-resource">Resource</th>
                    <th rowspan="2" class="matrix-header-assessment">ASSESSMENT</th>
                </tr>
                <tr>
                    <th class="matrix-header-title">Title</th>
                    <th class="matrix-header-modality-o">O</th>
                    <th class="matrix-header-modality-a">A</th>
                    <th class="matrix-header-modality-s">S</th>
                </tr>
            </thead>
            <tbody>
                @foreach($learning_matrix as $weekItem)
                    @php
                        // Parse week range
                        $weekRange = $weekItem['week_display'] ?? 'N/A';
                        preg_match('/(\d+)(?:-(\d+))?/', $weekRange, $matches);
                        $startWeek = isset($matches[1]) ? (int)$matches[1] : 1;
                        $endWeek = isset($matches[2]) ? (int)$matches[2] : $startWeek;
                        
                        // Calculate total activities to determine rowspan
                        $totalActivities = max(1, count($weekItem['learning_activities'] ?? []));
                        $totalOutcomes = max(1, count($weekItem['learning_outcomes'] ?? []));
                        $maxRows = max($totalActivities, $totalOutcomes);
                    @endphp
                    
                    @for($row = 0; $row < $maxRows; $row++)
                        <tr>
                            @if($row === 0)
                                <td rowspan="{{ $maxRows }}" class="matrix-week">
                                    @if($startWeek === $endWeek)
                                        {{ $startWeek }}<sup>{{ $startWeek === 1 ? 'st' : ($startWeek === 2 ? 'nd' : ($startWeek === 3 ? 'rd' : 'th')) }}</sup>
                                    @else
                                        {{ $startWeek }}<sup>{{ $startWeek === 1 ? 'st' : ($startWeek === 2 ? 'nd' : ($startWeek === 3 ? 'rd' : 'th')) }}</sup> - {{ $endWeek }}<sup>{{ $endWeek === 1 ? 'st' : ($endWeek === 2 ? 'nd' : ($endWeek === 3 ? 'rd' : 'th')) }}</sup>
                                    @endif
                                </td>
                                <td rowspan="{{ $maxRows }}" class="matrix-hour">
                                    @php
                                        $lecHours = 3; // Default from syllabus
                                        $labHours = 12; // Default from syllabus
                                    @endphp
                                    <div>Lec: {{ $lecHours }}</div>
                                    <div>Lab: {{ $labHours }}</div>
                                </td>
                            @endif
                            
                            {{-- Learning Outcome --}}
                            <td class="matrix-outcome">
                                @if(isset($weekItem['learning_outcomes'][$row]))
                                    @php $outcome = $weekItem['learning_outcomes'][$row]; @endphp
                                    <div class="outcome-text">
                                        At the end of the lesson, the learner will be able to:
                                        <br>• {{ ucfirst($outcome['verb'] ?? '') }} {!! strip_tags($outcome['content'] ?? '') !!}
                                    </div>
                                @endif
                            </td>
                            
                            {{-- Content --}}
                            <td class="matrix-content">
                                @if($row === 0)
                                    {{-- Extract content from first learning outcome or activity --}}
                                    @if(!empty($weekItem['learning_outcomes']))
                                        @php $firstOutcome = $weekItem['learning_outcomes'][0]; @endphp
                                        {{ $row + 1 }}. {{ $firstOutcome['verb'] ?? 'Content' }}
                                        <br>• {{ strip_tags($firstOutcome['content'] ?? '') }}
                                    @endif
                                @endif
                            </td>
                            
                            {{-- Teaching-Learning Activity --}}
                            @if(isset($weekItem['learning_activities'][$row]))
                                @php 
                                    $activity = $weekItem['learning_activities'][$row];
                                    $modalities = is_array($activity['modality'] ?? []) ? $activity['modality'] : [$activity['modality'] ?? ''];
                                    $hasOnsite = in_array('onsite', $modalities);
                                    $hasAsync = in_array('offsite_asynchronous', $modalities);
                                    $hasSync = in_array('offsite_synchronous', $modalities);
                                @endphp
                                <td class="matrix-activity-title">
                                    {!! strip_tags($activity['description'] ?? 'Lecture') !!}
                                </td>
                                <td class="matrix-modality-check">{{ $hasOnsite ? '✓' : '' }}</td>
                                <td class="matrix-modality-check">{{ $hasAsync ? '✓' : '' }}</td>
                                <td class="matrix-modality-check">{{ $hasSync ? '✓' : '' }}</td>
                            @else
                                <td class="matrix-activity-title"></td>
                                <td class="matrix-modality-check"></td>
                                <td class="matrix-modality-check"></td>
                                <td class="matrix-modality-check"></td>
                            @endif
                            
                            {{-- Resource --}}
                            <td class="matrix-resource">
                                @if(isset($weekItem['learning_activities'][$row]))
                                    @php $activity = $weekItem['learning_activities'][$row]; @endphp
                                    {!! strip_tags($activity['reference'] ?? '') !!}
                                @endif
                            </td>
                            
                            {{-- Assessment --}}
                            @if($row === 0)
                                <td rowspan="{{ $maxRows }}" class="matrix-assessment">
                                    @if(!empty($weekItem['assessments']))
                                        @if(is_array($weekItem['assessments']))
                                            {{ implode(', ', array_map('ucfirst', $weekItem['assessments'])) }}
                                        @else
                                            {{ ucfirst($weekItem['assessments']) }}
                                        @endif
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endfor
                @endforeach
            </tbody>
        </table>
        
        {{-- Modality Legend --}}
        <div class="legend-text">
            <strong>Modality:</strong> O=Onsite, A=Offline Asynchronous, S=Offline Synchronous
        </div>
        
        {{-- Preliminary Examination Section --}}
        <div style="margin-top: 20pt;">
            <table class="learning-matrix-table">
                <thead>
                    <tr>
                        <th colspan="9" class="matrix-header-preliminary">PRELIMINARY EXAMINATION</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@else
    <div>
        <h1>LEARNING MATRIX</h1>
        <p>No learning matrix data available.</p>
    </div>
@endif
