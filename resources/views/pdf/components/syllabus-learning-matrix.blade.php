{{-- Learning Matrix Component --}}
@if(!empty($learning_matrix))
    @foreach($learning_matrix as $weekItem)
        <div>
            <h1>{{ $weekItem['week_display'] ?? 'Week' }}</h1>
            
            <table class="learning-matrix-table-detailed">
                <thead>
                    <tr>
                        <th class="matrix-header">Learning Outcomes</th>
                        <th class="matrix-header">Learning Activities</th>
                        <th class="matrix-header">Assessment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="matrix-outcomes">
                            @if(!empty($weekItem['learning_outcomes']))
                                <ul class="outcomes-list">
                                    @foreach($weekItem['learning_outcomes'] as $outcome)
                                        <li>
                                            <span class="outcome-verb">{{ $outcome['verb'] ?? '' }}</span>
                                            {!! $outcome['content'] ?? '' !!}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No learning outcomes defined for this week.</p>
                            @endif
                        </td>
                        <td class="matrix-activities">
                            @if(!empty($weekItem['learning_activities']))
                                @foreach($weekItem['learning_activities'] as $activity)
                                    <div class="activity-item">
                                        @if(!empty($activity['modality']))
                                            <div class="activity-modality">
                                                <strong>Modality:</strong>
                                                @if(is_array($activity['modality']))
                                                    {{ implode(', ', $activity['modality']) }}
                                                @else
                                                    {{ $activity['modality'] }}
                                                @endif
                                            </div>
                                        @endif
                                        @if(!empty($activity['description']))
                                            <div class="activity-description">
                                                <strong>Activity:</strong> {!! $activity['description'] !!}
                                            </div>
                                        @endif
                                        @if(!empty($activity['reference']))
                                            <div class="activity-reference">
                                                <strong>Reference:</strong> {!! $activity['reference'] !!}
                                            </div>
                                        @endif
                                    </div>
                                    @if(!$loop->last)
                                        <hr class="activity-separator">
                                    @endif
                                @endforeach
                            @else
                                <p>No learning activities defined for this week.</p>
                            @endif
                        </td>
                        <td class="matrix-assessments">
                            @if(!empty($weekItem['assessments']))
                                @if(is_array($weekItem['assessments']))
                                    {{ implode(', ', $weekItem['assessments']) }}
                                @else
                                    {{ $weekItem['assessments'] }}
                                @endif
                            @else
                                No assessments
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach
@else
    <div>
        <h1>LEARNING MATRIX</h1>
        <p>No learning matrix data available.</p>
    </div>
@endif
