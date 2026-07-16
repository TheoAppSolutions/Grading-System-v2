{{--
    Reusable blank spacer row.

    Usage:
        @include('reports.hcgs._snippets.blank_row')                 // defaults to 62 columns
        @include('reports.hcgs._snippets.blank_row', ['cols' => 20])  // custom column count
--}}
<tr style='mso-height-source:userset;{{ $height }}'>
    @for ($i = 0; $i < ($cols ?? 62); $i++)
        <td class=blank></td>
    @endfor
</tr>