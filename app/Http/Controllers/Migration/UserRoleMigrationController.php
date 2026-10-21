<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserRoleMigrationController extends BaseMigrationController
{
    /**
     * Migrates `users_roles` from old MySQL → `user_roles` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/user-roles/preview  → preview()
     *   POST   /migrate/user-roles          → migrate()
     *   DELETE /migrate/user-roles/reset    → reset()
     *
     * Column mapping (old → new):
     *   user_id  → user_id   resolved via old_id → new users.id
     *   role_id  → role_id   direct copy (matches Sushi Role model IDs)
     *
     * Role IDs (Sushi static model — no DB lookup needed):
     *   1 → Admin
     *   2 → Principal
     *   3 → Office
     *   4 → Homeroom
     *   5 → Teacher
     *   6 → Registrar
     *   7 → Secretary
     *
     * ⚠ Run AFTER users migration — needs new users.id via old_id lookup.
     */

    // Map old user.id → new users.id (via old_id column)
    private ?array $userIdMap = null;

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        $this->buildUserIdMap();

        return response()->json([
            'table'     => 'user_roles',
            'old_count' => DB::connection('old_db')->table('users_roles')->count(),
            'new_count' => DB::table('user_roles')->count(),
            'sample'    => DB::connection('old_db')
                                ->table('users_roles')
                                ->limit(5)
                                ->get()
                                ->map(fn ($r) => $this->mapRow($r)),
        ]);
    }

    // -------------------------------------------------------------------------
    // Migrate
    // -------------------------------------------------------------------------

    public function migrate(): JsonResponse
    {
        $this->buildUserIdMap();

        $stats = $this->runMigration(
            newTable:    'user_roles',
            oldTable:    'users_roles',
            orderColumn: 'entry_id',
            mapper:      fn ($r) => $this->mapRow($r),
            columnCount: 4, // user_id, role_id, created_at, updated_at
            dedupeKey:   'entry_id'
        );

        $officeRolesAdded = $this->addOfficeRolesForEligibleUsers();

        $unresolved = DB::table('user_roles')->whereNull('user_id')->count();

        return response()->json([
            'status'     => empty($stats['errors']) ? 'done' : 'done_with_errors',
            'unresolved' => $unresolved,
            'note'       => $unresolved > 0
                ? "{$unresolved} row(s) could not resolve user_id — old user may not have been migrated."
                : 'All user_id values resolved successfully.',
            'office_roles_added' => $officeRolesAdded,
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // Reset
    // -------------------------------------------------------------------------

    public function reset(): JsonResponse
    {
        $deleted = DB::table('user_roles')->delete();
        DB::table('user_roles')->truncate();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $now = now()->toDateTimeString();

        return [
            // Resolve old users.id → new users.id via old_id column
            'user_id'    => $this->userIdMap[$row->user_id] ?? null,
            'role_id'    => $row->role_id ? (int) $row->role_id : null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        // 4 columns — keep in sync with columnCount above
    }

    // -------------------------------------------------------------------------
    // Build old user_id → new users.id map
    // -------------------------------------------------------------------------

    /**
     * The new users table has a new auto-increment id, but preserves the old
     * id in the old_id column. This map lets us translate old → new id.
     *
     * e.g. old users.id = 114 → new users.id = 23 (where old_id = 114)
     */
    private function buildUserIdMap(): void
    {
        if ($this->userIdMap !== null) {
            return;
        }

        // Map: old_id (int) → new id (int)
        $this->userIdMap = DB::table('users')
            ->whereNotNull('old_id')
            ->pluck('id', 'old_id')
            ->all();
    }

    /**
     * Users with Principal, Registrar, or Secretary access also need Office
     * access. Add the derived role only when it does not already exist so the
     * migration remains safe to run more than once.
     */
    private function addOfficeRolesForEligibleUsers(): int
    {
        $eligibleUserIds = DB::table('user_roles')
            ->select('user_id')
            ->whereNotNull('user_id')
            ->whereIn('role_id', [2, 6, 7])
            ->distinct()
            ->pluck('user_id');

        if ($eligibleUserIds->isEmpty()) {
            return 0;
        }

        $officeUserIds = DB::table('user_roles')
            ->where('role_id', 3)
            ->whereIn('user_id', $eligibleUserIds)
            ->pluck('user_id')
            ->all();

        $now = now()->toDateTimeString();
        $rows = $eligibleUserIds
            ->diff($officeUserIds)
            ->map(fn ($userId) => [
                'user_id' => $userId,
                'role_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->values()
            ->all();

        if ($rows === []) {
            return 0;
        }

        DB::table('user_roles')->insert($rows);

        return count($rows);
    }
}
