<?php

namespace App\Exports;

use App\Models\Presensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PresensiExport implements FromView
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $presensis = Presensi::with('user', 'kantor')
            ->filter($this->filters)
            ->latest()
            ->get();

        return view('presensi-exports', [
            'presensis' => $presensis
        ]);
    }
}
