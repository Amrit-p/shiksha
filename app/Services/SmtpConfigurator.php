<?php

namespace App\Services;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;

class SmtpConfigurator
{
    public function apply(?SmtpSetting $setting = null): bool
    {
        $setting = $setting ?: SmtpSetting::current();

        if (! $setting || ! $setting->is_active || ! $setting->host || ! $setting->from_email) {
            return false;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $setting->host);
        Config::set('mail.mailers.smtp.port', $setting->port ?: 587);
        Config::set('mail.mailers.smtp.username', $setting->username);
        Config::set('mail.mailers.smtp.password', $setting->getDecryptedPassword());
        Config::set('mail.mailers.smtp.encryption', $setting->encryption ?: null);
        Config::set('mail.from.address', $setting->from_email);
        Config::set('mail.from.name', $setting->from_name ?: config('app.name'));

        return true;
    }
}
