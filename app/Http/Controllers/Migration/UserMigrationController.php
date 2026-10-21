<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UserMigrationController extends BaseMigrationController
{
    /**
     * Migrates `users` + `user_info` from old MySQL → `users` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/users/preview  → preview()
     *   POST   /migrate/users          → migrate()
     *   GET    /migrate/users/unfilled → unfilledEmails()
     *   DELETE /migrate/users/reset    → reset()
     *
     * Column mapping (old users → new users):
     *   id             → old_id        preserved for reference
     *   lastname       → last_name     renamed
     *   firstname      → first_name    renamed
     *   middlename     → middle_name   renamed, nullable
     *   suffix         → suffix        empty string → null
     *   title          → honorifics    direct map
     *   active         → active        int → tinyInteger
     *   password       → password      hash preserved
     *   remember_token → remember_token
     *   created_at     → created_at    preserved
     *   updated_at     → updated_at    preserved
     *
     * Joined from old user_info (via user_id):
     *   email_address  → email         placeholder {id}@placeholder.email if null/missing
     *   birth_date     → birth_date
     *   contact        → contact
     *   address        → address
     *
     * user_roles migrated separately — see UserRolesMigrationController
     *
     * Generated:
     *   name           → "{firstname} {lastname}"
     */

    // Pre-load user_info keyed by user_id to avoid N+1 queries
    private ?array $userInfoMap  = null;

    // Pre-load user_roles keyed by user_id to avoid N+1 queries
    private ?array $userRolesMap = null;

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {
        $this->buildUserInfoMap();
        $this->buildUserRolesMap();

        return response()->json([
            'table'           => 'users',
            'old_count'       => DB::connection('old_db')->table('users')->count(),
            'new_count'       => DB::table('users')->count(),
            'user_info_count' => DB::connection('old_db')->table('users_info')->count(),
            'sample'          => DB::connection('old_db')
                ->table('users')
                ->limit(3)
                ->get()
                ->map(fn($r) => $this->mapRow($r)),
        ]);
    }

    // -------------------------------------------------------------------------
    // Migrate
    // -------------------------------------------------------------------------

    public function migrate(): JsonResponse
    {
        $this->buildUserInfoMap();
        $this->buildUserRolesMap();

        $stats = $this->runMigration(
            newTable: 'users',
            oldTable: 'users',
            orderColumn: 'id',
            mapper: fn($r) => $this->mapRow($r),
            columnCount: 18, // old_id, name, email, email_verified_at, last_name, first_name,
            // middle_name, suffix, honorifics, birth_date, contact, address,
            // active, password, remember_token, created_at, updated_at
            dedupeKey: 'id'
        );

        $unfilled = DB::table('users')
            ->where('email', 'like', '%@placeholder.email')
            ->count();

        return response()->json([
            'status'   => empty($stats['errors']) ? 'done' : 'done_with_errors',
            'unfilled' => $unfilled,
            'note'     => $unfilled > 0
                ? "{$unfilled} user(s) have placeholder emails. Call GET /migrate/users/unfilled."
                : 'All emails resolved from user_info.',
            'user_roles_note' => 'user_roles are migrated separately via POST /migrate/user-roles',
            ...$stats,
        ]);
    }

    // -------------------------------------------------------------------------
    // List placeholder emails that still need to be filled
    // -------------------------------------------------------------------------

    public function unfilledEmails(): JsonResponse
    {
        $rows = DB::table('users')
            ->where('email', 'like', '%@placeholder.email')
            ->select(['id', 'old_id', 'name', 'email', 'first_name', 'last_name'])
            ->get();

        return response()->json([
            'count' => $rows->count(),
            'users' => $rows,
        ]);
    }

    // -------------------------------------------------------------------------
    // Reset
    // -------------------------------------------------------------------------

    public function reset(): JsonResponse
    {
        $referencedUserIds = Schema::hasTable('grades')
            ? DB::table('grades')->whereNotNull('user_id')->distinct()->pluck('user_id')
            : collect();

        $users = DB::table('users');

        if ($referencedUserIds->isNotEmpty()) {
            $users->whereNotIn('id', $referencedUserIds);
        }

        $deleted = $users->delete();
        $preserved = DB::table('users')->count();

        return response()->json([
            'status' => 'reset',
            'deleted' => $deleted,
            'preserved' => $preserved,
            'note' => $preserved > 0
                ? 'Referenced users were preserved because grades still point to them.'
                : null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $now = now()->toDateTimeString();

        $firstName  = Str::title(str_replace('?', 'ñ', trim($row->firstname  ?? '')));
        $lastName   = Str::title(str_replace('?', 'ñ', trim($row->lastname   ?? '')));
        $middleName = Str::title(str_replace('?', 'ñ', trim($row->middlename ?? '')));

        if ($firstName === '' || Str::contains(strtolower(trim("{$firstName} {$lastName}")), 'test')) {
            return [];
        }

        if (config('app.name') === 'HCGS') {
            // Duplicate issue - forcing to true
            if (in_array($row->id, [6, 19, 7])) {
                $row->active = true;
            }

            // Duplicate issue - forcing to skip
            if (in_array($row->id, [45, 47, 53])) {
                return [];
            }
        }

        // Pull info from pre-loaded user_info map
        $info = $this->userInfoMap[$row->id] ?? null;

        // Use email from user_info if available, otherwise generate placeholder
        $email = ($info && ! empty($info->email_address))
            ? $info->email_address
            : "{$row->id}@placeholder.email";

        return [
            'old_id'            => $row->id,

            // Display name: "Firstname Lastname"
            'name'              => trim("{$firstName} {$lastName}"),

            // Email from user_info.email_address or placeholder
            'email'             => $email,
            'email_verified_at' => null,

            // Renamed columns
            'last_name'         => $lastName   ?: null,
            'first_name'        => $firstName  ?: null,
            'middle_name'       => $middleName ?: null,

            // Nullable, preserved as-is
            'suffix'            => ($row->suffix !== '' && $row->suffix !== null)
                ? $row->suffix
                : null,

            // title → honorifics (direct map)
            'honorifics'        => ($row->title !== '' && $row->title !== null)
                ? $row->title
                : null,

            // From user_info
            'birth_date'        => $info->birth_date ?? null,
            'contact'           => $info->contact    ?? null,
            'address'           => $info->address    ?? null,

            'active'            => (int) $row->active,
            'password'          => $row->password,
            'remember_token'    => $row->remember_token ?? null,

            // Preserve original timestamps from old DB if present
            'created_at'        => $row->created_at ?? $now,
            'updated_at'        => $row->updated_at ?? $now,
        ];
        // 18 columns — keep in sync with columnCount above
    }

    // -------------------------------------------------------------------------
    // Pre-load user_info into memory keyed by user_id
    // -------------------------------------------------------------------------

    /**
     * Loads all user_info rows into a map: user_id → row object.
     * Called once before chunking to avoid per-row queries.
     */
    private function buildUserInfoMap(): void
    {
        if ($this->userInfoMap !== null) {
            return;
        }

        $this->userInfoMap = [];

        DB::connection('old_db')
            ->table('users_info')
            ->orderBy('user_id')
            ->chunk(500, function ($rows) {
                foreach ($rows as $row) {
                    // If a user has multiple user_info rows, last one wins
                    $this->userInfoMap[$row->user_id] = $row;
                }
            });
    }

    private function buildUserRolesMap(): void
    {
        if ($this->userRolesMap !== null) {
            return;
        }

        $this->userRolesMap = [];

        DB::connection('old_db')
            ->table('users_roles')
            ->orderBy('user_id')
            ->chunk(500, function ($rows) {
                foreach ($rows as $row) {
                    // Store all roles per user — user may have multiple
                    $this->userRolesMap[$row->user_id][] = (int) $row->role_id;
                }
            });
    }
}
