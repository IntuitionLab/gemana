<?php

namespace App\Livewire\Admin;

use App\Core\ThemeManager;
use Livewire\Component;

class ThemeActivate extends Component
{
    public string $name;

    public bool $active;

    public string $description;

    public string $version;

    public string $author;

    public function mount(string $name, array $theme): void
    {
        $this->name = $name;
        $this->active = $theme['active'] ?? false;
        $this->description = $theme['description'] ?? '';
        $this->version = $theme['version'] ?? '1.0.0';
        $this->author = $theme['author'] ?? '';
    }

    public function activate(): void
    {
        $manager = app(ThemeManager::class);

        if (! $manager->setActive($this->name)) {
            $this->dispatch('notify', type: 'error', message: "Theme [{$this->name}] could not be activated.");

            return;
        }

        $this->active = true;
        $this->dispatch('themeActivated', name: $this->name);
        $this->dispatch('notify', type: 'success', message: "Theme [{$this->name}] is now active.");
    }

    public function render()
    {
        return view('livewire.admin.theme-activate');
    }
}
