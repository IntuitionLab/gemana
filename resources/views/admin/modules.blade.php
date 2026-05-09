@extends('admin.layout')
@section('title', 'Modules')

@section('content')

{{-- Livewire event → Alpine toast notification --}}
<div
    x-data="{
        toasts: [],
        add(type, message) {
            const id = Date.now();
            this.toasts.push({ id, type, message });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) { this.toasts = this.toasts.filter(t => t.id !== id); }
    }"
    @notify.window="add($event.detail.type, $event.detail.message)"
    class="relative"
>
    {{-- Toast container --}}
    <div class="fixed top-6 right-6 z-50 flex flex-col gap-2" style="min-width:280px">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" x-transition
                 :class="toast.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'"
                 class="flex items-center justify-between px-4 py-3 rounded-xl border text-sm font-medium shadow-sm">
                <span x-text="toast.message"></span>
                <button @click="remove(toast.id)" class="ml-3 opacity-50 hover:opacity-100">✕</button>
            </div>
        </template>
    </div>

    <div class="mb-6">
        <h2 class="font-display text-[22px] font-extrabold text-gray-900 mb-1">Modules</h2>
        <p class="text-sm text-gray-500">Enable or disable functionality. Core modules cannot be turned off.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($modules as $name => $module)
            @livewire('admin.module-toggle', ['name' => $name, 'module' => $module], key($name))
        @endforeach
    </div>

</div>

@endsection