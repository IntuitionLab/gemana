<?php

namespace App\Modules\Donations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class TaxReceipt extends Model
{
    protected $fillable = [
        'donation_id',
        'user_id',
        'receipt_number',
        'financial_year',
        'org_name',
        'org_abn',
        'org_is_dgr',
        'org_address',
        'donor_name',
        'donor_email',
        'donor_address',
        'amount',
        'currency',
        'pdf_path',
        'emailed_at',
        'emailed_to',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'org_is_dgr'  => 'boolean',
        'emailed_at'  => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function hasBeenEmailed(): bool
    {
        return $this->emailed_at !== null;
    }

    public function hasPdf(): bool
    {
        return $this->pdf_path !== null;
    }

    /**
     * Formatted amount with currency symbol.
     */
    public function formattedAmount(): string
    {
        return '$' . number_format($this->amount, 2) . ' ' . $this->currency;
    }

    /**
     * Generate the next sequential receipt number.
     * Format: GEM-2024-25-00001
     *
     * @param  string  $financialYear  e.g. "2024-25"
     */
    public static function nextReceiptNumber(string $financialYear): string
    {
        $last = static::where('financial_year', $financialYear)
            ->orderByDesc('id')
            ->value('receipt_number');

        if ($last) {
            // Extract the numeric suffix and increment
            $sequence = (int) substr($last, strrpos($last, '-') + 1);
        } else {
            $sequence = 0;
        }

        return 'GEM-' . $financialYear . '-' . str_pad($sequence + 1, 5, '0', STR_PAD_LEFT);
    }
}
