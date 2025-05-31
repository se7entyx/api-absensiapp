<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;

class Department extends Model {
    use Sortable;
    protected $fillable = ['user_id', 'name','status'];

    public function leader() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function users() : HasMany
    {
        return $this->hasMany(User::class);
    }

    protected $sortable = ['status'];

    public function scopeFilter(Builder $query, array $filters): void{
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        });
    }
}
