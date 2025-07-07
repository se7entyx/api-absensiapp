<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIP</th>
            <th>Department</th>
            <th>Jumlah Kehadiran</th>
            <th>Total Jam Kerja</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        @php
        $totalSeconds = 0;
        $kehadiran = 0;
        foreach ($user->presensi as $presensi) {
            if ($presensi->check_out) {
                $checkIn = \Carbon\Carbon::parse($presensi->created_at);
                $checkOut = \Carbon\Carbon::parse($presensi->check_out);
                $totalSeconds += $checkOut->diffInSeconds($checkIn);
                $kehadiran++;
            }
        }

        $totalSeconds = abs($totalSeconds);
        $totalHours = floor($totalSeconds / 3600);
        $remainingMinutes = floor(($totalSeconds % 3600) / 60);
        @endphp
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->uid }}</td>
            <td>{{ $user->department->name ?? '-' }}</td>
            <td>{{ $kehadiran }}</td>
            <td>{{ $totalHours }} jam, {{ $remainingMinutes }} menit</td>
        </tr>
        @endforeach
    </tbody>
</table>