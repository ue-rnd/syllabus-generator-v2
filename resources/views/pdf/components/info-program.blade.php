{{-- Program Outcomes Table Component --}}
<div>
    <h3>Program Outcomes</h3>
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="text-align: center; vertical-align: middle; width: 50%;">
                    <strong>Program Outcomes</strong><br />
                    <span class="note">
                        (Refer to your curriculum map.)
                    </span>
                </th>
                <th colspan="3" style="text-align: center; vertical-align: middle;">
                    <strong>Program Outcomes Addressed by the Course</strong><br />
                    <span class="note">
                        (Place a checkmark [ &#10003; ] to the appropriate cell.)
                    </span>
                </th>
            </tr>
            <tr>
                <th style="text-align: center; vertical-align: middle; width: 16.67%;">
                    <strong>Introduce</strong><br />
                    <strong>(I)</strong>
                </th>
                <th style="text-align: center; vertical-align: middle; width: 16.67%;">
                    <strong>Enhanced</strong><br />
                    <strong>(E)</strong>
                </th>
                <th style="text-align: center; vertical-align: middle; width: 16.67%;">
                    <strong>Demonstrated</strong><br />
                    <strong>(D)</strong>
                </th>
            </tr>
        </thead>
        <tbody>
            {{-- Loop through program outcomes if available --}}
            @if(!empty($programOutcomes))
                @foreach($programOutcomes as $key => $outcome)
                    <tr>
                        <td>{!! $outcome['content'] ?? '' !!}</td>
                        <td style="text-align: center;">
                            {!! (in_array('introduced', $outcome['addressed'] ?? []) ? '&check;' : '&nbsp;') !!}
                        </td>
                        <td style="text-align: center;">
                            {!! (in_array('enhanced', $outcome['addressed'] ?? []) ? '&check;' : '&nbsp;') !!}
                        </td>
                        <td style="text-align: center;">
                            {!! (in_array('demonstrated', $outcome['addressed'] ?? []) ? '&check;' : '&nbsp;') !!}
                        </td>
                    </tr>
                @endforeach
            @else
                {{-- Template rows when no data is available --}}
                @for($i = 1; $i <= 12; $i++)
                    <tr>
                        <td>Program Outcome {{ $i }}: (To be defined based on curriculum map)</td>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: center;">&nbsp;</td>
                        <td style="text-align: center;">&nbsp;</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
</div>

<div class="page-break"></div>