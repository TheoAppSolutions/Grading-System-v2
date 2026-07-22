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
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, '1.schooldays') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, '2.schooldays') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, '1.schooldays', 0) + data_get($attendance, '2.schooldays', 0) }}</td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
<tr style='height: 17pt'>
    <td colspan=8 class=report-card__attendance-label>Days Present</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, '1.present') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, '2.present') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, '1.present', 0) + data_get($attendance, '2.present', 0) }}</td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
<tr style='height: 17pt'>
    <td colspan=8 class=report-card__attendance-label>Days Absent</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, '1.absent') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, '2.absent') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, '1.absent', 0) + data_get($attendance, '2.absent', 0) }}</td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>
<tr style='height: 17pt'>
    <td colspan=8 class=report-card__attendance-label>Days Tardy</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, '1.tardy') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, '2.tardy') }}</td>
    <td colspan=4 class=report-card__attendance>{{ data_get($attendance, '1.tardy', 0) + data_get($attendance, '2.tardy', 0) }}</td>
    <td class=report-card__blank></td>
    <td class=report-card__blank></td>
</tr>