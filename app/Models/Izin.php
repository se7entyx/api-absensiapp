<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Kyslik\ColumnSortable\Sortable;

class Izin extends Model
{
    use Sortable;
    protected $table = 'izin';
    protected $fillable = ['user_id', 'keterangan', 'status', 'approved_hrd'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function hrd()
    {
        return $this->belongsTo(User::class,'approved_hrd');
    }
    public $sortable = [
        'waktu_kembali',
        'created_at',
        'waktu_keluar',
        'user.name'
    ];
    public function scopeFilter(Builder $query, array $filters): void
    {
        // Search by user name
        $query->when(
            $filters['search'] ?? false,
            fn($query, $search) =>
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
        );

        $query->when(
            isset($filters['start_keluar']) && isset($filters['end_keluar']),
            function ($query) use ($filters) {
                $start = Carbon::parse($filters['start_keluar'])->startOfDay();
                $end = Carbon::parse($filters['end_keluar'])->endOfDay();
                $query->whereBetween('waktu_keluar', [$start, $end]);
            }
        );

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
