<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait SearchSort
{
    /**
     * Scope for generic search by name/email/title fields (if columns exist)
     *
     * @param  Builder  $query
     * @param  string|null  $term
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!empty($term)) {
            $like = "%{$term}%";
            $table = $this->getTable();
            $cols = Schema::getColumnListing($table);
            $candidates = array_values(array_intersect($cols, ['name','email','title']));

            if (!empty($candidates)) {
                $query->where(function($q) use ($like, $candidates) {
                    foreach ($candidates as $col) {
                        $q->orWhere($col, 'LIKE', $like);
                    }
                });
            }
        }
        return $query;
    }

    /**
     * Scope for generic sorting
     *
     * @param  Builder  $query
     * @param  string|null  $sort
     * @return Builder
     */
    public function scopeSort(Builder $query, ?string $sort): Builder
    {
        $table = $this->getTable();
        $cols = Schema::getColumnListing($table);

        switch ($sort) {
            case 'oldest':
                if (in_array('created_at', $cols, true)) {
                    $query->orderBy('created_at', 'asc');
                }
                break;
            case 'name_asc':
                if (in_array('name', $cols, true)) {
                    $query->orderBy('name', 'asc');
                }
                break;
            case 'name_desc':
                if (in_array('name', $cols, true)) {
                    $query->orderBy('name', 'desc');
                }
                break;
            default:
                if (in_array('created_at', $cols, true)) {
                    $query->orderBy('created_at', 'desc');
                }
        }

        return $query;
    }
}
