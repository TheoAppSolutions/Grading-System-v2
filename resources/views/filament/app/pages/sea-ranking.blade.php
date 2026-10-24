<div style="display: flex; flex-direction: column; gap: 16px;">
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 16px 18px; border: 1px solid rgba(148, 163, 184, .25); border-radius: 12px; background: rgba(148, 163, 184, .08);">
        <div>
            <div style="font-size: 13px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; opacity: .7;">Subject Excellence Award</div>
            <div style="margin-top: 4px; font-size: 20px; font-weight: 700;">{{ $subject->subject_title }}</div>
        </div>
        <div style="padding: 7px 10px; border-radius: 999px; background: rgba(245, 158, 11, .16); color: #f59e0b; font-size: 12px; font-weight: 700; white-space: nowrap;">90+ required</div>
    </div>

    <p style="margin: 0; font-size: 13px; line-height: 1.5; opacity: .72;">
        Select one eligible student per subject.
        @if ($isPhysicalEducation)
            P.E. allows one male and one female selection.
        @endif
        The selection is saved to the awards table.
    </p>

    @if ($ranking->isEmpty())
        <div style="padding: 24px; border: 1px dashed rgba(148, 163, 184, .4); border-radius: 10px; text-align: center; font-size: 14px; opacity: .72;">No completed final averages are available yet.</div>
    @else
        <div style="overflow: hidden; border: 1px solid rgba(148, 163, 184, .25); border-radius: 12px;">
            <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                <thead style="background: rgba(148, 163, 184, .10);">
                    <tr>
                        <th style="width: 64px; padding: 12px 14px; font-weight: 700;">Rank</th>
                        <th style="padding: 12px 14px; font-weight: 700;">Student</th>
                        <th style="width: 84px; padding: 12px 14px; font-weight: 700;">Gender</th>
                        <th style="width: 130px; padding: 12px 14px; text-align: right; font-weight: 700;">Final average</th>
                        <th style="width: 132px; padding: 12px 14px; text-align: right; font-weight: 700;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ranking->take(10) as $entry)
                        <tr style="border-top: 1px solid rgba(148, 163, 184, .18);">
                            <td style="padding: 13px 14px; opacity: .65;">{{ $loop->iteration }}</td>
                            <td style="padding: 13px 14px; font-weight: 600;">
                                {{ $entry['student']->last_name }}, {{ $entry['student']->first_name }}
                            </td>
                            <td style="padding: 13px 14px;">{{ $entry['gender'] }}</td>
                            <td style="padding: 13px 14px; text-align: right; font-variant-numeric: tabular-nums; font-weight: 700;">
                                <span>{{ number_format($entry['score'], 2) }}</span>
                                @if ($entry['eligible'])
                                    <span style="display: inline-block; margin-left: 6px; padding: 3px 7px; border-radius: 999px; background: rgba(34, 197, 94, .14); color: #22c55e; font-size: 11px; font-weight: 700;">Eligible</span>
                                @else
                                    <span style="display: inline-block; margin-left: 6px; padding: 3px 7px; border-radius: 999px; background: rgba(148, 163, 184, .14); opacity: .65; font-size: 11px; font-weight: 600;">Below 90</span>
                                @endif
                            </td>
                            <td style="padding: 13px 14px; text-align: right;">
                                @if ($entry['selected'])
                                    <span style="display: inline-flex; align-items: center; gap: 5px; padding: 6px 9px; border-radius: 7px; background: rgba(34, 197, 94, .14); color: #22c55e; font-size: 12px; font-weight: 700;">✓ Selected</span>
                                @elseif ($entry['eligible'])
                                    <button type="button" wire:click="selectSubjectExcellenceAward({{ (int) $entry['student_id'] }})" wire:loading.attr="disabled" wire:target="selectSubjectExcellenceAward" style="padding: 7px 11px; border: 1px solid rgba(245, 158, 11, .55); border-radius: 7px; background: rgba(245, 158, 11, .14); color: #f59e0b; cursor: pointer; font-size: 12px; font-weight: 700;">Select</button>
                                @else
                                    <span style="font-size: 12px; opacity: .45;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    @endif
</div>
