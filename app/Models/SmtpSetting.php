<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SmtpSetting extends Model
{
    protected $fillable = [
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_email',
        'from_name',
        'admin_email',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'port' => 'integer',
        ];
    }

    public function setPasswordAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $this->attributes['password'] = Crypt::encryptString($value);
    }

    public function getDecryptedPassword(): ?string
    {
        if (empty($this->attributes['password'] ?? null)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->attributes['password']);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function current(): ?self
    {
        return static::query()->latest('id')->first();
    }
}
