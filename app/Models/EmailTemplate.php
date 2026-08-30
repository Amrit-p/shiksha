<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'subject',
        'body',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function render(array $variables = [], array $rawKeys = ['products']): array
    {
        $subject = $this->subject;
        $body = $this->body;

        foreach ($variables as $key => $value) {
            if (! is_scalar($value) && $value !== null) {
                continue;
            }

            $placeholder = '{{'.$key.'}}';
            $stringValue = (string) ($value ?? '');
            $subject = str_replace($placeholder, strip_tags($stringValue), $subject);
            $bodyValue = in_array($key, $rawKeys, true) ? $stringValue : e($stringValue);
            $body = str_replace($placeholder, $bodyValue, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }
}
