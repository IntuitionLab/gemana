<?php

namespace App\Providers;

use App\Core\ModuleLoader;
use App\Core\ThemeManager;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register core singletons into the container.
     *
     * Both the string alias ('modules') and the class name (ModuleLoader::class)
     * must resolve to the SAME singleton instance — otherwise constructor-injected
     * ModuleLoader instances won't have discover() called on them.
     */
    public function register(): void
    {
        // Build the ModuleLoader singleton once, bound by class name
        $this->app->singleton(ModuleLoader::class, function () {
            $loader = new ModuleLoader;
            $loader->discover();

            return $loader;
        });

        // String alias points to the same singleton — not a new instance
        $this->app->alias(ModuleLoader::class, 'modules');

        // ThemeManager — same pattern
        $this->app->singleton(ThemeManager::class, function () {
            return new ThemeManager;
        });

        $this->app->alias(ThemeManager::class, 'theme');
    }

    /**
     * Boot the Core — register theme views, boot enabled modules.
     */
    public function boot(): void
    {
        // Boot the theme first so module views can override theme views if needed
        app('theme')->boot();

        // Boot all enabled modules (registers their ServiceProviders)
        app('modules')->boot();

        // Share theme + module data with all views
        view()->composer('*', function ($view) {
            $view->with('activeTheme', app('theme')->getActive());
            $view->with('enabledModules', array_keys(app('modules')->enabled()));
        });
    }
}
