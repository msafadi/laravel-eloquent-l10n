<?php

namespace Safadi\Eloquent\L10n;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Safadi\Eloquent\L10n\Console\L10nTableCommand;

class EloquentL10nServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/eloquent-l10n.php', 'eloquent-l10n');
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        Builder::macro('useTable', function ($table) {
            $this->getModel()->setTable($table);           
            return $this;
        });

        Builder::macro('useLocale', function ($locale) {
            $this->getModel()->setLocale($locale);
            return $this;
        });

        if (method_exists($this->app, 'runningInConsole') && $this->app->runningInConsole()) {
            $this->commands([
                L10nTableCommand::class,
            ]);
        }

        
        $this->publishes([
            __DIR__ . '/../config/eloquent-l10n.php' => config_path('eloquent-l10n.php'),
        ]);
    }

}