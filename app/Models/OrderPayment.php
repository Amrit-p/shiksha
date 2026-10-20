<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPayment extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_REVERSED = 'reversed';

    /** Modes a salesman/admin can pick. "other" is only used for backfilled rows. */
    public const MODES = [
        'cash' => 'Cash',
        'upi' => 'UPI',
        'cheque' => 'Cheque',
        'bank_transfer' => 'Bank Transfer',
    ];

    protected $fillable = [
        'order_id',
        'user_id',
        'recorded_by',
        'amount',
        'paid_on',
        'mode',
        'reference',
        'note',
        'proof_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_reason',
        'is_backfill',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_on' => 'date',
            'reviewed_at' => 'datetime',
            'is_backfill' => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function salesman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_VERIFIED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /** Payments that reserve part of the order balance: verified or still awaiting verification. */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_VERIFIED]);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function modeLabel(): string
    {
        return self::MODES[$this->mode] ?? ucfirst(str_replace('_', ' ', (string) $this->mode));
    }

    /** Bootstrap badge class for the status. */
    public function statusBadge(): string
    {
        return match ($this->status) {
            self::STATUS_VERIFIED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_REVERSED => 'secondary',
            default => 'warning',
        };
    }
}
