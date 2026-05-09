<?php

namespace App\Modules\Donations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Donation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'donor_name',
        'donor_email',
        'is_anonymous',
        'amount',
        'currency',
        'type',
        'status',
        'gateway',
        'gateway_reference',
        'gateway_status',
        'donation_plan_id',
        'tribute_name',
        'tribute_type',
        'tribute_notify_name',
        'tribute_notify_email',
        'message',
        'tax_receipt_issued',
        'tax_receipt_issued_at',
    ];

    protected $casts = [
        'amount'                 => 'decimal:2',
        'is_anonymous'           => 'boolean',
        'tax_receipt_issued'     => 'boolean',
        'tax_receipt_issued_at'  => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(DonationPlan::class, 'donation_plan_id');
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(TaxReceipt::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOneOff($query)
    {
        return $query->where('type', 'one_off');
    }

    public function scopeRecurring($query)
    {
        return $query->where('type', 'recurring');
    }

    public function scopeInMemory($query)
    {
        return $query->where('type', 'in_memory');
    }

    public function scopeThisFinancialYear($query)
    {
        // Australian FY: 1 July – 30 June
        $fyStart = now()->month >= 7
            ? now()->startOfYear()->month(7)->startOfMonth()
            : now()->subYear()->month(7)->startOfMonth();

        return $query->where('created_at', '>=', $fyStart);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRecurring(): bool
    {
        return $this->type === 'recurring';
    }

    public function isInMemory(): bool
    {
        return $this->type === 'in_memory';
    }

    public function hasReceipt(): bool
    {
        return $this->tax_receipt_issued;
    }

    /**
     * Display name for the donor.
     * Returns "Anonymous" if the donation was made anonymously.
     */
    public function displayName(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous';
        }

        return $this->donor_name
            ?? $this->user?->name
            ?? 'Unknown';
    }

    /**
     * Formatted amount with currency symbol.
     */
    public function formattedAmount(): string
    {
        return '$' . number_format($this->amount, 2) . ' ' . $this->currency;
    }

    /**
     * Australian financial year string, e.g. "2024-25".
     */
    public function financialYear(): string
    {
        $year = $this->created_at->month >= 7
            ? $this->created_at->year
            : $this->created_at->year - 1;

        return $year . '-' . substr($year + 1, -2);
    }
}
