<?php

namespace App\Actions\Fortify;

use App\Modules\Members\Models\Member;
use App\Modules\Members\Models\MembershipLevel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): Member
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => $this->passwordRules(),
            'membership_level' => ['nullable', 'string', Rule::exists('membership_levels', 'slug')
                ->where('is_active', true)],
            'terms' => ['accepted'],
        ])->validate();

        // Resolve the requested membership level, defaulting to General.
        $level = MembershipLevel::where(
            'slug',
            $input['membership_level'] ?? 'general'
        )->first();

        // Levels requiring approval start as pending; others go straight to active.
        $status = ($level && $level->requires_approval) ? 'pending' : 'active';

        $member = Member::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'membership_level_id' => $level?->id,
            'membership_status' => $status,
            'joined_at' => now()->toDateString(),
        ]);

        // Assign the base member role (Spatie)
        $member->assignRole('member');

        return $member;
    }
}
