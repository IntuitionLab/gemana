<?php

namespace App\Livewire\Admin;

use App\Modules\Members\Models\Member;
use App\Modules\Members\Models\MembershipLevel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateMember extends Component
{
    public string $name             = '';
    public string $email            = '';
    public string $password         = '';
    public string $membership_level = 'general';
    public string $membership_status = 'active';
    public string $phone            = '';
    public string $joined_at        = '';
    public string $role             = 'member';

    public bool $success = false;

    protected function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password'         => ['required', 'string', 'min:8'],
            'membership_level' => ['required', Rule::exists('membership_levels', 'slug')->where('is_active', true)],
            'membership_status'=> ['required', 'in:pending,active,suspended,expired,cancelled'],
            'phone'            => ['nullable', 'string', 'max:20'],
            'joined_at'        => ['nullable', 'date'],
            'role'             => ['required', 'in:member,volunteer,team,admin'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $level = MembershipLevel::where('slug', $this->membership_level)->firstOrFail();

        $member = Member::create([
            'name'                => $this->name,
            'email'               => $this->email,
            'password'            => Hash::make($this->password),
            'membership_level_id' => $level->id,
            'membership_status'   => $this->membership_status,
            'phone'               => $this->phone ?: null,
            'joined_at'           => $this->joined_at ?: now()->toDateString(),
            // Admin-created accounts are pre-verified.
            'email_verified_at'   => now(),
        ]);

        $member->assignRole($this->role);

        $this->success = true;
        $this->reset(['name', 'email', 'password', 'phone', 'joined_at']);
        $this->membership_level  = 'general';
        $this->membership_status = 'active';
        $this->role              = 'member';

        $this->dispatch('member-created');
    }

    public function render()
    {
        return view('livewire.admin.create-member', [
            'levels' => MembershipLevel::active()->get(),
        ]);
    }
}
