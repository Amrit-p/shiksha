<?php

namespace App\Services;

use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Sends WhatsApp messages through Meta's WhatsApp Cloud API. Credentials and the
 * recipient admin number live in Website Settings (group "whatsapp").
 */
class WhatsAppService
{
    protected const GRAPH_URL = 'https://graph.facebook.com/v21.0';

    public function isConfigured(): bool
    {
        return $this->adminNumber()
            && WebsiteSetting::getValue('whatsapp_phone_number_id')
            && WebsiteSetting::getValue('whatsapp_access_token');
    }

    /** Admin number to notify, digits only with country code (e.g. 919876543210). */
    public function adminNumber(): ?string
    {
        $digits = ltrim(preg_replace('/\D+/', '', (string) WebsiteSetting::getValue('whatsapp_notification_number')), '0');

        return $digits ?: null;
    }

    /**
     * Notify the admin number. With a template name set, sends that approved template
     * with $params as its body variables {{1}}, {{2}}, ... (needed for business-initiated
     * messages). Without one, sends $fallbackText as plain text, which Meta only delivers
     * if the admin messaged the business number in the last 24 hours.
     *
     * Throws on API errors; callers decide how to handle them.
     */
    public function notifyAdmin(array $params, string $fallbackText): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        $template = trim((string) WebsiteSetting::getValue('whatsapp_template_name'));

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $this->adminNumber(),
        ];

        if ($template !== '') {
            $payload['type'] = 'template';
            $payload['template'] = [
                'name' => $template,
                'language' => ['code' => WebsiteSetting::getValue('whatsapp_template_language') ?: 'en'],
                'components' => [[
                    'type' => 'body',
                    'parameters' => array_map(
                        fn ($value) => ['type' => 'text', 'text' => $this->templateText($value)],
                        array_values($params)
                    ),
                ]],
            ];
        } else {
            $payload['type'] = 'text';
            $payload['text'] = ['preview_url' => false, 'body' => Str::limit($fallbackText, 4000)];
        }

        Http::withToken((string) WebsiteSetting::getValue('whatsapp_access_token'))
            ->timeout(10)
            ->post(self::GRAPH_URL.'/'.WebsiteSetting::getValue('whatsapp_phone_number_id').'/messages', $payload)
            ->throw();
    }

    /** Template variables may not contain newlines, tabs or runs of spaces, and may not be empty. */
    protected function templateText(mixed $value): string
    {
        $text = trim(preg_replace('/\s+/', ' ', (string) $value));

        return Str::limit($text !== '' ? $text : '-', 900);
    }
}
