<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'show_in_footer',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'show_in_footer' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeFooter($query)
    {
        return $query->active()->where('show_in_footer', true);
    }
}
