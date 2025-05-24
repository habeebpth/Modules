<?php

namespace Modules\Synktime\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Synktime\Services\DepartmentSyncService;
use Modules\Synktime\Services\AreaSyncService;
use Modules\Synktime\Services\EmployeeSyncService;

class SynktimeServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('Synktime', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        // Register services
        $this->app->singleton(DepartmentSyncService::class, function ($app) {
            return new DepartmentSyncService();
        });

        $this->app->singleton(AreaSyncService::class, function ($app) {
            return new AreaSyncService();
        });

        $this->app->singleton(EmployeeSyncService::class, function ($app) {
            return new EmployeeSyncService();
        });
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('Synktime', 'Config/config.php') => config_path('synktime.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Synktime', 'Config/config.php'), 'synktime'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/synktime');

        $sourcePath = module_path('Synktime', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/synktime';
        }, \Config::get('view.paths')), [$sourcePath]), 'synktime');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/synktime');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'synktime');
        } else {
            $this->loadTranslationsFrom(module_path('Synktime', 'Resources/lang'), 'synktime');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    protected function registerFactories()
    {
        if ($this->app->runningInConsole()) {
            $factoriesPath = __DIR__ . '/../Database/factories';

            if (class_exists(\Illuminate\Database\Eloquent\Factories\Factory::class) && is_dir($factoriesPath)) {
                // Laravel 8+ Factory loader
                \Illuminate\Database\Eloquent\Factories\Factory::guessFactoryNamesUsing(function (string $modelName) {
                    return 'Modules\\Synktime\\Database\\Factories\\' . class_basename($modelName) . 'Factory';
                });
            }
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            DepartmentSyncService::class,
            AreaSyncService::class,
            EmployeeSyncService::class,
        ];
    }
}
