 <tr style='height: 17pt'>
    <td colspan=8 class=report-card__months>Semester</td>
    <td colspan=4 class=report-card__months>1</td>
    <td colspan=4 class=report-card__months>2</td>
    <td colspan=4 class=report-card__months>Total</td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
<tr style='height: 17pt'>
    <td colspan=8 class=report-card__attendance-label>School Days</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, 'schooldays.1') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, 'schooldays.2') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, 'schooldays.total', 0) }}</td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
<tr style='height: 17pt'>
    <td colspan=8 class=report-card__attendance-label>Days Present</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, 'present.1') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, 'present.2') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, 'present.total', 0) }}</td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
<tr style='height: 17pt'>
    <td colspan=8 class=report-card__attendance-label>Days Absent</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, 'absent.1') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, 'absent.2') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, 'absent.total', 0) }}</td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
<tr style='height: 17pt'>
    <td colspan=8 class=report-card__attendance-label>Days Tardy</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, 'tardy.1') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, 'tardy.2') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, 'tardy.total', 0) }}</td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>