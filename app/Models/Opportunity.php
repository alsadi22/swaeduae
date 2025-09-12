<?php

namespace App\Models;

use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Opportunity extends Model
{
    use HasFactory;

    // If your table uses guarded/primary keys differently, adjust here as needed.
    protected $guarded = [];
    /** Query scope: published rows if columns exist */
    

    /** Only published rows if columns exist */
    public function scopePublished($q){
        $t = $q->getModel()->getTable();
        if (Schema::hasColumn($t,'is_published')) return $q->where('is_published',1);
        if (Schema::hasColumn($t,'published_at')) return $q->whereNotNull('published_at');
        return $q;
    }
}
