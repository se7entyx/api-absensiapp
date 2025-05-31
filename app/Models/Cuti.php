<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Kyslik\ColumnSortable\Sortable;

class Cuti extends Model
{
    use Sortable;
    protected $fillable = ['user_id', 'status', 'keterangan', 'start_date', 'end_date', 'jumlah_hari'];
    protected $table = 'cuti';
    public $sortable = [
        'start_date',
        'end_date',
        'created_at',
        'user.name',
        'jumlah_hari'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeFilter(Builder $query, array $filters): void
    {
        // Search by user name
        $query->when(
            $filters['search'] ?? false,
            fn($query, $search) =>
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
        );

        // Filter by waktu_keluar (datetime) range
        $query->when(
            isset($filters['start_keluar']) && isset($filters['end_keluar']),
            function ($query) use ($filters) {
                $startKeluar = Carbon::parse($filters['start_keluar'])->startOfDay();
                $endKeluar = Carbon::parse($filters['end_keluar'])->endOfDay();

                $query->where(function ($query) use ($startKeluar, $endKeluar) {
                    $query->whereDate('start_date', '<=', $endKeluar)
                        ->whereDate('end_date', '>=', $startKeluar);
                });
            }
        );
        // Filter by waktu_dibuat / created_at (timestamp)
        $query->when(
            isset($filters['start_created']) && isset($filters['end_created']),
            function ($query) use ($filters) {
                $start = Carbon::parse($filters['start_created'])->startOfDay();
                $end = Carbon::parse($filters['end_created'])->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            }
        );
    }
}
