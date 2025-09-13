{{-- Signatures Component --}}
<div>
    {{-- Preparers Section --}}
    <div style="margin-bottom: 20pt;">
        <div style="background-color: #333; color: white; padding: 6pt; text-align: center; font-weight: bold; margin-bottom: 10pt;">
            PREPARED BY:
        </div>
        
        @if(!empty($preparers))
            @php
                $preparerChunks = array_chunk(array_filter($preparers, function($p) { return !empty($p['name']); }), 3);
            @endphp
            
            @foreach($preparerChunks as $chunk)
                <table class="signatures" style="width: 100%; margin-bottom: 15pt; border-collapse: collapse;">
                    <tr>
                        @foreach($chunk as $preparer)
                            <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 0 10pt;">
                                {{-- Signature Line --}}
                                <div style="border-bottom: 1pt solid #000; height: 30pt; margin-bottom: 5pt; width: 100%;"></div>
                                {{-- Name --}}
                                <div style="text-align: center; font-weight: bold; margin-bottom: 2pt;">
                                    {{ $preparer['name'] }}
                                </div>
                                {{-- Title/Role --}}
                                <div style="text-align: center; font-size: 10pt;">
                                    {{ $preparer['role'] ?? 'Faculty' }}
                                </div>
                            </td>
                        @endforeach
                        
                        {{-- Fill empty cells if less than 3 preparers in this row --}}
                        @for($i = count($chunk); $i < 3; $i++)
                            <td style="width: 33.33%;"></td>
                        @endfor
                    </tr>
                </table>
            @endforeach
        @else
            {{-- Default preparers when no data --}}
            <table class="signatures" style="width: 100%; margin-bottom: 15pt; border-collapse: collapse;">
                <tr>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 0 10pt;">
                        <div style="border-bottom: 1pt solid #000; height: 30pt; margin-bottom: 5pt; width: 100%;"></div>
                        <div style="text-align: center; font-weight: bold; margin-bottom: 2pt;">
                            [Principal Preparer Name]
                        </div>
                        <div style="text-align: center; font-size: 10pt;">
                            Principal Preparer
                        </div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 0 10pt;">
                        <div style="border-bottom: 1pt solid #000; height: 30pt; margin-bottom: 5pt; width: 100%;"></div>
                        <div style="text-align: center; font-weight: bold; margin-bottom: 2pt;">
                            [Library Committee Member]
                        </div>
                        <div style="text-align: center; font-size: 10pt;">
                            Member of the Library Committee
                        </div>
                    </td>
                    <td style="width: 33.33%;"></td>
                </tr>
            </table>
        @endif
    </div>

    {{-- Approvers Section --}}
    <div style="margin-top: 30pt;">
        <table class="signatures" style="width: 100%; border-collapse: collapse;">
            <tr>
                {{-- Reviewer --}}
                <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 0 10pt;">
                    {{-- Label --}}
                    <div style="text-align: left; font-weight: bold; margin-bottom: 8pt;">
                        REVIEWED BY:
                    </div>
                    {{-- Signature Line --}}
                    <div style="border-bottom: 1pt solid #000; height: 30pt; margin-bottom: 5pt; width: 100%;"></div>
                    {{-- Name --}}
                    <div style="text-align: center; font-weight: bold; margin-bottom: 2pt;">
                        {{ $syllabus->reviewer->full_name ?? $approvers['departmentChair'] ?? '[Department Chair Name]' }}
                    </div>
                    {{-- Title --}}
                    <div style="text-align: center; font-size: 10pt; margin-bottom: 2pt;">
                        Department Chair
                    </div>
                    {{-- Department --}}
                    <div style="text-align: center; font-size: 9pt; margin-bottom: 3pt;">
                        {{ $college->name ?? 'Department Name' }}
                    </div>
                    {{-- Date --}}
                    @if($approval_details['dept_chair_reviewed_at'])
                        <div style="text-align: center; font-size: 8pt;">
                            Date: {{ \Carbon\Carbon::parse($approval_details['dept_chair_reviewed_at'])->format('M j, Y') }}
                        </div>
                    @endif
                </td>

                {{-- Recommending Approval --}}
                <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 0 10pt;">
                    {{-- Label --}}
                    <div style="text-align: left; font-weight: bold; margin-bottom: 8pt;">
                        RECOMMENDING APPROVAL:
                    </div>
                    {{-- Signature Line --}}
                    <div style="border-bottom: 1pt solid #000; height: 30pt; margin-bottom: 5pt; width: 100%;"></div>
                    {{-- Name --}}
                    <div style="text-align: center; font-weight: bold; margin-bottom: 2pt;">
                        {{ $syllabus->recommendingApprover->full_name ?? $approvers['associateDean'] ?? '[Associate Dean Name]' }}
                    </div>
                    {{-- Title --}}
                    <div style="text-align: center; font-size: 10pt; margin-bottom: 2pt;">
                        Associate Dean
                    </div>
                    {{-- College --}}
                    <div style="text-align: center; font-size: 9pt; margin-bottom: 3pt;">
                        {{ $college->name ?? 'College Name' }}
                    </div>
                    {{-- Date --}}
                    @if($approval_details['assoc_dean_reviewed_at'])
                        <div style="text-align: center; font-size: 8pt;">
                            Date: {{ \Carbon\Carbon::parse($approval_details['assoc_dean_reviewed_at'])->format('M j, Y') }}
                        </div>
                    @endif
                </td>

                {{-- Final Approval --}}
                <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 0 10pt;">
                    {{-- Label --}}
                    <div style="text-align: left; font-weight: bold; margin-bottom: 8pt;">
                        APPROVED BY:
                    </div>
                    {{-- Signature Line --}}
                    <div style="border-bottom: 1pt solid #000; height: 30pt; margin-bottom: 5pt; width: 100%;"></div>
                    {{-- Name --}}
                    <div style="text-align: center; font-weight: bold; margin-bottom: 2pt;">
                        {{ $syllabus->approver->full_name ?? $approvers['dean'] ?? '[Dean Name]' }}
                    </div>
                    {{-- Title --}}
                    <div style="text-align: center; font-size: 10pt; margin-bottom: 2pt;">
                        Dean
                    </div>
                    {{-- College --}}
                    <div style="text-align: center; font-size: 9pt; margin-bottom: 3pt;">
                        {{ $college->name ?? 'College Name' }}
                    </div>
                    {{-- Date --}}
                    @if($approval_details['dean_approved_at'])
                        <div style="text-align: center; font-size: 8pt;">
                            Date: {{ \Carbon\Carbon::parse($approval_details['dean_approved_at'])->format('M j, Y') }}
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>
