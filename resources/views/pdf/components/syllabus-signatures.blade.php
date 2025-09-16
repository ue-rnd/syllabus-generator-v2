{{-- Signatures Component --}}
<div>
    {{-- Preparers Section --}}
    <div>

        {{-- Signatures Component --}}
        <style>
            .signatures-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15pt;
                border: none;
            }
            .signatures-td {
                width: 33.33%;
                text-align: center;
                vertical-align: bottom;
                padding: 0 10pt;
                border: none;
            }
            .signatures-label {
                text-align: left;
                font-weight: bold;
                margin-bottom: 8pt;
            }
            .signatures-line {
                border-bottom: 1pt solid #000;
                height: 30pt;
                margin-bottom: 5pt;
                width: 100%;
            }
            .signatures-name {
                text-align: center;
                font-weight: bold;
                margin-bottom: 2pt;
            }
            .signatures-role {
                text-align: center;
                font-size: 10pt;
            }
            .signatures-title {
                text-align: center;
                font-size: 10pt;
                margin-bottom: 2pt;
            }
            .signatures-college {
                text-align: center;
                font-size: 9pt;
                margin-bottom: 3pt;
            }
            .signatures-date {
                text-align: center;
                font-size: 8pt;
            }
            .signatures-approvers {
                margin-top: 30pt;
            }
        </style>
        <div>
            {{-- Preparers Section --}}
            <div>
                @if(!empty($preparers))
                    @php
                        $preparerChunks = array_chunk(array_filter($preparers, function($p) { return !empty($p['name']); }), 3);
                    @endphp
                    @foreach($preparerChunks as $chunk)
                        <table class="signatures-table">
                            <tr>
                                @foreach($chunk as $prepIndex => $preparer)
                                    <td class="signatures-td">
                                        @if($prepIndex === 0)
                                            <div class="signatures-label">PREPARED BY:</div>
                                        @endif
                                        <div class="signatures-line"></div>
                                        <div class="signatures-name">{{ $preparer['name'] }}</div>
                                        <div class="signatures-role">{{ $preparer['role'] ?? 'Faculty' }}</div>
                                    </td>
                                @endforeach
                                @for($i = count($chunk); $i < 3; $i++)
                                    <td class="signatures-td"></td>
                                @endfor
                            </tr>
                        </table>
                    @endforeach
                @else
                    <table class="signatures-table">
                        <tr>
                            <td class="signatures-td">
                                <div class="signatures-line"></div>
                                <div class="signatures-name">[Principal Preparer Name]</div>
                                <div class="signatures-role">Principal Preparer</div>
                            </td>
                            <td class="signatures-td">
                                <div class="signatures-line"></div>
                                <div class="signatures-name">[Library Committee Member]</div>
                                <div class="signatures-role">Member of the Library Committee</div>
                            </td>
                            <td class="signatures-td"></td>
                        </tr>
                    </table>
                @endif
            </div>

            {{-- Approvers Section --}}
            <div class="signatures-approvers">
                <table class="signatures-table">
                    <tr>
                        {{-- Reviewer --}}
                        <td class="signatures-td">
                            <div class="signatures-label">REVIEWED BY:</div>
                            <div class="signatures-line"></div>
                            <div class="signatures-name">{{ $syllabus->reviewer->full_name ?? $approvers['departmentChair'] ?? '[Department Chair Name]' }}</div>
                            <div class="signatures-title">Department Chair</div>
                            <div class="signatures-college">{{ $syllabus->course->programs()->first()->department->name ?? 'Department Name' }}</div>
                            @if($approval_details['dept_chair_reviewed_at'])
                                <div class="signatures-date">Date: {{ \Carbon\Carbon::parse($approval_details['dept_chair_reviewed_at'])->format('M j, Y') }}</div>
                            @endif
                        </td>
                        {{-- Recommending Approval --}}
                        <td class="signatures-td">
                            <div class="signatures-label">RECOMMENDING APPROVAL:</div>
                            <div class="signatures-line"></div>
                            <div class="signatures-name">{{ $syllabus->recommendingApprover->full_name ?? $approvers['associateDean'] ?? '[Associate Dean Name]' }}</div>
                            <div class="signatures-title">Associate Dean</div>
                            <div class="signatures-college">{{ $college->name ?? 'College Name' }}</div>
                            @if($approval_details['assoc_dean_reviewed_at'])
                                <div class="signatures-date">Date: {{ \Carbon\Carbon::parse($approval_details['assoc_dean_reviewed_at'])->format('M j, Y') }}</div>
                            @endif
                        </td>
                        {{-- Final Approval --}}
                        <td class="signatures-td">
                            <div class="signatures-label">APPROVED BY:</div>
                            <div class="signatures-line"></div>
                            <div class="signatures-name">{{ $syllabus->approver->full_name ?? $approvers['dean'] ?? '[Dean Name]' }}</div>
                            <div class="signatures-title">Dean</div>
                            <div class="signatures-college">{{ $college->name ?? 'College Name' }}</div>
                            @if($approval_details['dean_approved_at'])
                                <div class="signatures-date">Date: {{ \Carbon\Carbon::parse($approval_details['dean_approved_at'])->format('M j, Y') }}</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
                    {{-- Title --}}
