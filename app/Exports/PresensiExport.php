<?php

namespace App\Exports;

use App\Models\Presensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;

class PresensiExport implements FromView, WithColumnWidths, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $presensis = Presensi::with('user', 'kantor')
            ->where('status','success')
            ->filter($this->filters)
            ->latest()
            ->get();

        return view('presensi-exports', [
            'presensis' => $presensis
        ]);
    }
    public function title(): string
    {
        return 'Rekap Presensi';
    }
    public function columnWidths(): array
    {
        return [
            'A' => 20, // Nama
            'B' => 15, // NIP
            'C' => 20, // Department
            'D' => 18, // Check In
            'E' => 18, // Check Out
            'F' => 18, // Terlambat
            'G' => 20, // Lama Bekerja
        ];
    }
}
