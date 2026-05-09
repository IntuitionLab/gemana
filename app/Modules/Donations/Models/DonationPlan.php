<?php

namespace App\Modules\Donations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class DonationPlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'frequency',
        'status',
        'gateway',
        'gateway_subscription_id',
        'gateway_plan_id',
        'started_at',
        'next_billing_date',
        'cancelled_at',
        'donor_name',
        'donor_email',
    ];

    protected $casts = [
        'amount'            => 'decimal:2',
        'started_at'        => 'date',
        'next_billing_date' => 'date',
        'cancelled_at'      => 'date',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function cancel(): void
    {
        $this->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Human-readable frequency label.
     */
    public function frequencyLabel(): string
    {
        return match ($this->frequency) {
            'weekly'    => 'Weekly',
            'monthly'   => 'Monthly',
            'quarterly' => 'Quarterly',
            'annual'    => 'Annually',
            default     => ucfirst($this->frequency),
        };
    }

    /**
     * Formatted amount with currency symbol.
     * Defaults to AUD — will respect org settings once Organisation Settings is built.
     */
    public function formattedAmount(): string
    {
        return '$' . number_format($this->amount, 2) . ' ' . $this->currency;
    }
}
