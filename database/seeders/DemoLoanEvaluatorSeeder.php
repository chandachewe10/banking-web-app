<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DemoLoanEvaluatorSeeder extends Seeder
{
    private const DEMO_PASSWORD = 'password';

    /**
     * Loan pipeline stages mapped to Filament resources / Spatie permissions.
     */
    private const ROLE_PERMISSIONS = [
        'super_admin' => '*',

        'Credit Officer' => [
            'borrower',
            'credit::evaluation',
        ],

        'Head Credit Officer' => [
            'head::credit::review',
        ],

        'Branch Manager' => [
            'branch::manager::review',
        ],

        'Physical Signing Officer' => [
            'physical::signing::evaluation',
        ],

        'Finance Officer' => [
            'disbursements',
        ],
    ];

    private const DEMO_USERS = [
        [
            'name'  => 'System Admin',
            'email' => 'admin@sentinel.demo',
            'role'  => 'super_admin',
        ],
        [
            'name'  => 'Demo Credit Officer',
            'email' => 'credit.officer@sentinel.demo',
            'role'  => 'Credit Officer',
        ],
        [
            'name'  => 'Demo Head Credit Officer',
            'email' => 'head.credit@sentinel.demo',
            'role'  => 'Head Credit Officer',
        ],
        [
            'name'  => 'Demo Branch Manager',
            'email' => 'branch.manager@sentinel.demo',
            'role'  => 'Branch Manager',
        ],
        [
            'name'  => 'Demo Physical Signing Officer',
            'email' => 'physical.signing@sentinel.demo',
            'role'  => 'Physical Signing Officer',
        ],
        [
            'name'  => 'Demo Finance Officer',
            'email' => 'finance@sentinel.demo',
            'role'  => 'Finance Officer',
        ],
    ];

    public function run(): void
    {
        $this->ensurePermissionsExist();

        foreach (self::ROLE_PERMISSIONS as $roleName => $resourceSlugs) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
            );

            $role->syncPermissions($this->permissionsFor($resourceSlugs));
        }

        Role::firstOrCreate(['name' => 'panel_user', 'guard_name' => 'web']);

        foreach (self::DEMO_USERS as $demoUser) {
            $user = User::updateOrCreate(
                ['email' => $demoUser['email']],
                [
                    'name'              => $demoUser['name'],
                    'password'          => Hash::make(self::DEMO_PASSWORD),
                    'email_verified_at' => now(),
                    'is_verified'       => true,
                    'case_number'       => null,
                ],
            );

            $user->syncRoles([$demoUser['role'], 'panel_user']);
        }

        $this->command?->info('Demo loan evaluators seeded. All accounts use password: ' . self::DEMO_PASSWORD);
        $this->command?->table(
            ['Role', 'Email', 'Filament path'],
            [
                ['super_admin', 'admin@sentinel.demo', '/admin — full access'],
                ['Credit Officer', 'credit.officer@sentinel.demo', 'Borrower Evaluation → Credit Evaluation'],
                ['Head Credit Officer', 'head.credit@sentinel.demo', 'Head Credit Evaluation'],
                ['Branch Manager', 'branch.manager@sentinel.demo', 'Branch Manager Evaluation'],
                ['Physical Signing Officer', 'physical.signing@sentinel.demo', 'Physical signing verification'],
                ['Finance Officer', 'finance@sentinel.demo', 'Disbursements'],
            ],
        );
    }

    private function ensurePermissionsExist(): void
    {
        if (Permission::count() > 0) {
            return;
        }

        Artisan::call('shield:generate', [
            '--all'                      => true,
            '--panel'                    => 'admin',
            '--ignore-existing-policies' => true,
            '--no-interaction'           => true,
        ]);
    }

    /**
     * @param  string|list<string>  $resourceSlugs
     * @return list<int>
     */
    private function permissionsFor(string|array $resourceSlugs): array
    {
        if ($resourceSlugs === '*') {
            return Permission::pluck('id')->all();
        }

        $slugs = is_array($resourceSlugs) ? $resourceSlugs : [$resourceSlugs];
        $actions = [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'force_delete',
            'force_delete_any',
        ];

        $names = [];
        foreach ($slugs as $slug) {
            foreach ($actions as $action) {
                $names[] = "{$action}_{$slug}";
            }
        }

        return Permission::whereIn('name', $names)->pluck('id')->all();
    }
}
