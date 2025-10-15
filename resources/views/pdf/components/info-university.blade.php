{{-- University of the East Header Component --}}
<div>
    <div class="header-logo">
        @if (isset($logo_base64) && $logo_base64)
            <img src="{{ $logo_base64 }}" alt="UE Logo" />
        @endif
    </div>

    <h1>University of the East</h1>

    <h3>University Mission Statement</h3>
    {!! $university_mission !!}

    <br>
    <br>
    <br>


    <h3>University Vision Statement</h3>
    {!! $university_vision !!}

    <br>
    <br>
    <br>
    

    <h3>Core Values</h3>
    {!! $university_core_values !!}
</div>

<div class="page-break"></div>

<div>
    <div>
        <h3>Guiding Principles</h3>
        {!! $university_guiding_principles !!}

        <br>
        <br>
        <br>
    

        <h3>Institutional Outcomes</h3>
        {!! $university_institutional_outcomes !!}
    </div>
</div>

<div class="page-break"></div>
