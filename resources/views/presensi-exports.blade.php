<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Department</th>
            <th>Tipe</th>
            <th>Kantor</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($presensis as $presensi)
            <tr>
                <td>{{ $presensi->user->name ?? '-' }}</td>
                <td>{{ $presensi->user->department->name ?? '-' }}</td>
                <td>{{ $presensi->type ?? '-' }}</td>
                <td>{{ $presensi->kantor->name ?? '-' }}</td>
                <td>{{ $presensi->created_at ?? '-' }}</td>
                <td>{{ $presensi->check_out ?? '-' }}</td>
                <td>{{ $presensi->lat ?? '-' }}</td>
                <td>{{ $presensi->long ?? '-' }}</td>
                <td>{{ $presensi->status ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>