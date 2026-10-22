<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustBadge extends Model
{
    protected $fillable = ['title', 'subtitle', 'icon', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
