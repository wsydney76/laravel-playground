<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use App\Models\Homepage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Livewire\Blaze\Blaze;

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
        $this->configureDefaults();

        // Share the singleton Homepage with every view (including Blade components),
        // so brand, footer, and the layout can access sitename and copyright.
        // Wrapped in try/catch so artisan commands work before migrations have run.
        try {
            View::share('homepage', Homepage::getSingleton());
        } catch (\Throwable) {
            View::share('homepage', null);
        }

        // Blaze::debug();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(app()->isProduction());

        Password::defaults(
            fn(): ?Password => app()->isProduction()
                ? Password::min(12)->mixedCase()->letters()->numbers()->symbols()->uncompromised()
                : null,
        );

        //Model::unguard();
        Model::shouldBeStrict();
        Model::automaticallyEagerLoadRelationships();
    }
}
