<?php

namespace App\Core;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ModuleLoader
{
    /**
     * Base path where all modules live.
     */
    protected string $modulesPath;

    /**
     * All discovered modules and their metadata.
     */
    protected array $modules = [];

    public function __construct()
    {
        $this->modulesPath = app_path('Modules');
    }

    /**
     * Discover all modules from the filesystem and load their module.json.
     */
    public function discover(): void
    {
        if (! File::isDirectory($this->modulesPath)) {
            return;
        }

        $directories = File::directories($this->modulesPath);

        foreach ($directories as $directory) {
            $manifestPath = $directory.'/module.json';

            if (! File::exists($manifestPath)) {
                continue;
            }

            $manifest = json_decode(File::get($manifestPath), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning("Gemana: Could not parse module.json in {$directory}");

                continue;
            }

            $moduleName = basename($directory);
            $this->modules[$moduleName] = array_merge($manifest, [
                'path' => $directory,
            ]);
        }
    }

    /**
     * Boot all modules that are enabled.
     * Registers each module's ServiceProvider with Laravel.
     */
    public function boot(): void
    {
        foreach ($this->modules as $name => $module) {
            if (! ($module['enabled'] ?? false)) {
                continue;
            }

            $providerClass = "App\\Modules\\{$name}\\ModuleServiceProvider";

            if (class_exists($providerClass)) {
                app()->register($providerClass);
            } else {
                Log::warning("Gemana: No ServiceProvider found for module [{$name}].");
            }
        }
    }

    /**
     * Get all discovered modules.
     */
    public function all(): array
    {
        return $this->modules;
    }

    /**
     * Get only enabled modules.
     */
    public function enabled(): array
    {
        return array_filter($this->modules, fn ($m) => $m['enabled'] ?? false);
    }

    /**
     * Check if a specific module is enabled.
     */
    public function isEnabled(string $name): bool
    {
        return isset($this->modules[$name]) && ($this->modules[$name]['enabled'] ?? false);
    }

    /**
     * Toggle a module on or off by updating its module.json.
     */
    public function toggle(string $name, bool $enabled): bool
    {
        if (! isset($this->modules[$name])) {
            return false;
        }

        $manifestPath = $this->modules[$name]['path'].'/module.json';
        $manifest = json_decode(File::get($manifestPath), true);
        $manifest['enabled'] = $enabled;

        File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
        $this->modules[$name]['enabled'] = $enabled;

        return true;
    }
}
