<?php

namespace Azuriom\Plugin\AuthSkinSlim\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Plugin\AuthSkinSlim\Http\Middleware\MergeSkinSlimIntoAuthApiResponse;
use Azuriom\Plugin\AuthSkinSlim\Http\Middleware\RelaxSkinApiDimensionsForValidation;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;

class AuthSkinSlimServiceProvider extends BasePluginServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom($this->pluginPath('database/migrations'));
        $this->loadViewsFrom($this->pluginResourcePath('views'), $this->plugin->id);
        $this->loadTranslationsFrom($this->pluginResourcePath('lang'), $this->plugin->id);

        $kernel = $this->app->make(Kernel::class);
        $kernel->appendMiddlewareToGroup('api', MergeSkinSlimIntoAuthApiResponse::class);
        $kernel->appendMiddlewareToGroup('api', RelaxSkinApiDimensionsForValidation::class);
        $kernel->appendMiddlewareToGroup('web', RelaxSkinApiDimensionsForValidation::class);

        $this->registerAdminRoutes();
        $this->registerAdminNavigation();
    }

    protected function adminNavigation(): array
    {
        return [
            'auth-skin-slim' => [
                'name' => trans('auth-skin-slim::admin.nav'),
                'icon' => 'bi bi-aspect-ratio',
                'route' => 'auth-skin-slim.admin.settings',
                'permission' => 'admin.plugins',
            ],
        ];
    }

    protected function registerAdminRoutes(): void
    {
        if (! is_installed()) {
            return;
        }

        Route::middleware('admin-access')
            ->prefix('admin/'.$this->plugin->id)
            ->name($this->plugin->id.'.admin.')
            ->group($this->pluginPath('routes/admin.php'));
    }
}
