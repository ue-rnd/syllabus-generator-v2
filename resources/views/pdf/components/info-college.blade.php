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
    
    @if($college && $college->core_values)
        <h3>College Core Values</h3>
        <p>
            {!! $college->core_values !!}
        </p>
    @endif
    
    @if($college && $college->objectives)
        <h3>College Objectives</h3>
        <p>
            In pursuit of its vision and mission, the College will produce GRADUATES
            {!! $college->objectives !!}
        </p>
    @endif
    
    {{-- Program Educational Objectives Template - Not filled in yet --}}
    <h3>Program Educational Objectives</h3>
    <ol>
        <li>Program Educational Objective 1: (To be defined)</li>
        <li>Program Educational Objective 2: (To be defined)</li>
        <li>Program Educational Objective 3: (To be defined)</li>
        <li>Program Educational Objective 4: (To be defined)</li>
        <li>Program Educational Objective 5: (To be defined)</li>
        <li>Program Educational Objective 6: (To be defined)</li>
    </ol>
</div>
