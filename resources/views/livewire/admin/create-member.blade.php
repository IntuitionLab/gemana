<div>
    {{-- Success message --}}
    @if ($success)
        <div
            class="mb-6 rounded-md bg-green-50 border border-green-200 p-4 text-sm text-green-700 flex items-center justify-between"
            x-data
            x-init="setTimeout(() => $wire.set('success', false), 4000)"
        >
            <span>Member created successfully.</span>
            <button wire:click="$set('success', false)" class="text-green-500 hover:text-green-700">&times;</button>
        </div>
    @endif

    <form wire:submit="save" class="space-y-5">

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
                <input
                    type="text"
                    wire:model="name"
                    class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('name') border-red-400 @enderror"
                >
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                <input
                    type="email"
                    wire:model="email"
                    class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('email') border-red-400 @enderror"
                >
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Temporary password</label>
                <input
                    type="password"
                    wire:model="password"
                    class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('password') border-red-400 @enderror"
                >
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-gray-400 font-normal">(optional)</span></label>
                <input
                    type="text"
                    wire:model="phone"
                    class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                >
            </div>

            {{-- Membership Level --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Membership level</label>
                <select
                    wire:model="membership_level"
                    class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                >
                    @foreach ($levels as $level)
                        <option value="{{ $level->slug }}">{{ $level->name }} — {{ $level->feeLabel() }}</option>
                    @endforeach
                </select>
                @error('membership_level') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Membership Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select
                    wire:model="membership_status"
                    class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                >
                    <option value="active">Active</option>
                    <option value="pending">Pending approval</option>
                    <option value="suspended">Suspended</option>
                    <option value="expired">Expired</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">System role</label>
                <select
                    wire:model="role"
                    class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                >
                    <option value="member">Member</option>
                    <option value="volunteer">Volunteer</option>
                    <option value="team">Team</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            {{-- Joined date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Joined date <span class="text-gray-400 font-normal">(optional)</span></label>
                <input
                    type="date"
                    wire:model="joined_at"
                    class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                >
            </div>

        </div>

        <div class="pt-2 flex items-center gap-4">
            <button
                type="submit"
                class="inline-flex items-center px-5 py-2.5 rounded-md bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700 transition-colors"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-not-allowed"
            >
                <span wire:loading.remove>Create member</span>
                <span wire:loading>Creating…</span>
            </button>
        </div>

    </form>
</div>
