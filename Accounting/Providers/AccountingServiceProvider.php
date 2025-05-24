<?php
namespace Modules\Accounting\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Accounting\Services\AccountingService;
use Modules\Accounting\Services\ReportService;

class AccountingServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Accounting';
    protected string $moduleNameLower = 'accounting';

    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->registerHelpers();
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        
        // Register services
        $this->app->singleton(AccountingService::class, function ($app) {
            return new AccountingService();
        });
        
        $this->app->singleton(ReportService::class, function ($app) {
            return new ReportService();
        });
    }

    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'));
        }
    }

    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower.'.php')
        ], 'config');
        
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), 
            $this->moduleNameLower
        );
    }

    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->moduleNameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);

        $componentNamespace = str_replace('/', '\\', config('modules.namespace').'\\'.$this->moduleName.'\\'.config('modules.paths.generator.component-class.path'));
        Blade::componentNamespace($componentNamespace, $this->moduleNameLower);
    }

    protected function registerHelpers(): void
    {
        // Load helper functions
        if (file_exists(module_path($this->moduleName, 'Helpers/AccountingHelper.php'))) {
            require_once module_path($this->moduleName, 'Helpers/AccountingHelper.php');
        }
    }

    public function provides(): array
    {
        return [
            AccountingService::class,
            ReportService::class,
        ];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }
        return $paths;
    }
}
