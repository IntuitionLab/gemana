<?php

namespace App\Core;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\View\FileViewFinder;

class ThemeManager
{
    protected string $themesPath;

    protected string $activeTheme;

    public function __construct()
    {
        $this->themesPath = base_path('themes');
        $this->activeTheme = config('gemana.active_theme', 'gemana-default');
    }

    /**
     * Register the active theme's view path with Laravel's view finder.
     * Views in the active theme take priority over default resources/views.
     */
    public function boot(): void
    {
        $themePath = $this->getActivePath();

        if (! File::isDirectory($themePath)) {
            Log::warning("Gemana: Active theme [{$this->activeTheme}] not found at [{$themePath}]. Falling back to default views.");

            return;
        }

        /** @var FileViewFinder $finder */
        $finder = app('view.finder');
        $finder->prependNamespace('theme', $themePath);
        $finder->addLocation($themePath.'/views');
    }

    public function getActivePath(): string
    {
        return $this->themesPath.'/'.$this->activeTheme;
    }

    public function getActive(): string
    {
        return $this->activeTheme;
    }

    public function all(): array
    {
        if (! File::isDirectory($this->themesPath)) {
            return [];
        }

        $themes = [];

        foreach (File::directories($this->themesPath) as $dir) {
            $manifestPath = $dir.'/theme.json';
            $name = basename($dir);

            $meta = File::exists($manifestPath)
                ? json_decode(File::get($manifestPath), true)
                : [];

            $themes[$name] = array_merge([
                'name' => $name,
                'description' => '',
                'author' => '',
                'version' => '1.0.0',
                'preview' => null,
            ], $meta, [
                'active' => $name === $this->activeTheme,
                'path' => $dir,
            ]);
        }

        return $themes;
    }

    public function setActive(string $themeName): bool
    {
        $themePath = $this->themesPath.'/'.$themeName;

        if (! File::isDirectory($themePath)) {
            return false;
        }

        $this->activeTheme = $themeName;

        // TODO: Persist to settings table (Phase 2 DB milestone)
        config(['gemana.active_theme' => $themeName]);

        return true;
    }

    public function asset(string $path): string
    {
        return asset('themes/'.$this->activeTheme.'/'.ltrim($path, '/'));
    }
}
