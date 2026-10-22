<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['title', 'btn_text', 'btn_link', 'image', 'status', 'starts_at', 'ends_at', 'sort_order', 'show_in_popup'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'show_in_popup' => 'boolean',
        ];
    }

    /** Active banners whose date window (inclusive, either end optional) covers today. */
    public function scopeCurrent($query)
    {
        $today = now()->toDateString();

        return $query->where('status', 1)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhereDate('starts_at', '<=', $today))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', $today))
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }

    public function isCurrent(): bool
    {
        $today = now()->startOfDay();

        return (int) $this->status === 1
            && (! $this->starts_at || $this->starts_at->lte($today))
            && (! $this->ends_at || $this->ends_at->gte($today));
    }
}
