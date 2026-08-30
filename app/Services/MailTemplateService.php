<?php

namespace App\Services;

use App\Models\EmailTemplate;

class MailTemplateService
{
    public function build(string $slug, array $variables = []): ?array
    {
        $template = EmailTemplate::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            return null;
        }

        return $template->render($variables);
    }
}
