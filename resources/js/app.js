import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// On pages with Livewire, it calls Alpine.start() automatically.
// On non-Livewire pages (welcome, auth views) we start it ourselves.
if (typeof window.Livewire === 'undefined') {
    Alpine.start();
}