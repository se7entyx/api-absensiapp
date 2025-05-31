<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Kyslik\ColumnSortable\Sortable;

class Presensi extends Model
{
    use Sortable;
    protected $fillable = ['user_id', 'type', 'status', 'lat', 'long', 'check_out', 'kantor_id'];
    protected $table = 'presensi';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kantor()
    {
        return $this->belongsTo(Kantor::class);
    }

    public $sortable = [
        'type',
        'created_at',
        'status',
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
            isset($filters['start_date']) && isset($filters['end_date']),
            function ($query) use ($filters) {
                $start = Carbon::parse($filters['start_date'])->startOfDay();
                $end = Carbon::parse($filters['end_date'])->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            }
        );

        $query->when(
            $filters['type'] ?? false,
            fn($query, $type) =>
            $type === 'dinas'
                ? $query->whereIn('type', ['dinas-ipg', 'dinas-luar'])
                : $query->where('type', $type)
        );

        // Filter by kantor_id
        $query->when(
            $filters['kantor_id'] ?? false,
            fn($query, $kantor_id) =>
            $query->where('kantor_id', $kantor_id)
        );
    }
}
