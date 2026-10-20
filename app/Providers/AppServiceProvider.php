<?php

namespace App\Providers;

use App\Models\TrustBadge;
use App\Services\SmtpConfigurator;
use App\View\Composers\ShopLayoutComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        View::composer('shop.*', ShopLayoutComposer::class);
        View::composer('front.partials.trust_badges', function ($view) {
            $view->with('trustBadges', TrustBadge::active()->get());
        });

        // Use the SMTP settings saved in admin for every email (password reset, invites, ...),
        // not only where a service calls SmtpConfigurator explicitly.
        $this->app->resolving('mail.manager', function () {
            try {
                if (Schema::hasTable('smtp_settings')) {
                    app(SmtpConfigurator::class)->apply();
                }
            } catch (\Throwable $e) {
                Log::warning('SMTP settings could not be applied', ['error' => $e->getMessage()]);
            }
        });
    }
}
