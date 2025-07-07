<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIP</th>
            <th>Department</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Terlambat</th>
            <th>Lama Bekerja</th>
        </tr>
    </thead>
    <tbody>
        @foreach($presensis as $presensi)
        @php
        $checkIn = \Carbon\Carbon::parse($presensi->created_at);
        $checkOut = $presensi->check_out ? \Carbon\Carbon::parse($presensi->check_out) : null;

        // Patokan jam 07:30
        $limit = $checkIn->copy()->setTime(7, 30);

        // Hitung Terlambat
        if ($checkIn->gt($limit)) {
        $diff = $checkIn->diff($limit);
        $terlambat = $diff->h . ' jam, ' . $diff->i . ' menit';
        } else {
        $terlambat = 'Tepat Waktu';
        }

        // Hitung Lama Bekerja
        if ($checkOut) {
        $workDiff = $checkOut->diff($checkIn);
        $lamaKerja = $workDiff->h . ' jam, ' . $workDiff->i . ' menit';
        } else {
        $lamaKerja = '-';
        }
        @endphp
        @if ($presensi->status != 'failed')
        <tr>
            <td>{{ $presensi->user->name ?? '-' }}</td>
            <td>{{ $presensi->user->uid ?? '-' }}</td>
            <td>{{ $presensi->user->department->name ?? '-' }}</td>
            <td>{{ $presensi->created_at ?? '-' }}</td>
            <td>{{ $presensi->check_out ?? '-' }}</td>
            <td>{{ $terlambat }}</td>
            <td>{{ $lamaKerja }}</td>
        </tr>
        @endif
        @endforeach
    </tbody>
</table>