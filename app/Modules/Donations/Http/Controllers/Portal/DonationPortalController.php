<?php

namespace App\Modules\Donations\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Modules\Donations\Models\Donation;
use App\Modules\Donations\Models\DonationPlan;
use App\Modules\Donations\Services\DonationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationPortalController extends Controller
{
    public function __construct(
        protected DonationService $donations,
    ) {}

    // ─── Donations Index ──────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = auth()->user();

        // All completed donations for this member, paginated
        $donations = Donation::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('receipt', 'plan')
            ->latest()
            ->paginate(10);

        // Active recurring plans
        $plans = DonationPlan::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->get();

        // Summary stats
        $totalGiven = Donation::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $totalThisFy = Donation::where('user_id', $user->id)
            ->where('status', 'completed')
            ->thisFinancialYear()
            ->sum('amount');

        $totalCount = Donation::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        return view('donations::portal.index', compact(
            'donations',
            'plans',
            'totalGiven',
            'totalThisFy',
            'totalCount',
        ));
    }

    // ─── Receipt Download ─────────────────────────────────────────────────────

    public function downloadReceipt(Donation $donation)
    {
        // Ensure the donation belongs to the authenticated user
        abort_if($donation->user_id !== auth()->id(), 403);
        abort_if(! $donation->receipt || ! $donation->receipt->hasPdf(), 404);

        $path = $donation->receipt->pdf_path;

        abort_if(! Storage::disk('private')->exists($path), 404);

        return Storage::disk('private')->download(
            $path,
            $donation->receipt->receipt_number . '.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

    // ─── Cancel Recurring Plan ────────────────────────────────────────────────

    public function cancelPlan(DonationPlan $plan)
    {
        // Ensure the plan belongs to the authenticated user
        abort_if($plan->user_id !== auth()->id(), 403);
        abort_if(! $plan->isActive(), 422);

        $cancelled = $this->donations->cancelPlan($plan);

        if (! $cancelled) {
            return back()->with('error', 'Unable to cancel your recurring donation. Please contact us for assistance.');
        }

        return back()->with('success', 'Your recurring donation has been cancelled successfully.');
    }
}
