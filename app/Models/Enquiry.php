<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enquiry extends Model
{
    protected $fillable = [
        'enquiry_number',
        'name',
        'email',
        'phone',
        'company',
        'address',
        'city',
        'state',
        'pin_code',
        'message',
        'enquiry_status_id',
        'source',
        'assigned_to',
        'internal_notes',
        'ip_address',
        'user_agent',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(EnquiryStatus::class, 'enquiry_status_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function items(): HasMany
    {
        return $this->hasMany(EnquiryItem::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(EnquiryActivity::class)->latest();
    }

    public function totalQty(): int
    {
        return (int) $this->items->sum('qty');
    }
}
