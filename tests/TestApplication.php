<?php

namespace Safadi\Tests;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Safadi\Eloquent\L10n\EloquentL10nServiceProvider;

class TestApplication extends Container
{
    protected $providers = [
        EventServiceProvider::class,
        DatabaseServiceProvider::class,
        EloquentL10nServiceProvider::class,
    ];

    protected $services;

    public function register()
    {
        $this->bind('config', function() {
            return new Repository(require __DIR__ . '/../config/eloquent-l10n.php');
        });

        $this->singleton('migration.creator', function ($app) {
            return new MigrationCreator(new Filesystem, $app->basePath('stubs'));
        }); 

        $this->instance('app', $this);
        foreach ($this->providers as $name) {
            $provider = new $name($this);
            if (method_exists($provider, 'register')) {
                $provider->register();
            }
            $this->services[] = $provider;
        }
    }

    public function boot()
    {
        $this->register();
        foreach ($this->services as $service) {
            if (method_exists($service, 'boot')) {
                $service->boot();
            }
        }
    }

    public function basePath()
    {
        return __DIR__;
    }

    public function databasePath()
    {
        return __DIR__ . '/migrations';
    }
}