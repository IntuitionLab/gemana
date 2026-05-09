<?php

namespace App\Livewire\Admin;

use App\Core\ModuleLoader;
use Livewire\Component;

class ModuleToggle extends Component
{
    public string $name;

    public bool $enabled;

    public bool $core;

    public string $description;

    public string $version;

    public function mount(string $name, array $module): void
    {
        $this->name = $name;
        $this->enabled = $module['enabled'] ?? false;
        $this->core = $module['core'] ?? false;
        $this->description = $module['description'] ?? '';
        $this->version = $module['version'] ?? '1.0.0';
    }

    public function toggle(): void
    {
        if ($this->core) {
            $this->dispatch('notify', type: 'error', message: "Core module [{$this->name}] cannot be disabled.");

            return;
        }

        $loader = app(ModuleLoader::class);
        $newState = ! $this->enabled;
        $loader->toggle($this->name, $newState);

        $this->enabled = $newState;

        $status = $newState ? 'enabled' : 'disabled';
        $this->dispatch('notify', type: 'success', message: "Module [{$this->name}] has been {$status}.");
    }

    public function render()
    {
        return view('livewire.admin.module-toggle');
    }
}
