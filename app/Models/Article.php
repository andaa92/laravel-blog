<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = ['category_id', 'title', 'slug', 'desc', 'img', 'views', 'status', 'publish_date'];

    //relasi ke categories
    public function Category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
