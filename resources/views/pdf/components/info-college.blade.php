{{-- College Specific Information Component --}}
<div>
    <h2>{{ $college->name ?? 'COLLEGE' }}</h2>
    
    @if($college && $college->mission)
        <h3>College Mission</h3>
        <p>
            {!! $college->mission !!}
        </p>
    @endif
    
    @if($college && $college->vision)
        <h3>College Vision</h3>
        <p>
            {!! $college->vision !!}
        </p>
    @endif
    
    {{-- @if($college && $college->core_values)
        <h3>College Core Values</h3>
        <p>
            {!! $college->core_values !!}
        </p>
    @endif --}}
    
    @if($college && $college->objectives)
        <h3>College Objectives</h3>
        @if (is_array($college->objectives))
            <ul>
                @foreach ($college->objectives as $objective)
                    <li>{!! is_array($objective) ? ($objective['content'] ?? e(json_encode($objective))) : $objective !!}</li>
                @endforeach
            </ul>
        @else
            <p>
                In pursuit of its vision and mission, the College will produce GRADUATES
                {!! $college->objectives !!}
            </p>
        @endif
    @endif
    
    {{-- Program Educational Objectives Template - Not filled in yet --}}

    @if ($programObjectives)
        <h3>Program Educational Objectives</h3>
        <p>
            {!! $programObjectives !!}
        </p>
    @endif
</div>

<div class="page-break"></div>
