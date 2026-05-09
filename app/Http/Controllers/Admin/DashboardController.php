<?php

namespace App\Http\Controllers\Admin;

use App\Core\ModuleLoader;
use App\Core\ThemeManager;
use App\Http\Controllers\Controller;
use App\Modules\Members\Models\Member;

class DashboardController extends Controller
{
    public function __construct(
        protected ModuleLoader $modules,
        protected ThemeManager $theme
    ) {}

    public function index()
    {
        $total = Member::count();
        $financial = Member::financial()->count();
        $pending = Member::where('membership_status', 'pending')->count();
        $newMonth = Member::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $stats = [
            'total_members' => $total,
            'financial_members' => $financial,
            'financial_pct' => $total > 0 ? round(($financial / $total) * 100) : 0,
            'pending' => $pending,
            'new_this_month' => $newMonth,
        ];

        $recentMembers = Member::with('membershipLevel')->latest()->take(5)->get();

        $modules = collect($this->modules->all())->map(fn ($m) => [
            'name' => $m['name'] ?? ucfirst($m['id'] ?? ''),
            'description' => $m['description'] ?? '',
            'enabled' => $m['enabled'] ?? false,
        ])->values();

        $activity = [[
            'color' => 'blue',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />',
            'message' => 'System ready — Phase 2 complete',
            'time' => now()->diffForHumans(),
        ]];

        return view('admin.dashboard', compact(
            'stats', 'recentMembers', 'modules', 'activity'
        ));
    }

    public function modules()
    {
        return view('admin.modules', [
            'modules' => $this->modules->all(),
        ]);
    }

    public function toggleModule(string $name)
    {
        $module = $this->modules->all()[$name] ?? null;

        if (! $module) {
            return back()->with('error', "Module [{$name}] not found.");
        }

        if ($module['core'] ?? false) {
            return back()->with('error', "Core module [{$name}] cannot be disabled.");
        }

        $newState = ! ($module['enabled'] ?? false);
        $this->modules->toggle($name, $newState);

        return back()->with('success', "Module [{$name}] has been ".($newState ? 'enabled' : 'disabled').'.');
    }

    public function themes()
    {
        return view('admin.themes', [
            'themes' => $this->theme->all(),
            'activeTheme' => $this->theme->getActive(),
        ]);
    }

    public function activateTheme(string $name)
    {
        if (! $this->theme->setActive($name)) {
            return back()->with('error', "Theme [{$name}] not found.");
        }

        return back()->with('success', "Theme [{$name}] is now active.");
    }
}
