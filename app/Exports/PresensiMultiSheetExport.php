<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PresensiMultiSheetExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new PresensiExport($this->filters),   // Sheet 1
            new JamKerjaExport($this->filters),             // Sheet 2
        ];
    }
}

