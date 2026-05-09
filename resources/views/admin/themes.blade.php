@extends('admin.layout')
@section('title', 'Themes')

@section('content')

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
        <h2 class="font-display text-[22px] font-extrabold text-gray-900 mb-1">Themes</h2>
        <p class="text-sm text-gray-500">Choose the appearance of your public-facing site and member portal.</p>
    </div>

    @forelse($themes as $name => $theme)
        @livewire('admin.theme-activate', ['name' => $name, 'theme' => $theme], key($name))
    @empty
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
            <p class="text-[15px] font-medium text-gray-500">No themes found.</p>
            <p class="text-sm text-gray-400 mt-2">Add a theme folder to <code class="bg-gray-100 px-1.5 py-0.5 rounded">/themes</code> with a <code class="bg-gray-100 px-1.5 py-0.5 rounded">theme.json</code> manifest.</p>
        </div>
    @endforelse

</div>

@endsection