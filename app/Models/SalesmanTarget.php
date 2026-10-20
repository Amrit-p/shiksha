<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesmanTarget extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'sales_target',
        'collection_target',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'sales_target' => 'decimal:2',
            'collection_target' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** The override for that month if one exists, otherwise the salesman's standing default. */
    public static function forMonth(int $userId, CarbonInterface $month): ?self
    {
        return static::query()
            ->where('user_id', $userId)
            ->where(fn ($q) => $q->whereNull('month')->orWhereDate('month', $month->copy()->startOfMonth()->toDateString()))
            ->orderByRaw('month IS NULL')
            ->first();
    }

    /** Same as forMonth() for many salesmen at once, keyed by user_id. */
    public static function mapForMonth(array $userIds, CarbonInterface $month): array
    {
        return static::query()
            ->whereIn('user_id', $userIds)
            ->where(fn ($q) => $q->whereNull('month')->orWhereDate('month', $month->copy()->startOfMonth()->toDateString()))
            ->orderByRaw('month IS NULL DESC') // defaults first, so overrides win below
            ->get()
            ->keyBy('user_id')
            ->all();
    }
}
