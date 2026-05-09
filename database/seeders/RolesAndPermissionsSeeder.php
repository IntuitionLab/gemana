<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // -------------------------------------------------------------------------
        // Permissions
        // -------------------------------------------------------------------------

        $permissions = [
            // Members
            'members.view',
            'members.create',
            'members.edit',
            'members.delete',
            'members.export',

            // Donations
            'donations.view',
            'donations.create',
            'donations.edit',
            'donations.delete',
            'donations.export',
            'donations.issue_receipt',

            // Events
            'events.view',
            'events.create',
            'events.edit',
            'events.delete',
            'events.manage_attendees',

            // Blog
            'blog.view',
            'blog.create',
            'blog.edit',
            'blog.delete',
            'blog.publish',

            // Documents
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.delete',

            // Volunteering
            'volunteering.view',
            'volunteering.create',
            'volunteering.edit',
            'volunteering.manage_rosters',

            // Newsletter
            'newsletter.view',
            'newsletter.create',
            'newsletter.send',

            // Settings
            'settings.view',
            'settings.edit',

            // Modules & Themes (Super-Admin only)
            'modules.manage',
            'themes.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // -------------------------------------------------------------------------
        // Roles  (requires_2fa is our custom column added in the migration)
        // -------------------------------------------------------------------------

        $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'web'],
            ['requires_2fa' => true]
        );
        // Super-Admin gets all permissions via gate bypass in AuthServiceProvider
        // (using Spatie's recommended pattern) — no need to assign individually.

        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['requires_2fa' => true]
        );
        $admin->syncPermissions([
            'members.view', 'members.create', 'members.edit', 'members.delete', 'members.export',
            'donations.view', 'donations.create', 'donations.edit', 'donations.delete', 'donations.export', 'donations.issue_receipt',
            'events.view', 'events.create', 'events.edit', 'events.delete', 'events.manage_attendees',
            'blog.view', 'blog.create', 'blog.edit', 'blog.delete', 'blog.publish',
            'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
            'volunteering.view', 'volunteering.create', 'volunteering.edit', 'volunteering.manage_rosters',
            'newsletter.view', 'newsletter.create', 'newsletter.send',
            'settings.view', 'settings.edit',
        ]);

        $team = Role::firstOrCreate(
            ['name' => 'team', 'guard_name' => 'web'],
            ['requires_2fa' => true]
        );
        $team->syncPermissions([
            'members.view',
            'donations.view',
            'events.view', 'events.create', 'events.edit', 'events.manage_attendees',
            'blog.view', 'blog.create', 'blog.edit',
            'documents.view', 'documents.create',
            'volunteering.view', 'volunteering.create', 'volunteering.edit', 'volunteering.manage_rosters',
            'newsletter.view',
        ]);

        $volunteer = Role::firstOrCreate(
            ['name' => 'volunteer', 'guard_name' => 'web'],
            ['requires_2fa' => false]
        );
        $volunteer->syncPermissions([
            'events.view',
            'documents.view',
            'volunteering.view',
        ]);

        $member = Role::firstOrCreate(
            ['name' => 'member', 'guard_name' => 'web'],
            ['requires_2fa' => false]
        );
        $member->syncPermissions([
            'events.view',
            'documents.view',
            'donations.view',
        ]);

        // Public has no role — unauthenticated access is handled by route middleware.

        // -------------------------------------------------------------------------
        // Membership Levels
        // -------------------------------------------------------------------------

        $levels = [
            [
                'name'              => 'General',
                'slug'              => 'general',
                'description'       => 'Standard membership open to all.',
                'annual_fee'        => 20.00,
                'is_free'           => false,
                'has_voting_rights' => false,
                'requires_approval' => false,
                'sort_order'        => 1,
                'is_active'         => true,
            ],
            [
                'name'              => 'Financial',
                'slug'              => 'financial',
                'description'       => 'Full financial member with voting rights.',
                'annual_fee'        => 50.00,
                'is_free'           => false,
                'has_voting_rights' => true,
                'requires_approval' => false,
                'sort_order'        => 2,
                'is_active'         => true,
            ],
            [
                'name'              => 'Life',
                'slug'              => 'life',
                'description'       => 'Lifetime membership — no annual renewal required.',
                'annual_fee'        => 0.00,
                'is_free'           => true,
                'has_voting_rights' => true,
                'requires_approval' => true,
                'sort_order'        => 3,
                'is_active'         => true,
            ],
            [
                'name'              => 'Honorary',
                'slug'              => 'honorary',
                'description'       => 'Awarded by the committee in recognition of outstanding service.',
                'annual_fee'        => 0.00,
                'is_free'           => true,
                'has_voting_rights' => false,
                'requires_approval' => true,
                'sort_order'        => 4,
                'is_active'         => true,
            ],
        ];

        foreach ($levels as $level) {
            \App\Modules\Members\Models\MembershipLevel::firstOrCreate(
                ['slug' => $level['slug']],
                $level
            );
        }

        $this->command->info('✅ Roles, permissions, and membership levels seeded.');
    }
}
