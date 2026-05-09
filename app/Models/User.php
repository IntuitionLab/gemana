<?php

namespace App\Models;

use App\Modules\Members\Models\MembershipLevel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    /**
     * Spatie uses this to match roles/permissions to the correct guard.
     */
    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'membership_level_id',
        'membership_status',
        'phone',
        'address_line1',
        'address_line2',
        'suburb',
        'state',
        'postcode',
        'country',
        'joined_at',
        'membership_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'joined_at' => 'date',
            'membership_expires_at' => 'date',
            'password' => 'hashed',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function membershipLevel(): BelongsTo
    {
        return $this->belongsTo(MembershipLevel::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('membership_status', 'active');
    }

    public function scopeFinancial($query)
    {
        return $query->active()->where(function ($q) {
            $q->whereNull('membership_expires_at')
                ->orWhere('membership_expires_at', '>=', now());
        });
    }

    public function scopeByLevel($query, string $slug)
    {
        return $query->whereHas('membershipLevel', fn ($q) => $q->where('slug', $slug));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Whether this user's role requires 2FA. */
    public function requires2fa(): bool
    {
        return $this->roles()->where('requires_2fa', true)->exists();
    }

    /** Whether 2FA has been fully confirmed. */
    public function hasTwoFactorEnabled(): bool
    {
        return ! is_null($this->two_factor_confirmed_at)
            && ! is_null($this->two_factor_secret);
    }

    /** Human-readable membership status label. */
    public function statusLabel(): string
    {
        return match ($this->membership_status) {
            'active' => 'Active',
            'pending' => 'Pending Approval',
            'suspended' => 'Suspended',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            default => ucfirst((string) $this->membership_status),
        };
    }

    /** Full formatted address. */
    public function formattedAddress(): string
    {
        return collect([
            $this->address_line1,
            $this->address_line2,
            $this->suburb,
            trim(($this->state ?? '').' '.($this->postcode ?? '')),
            $this->country,
        ])->filter()->implode(', ');
    }
}
