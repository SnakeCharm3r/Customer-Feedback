<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        Paginator::useBootstrapFive();

        $settings = null;

        try {
            if (Schema::hasTable('system_settings')) {
                $settings = SystemSetting::current();

                config([
                    'app.name' => $settings->appDisplayName(),
                    'mail.from.address' => $settings->mail_from_address,
                    'mail.from.name' => $settings->mail_from_name,
                    'auth.login_max_attempts' => $settings->login_max_attempts,
                    'auth.login_lockout_minutes' => $settings->login_lockout_minutes,
                ]);
            }
        } catch (\Throwable) {
            $settings = null;
        }

        View::share('systemSettings', $settings);
    }
}
