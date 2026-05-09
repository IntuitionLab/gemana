<?php

namespace App\Modules\Members\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipLevel extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'annual_fee',
        'is_free',
        'has_voting_rights',
        'requires_approval',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'annual_fee'         => 'decimal:2',
            'is_free'            => 'boolean',
            'has_voting_rights'  => 'boolean',
            'requires_approval'  => 'boolean',
            'is_active'          => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(Member::class)->where('membership_status', 'active');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function feeLabel(): string
    {
        if ($this->is_free || $this->annual_fee == 0) {
            return 'Free';
        }

        return '$' . number_format($this->annual_fee, 2) . ' / year';
    }
}
