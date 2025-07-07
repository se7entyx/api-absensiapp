<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;

class JamKerjaExport implements FromView, WithTitle, WithColumnWidths
{
    protected $filters;
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $start = $this->filters['start_date'] ?? null;
        $end = $this->filters['end_date'] ?? null;

        // Ambil presensi dalam rentang tanggal (kalau ada)
        $users = User::with(['presensi' => function ($query) use ($start, $end) {
            if ($start) {
                $query->whereDate('created_at', '>=', $start);
            }
            if ($end) {
                $query->whereDate('created_at', '<=', $end);
            }
        }, 'department'])->get();
        // $users = User::get();

        return view('presensi-exports-jam-kerja', [
            'users' => $users
        ]);
    }

    public function title(): string
    {
        return 'Jam Kerja';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Nama
            'B' => 15, // NIP
            'C' => 20, // Department
            'D' => 20, // Check In
        ];
    }
}
