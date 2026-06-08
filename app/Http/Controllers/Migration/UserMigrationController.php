<?php

namespace App\Http\Controllers\Migration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserMigrationController extends BaseMigrationController
{
    /**
     * Migrates `users` from old MySQL → `users` in new SQLite.
     *
     * Routes:
     *   GET    /migrate/users/preview  → preview()
     *   POST   /migrate/users          → migrate()
     *   GET    /migrate/users/unfilled → unfilledEmails()
     *   DELETE /migrate/users/reset    → reset()
     *
     * Column mapping (old → new):
     *   id             → (auto-increment, not mapped)
     *   lastname       → last_name
     *   firstname      → first_name
     *   middlename     → middle_name
     *   suffix         → suffix         (nullable, preserved)
     *   title          → honorifics     (direct map per user request)
     *   active         → active         (int → tinyInteger, compatible)
     *   password       → password       (hash preserved as-is)
     *   remember_token → remember_token
     *   created_at     → created_at     (preserved from old record)
     *   updated_at     → updated_at     (preserved from old record)
     *
     * Generated columns:
     *   name           → "{firstname} {lastname}" (display name)
     *   email          → placeholder: user.{id}@placeholder.email
     *                    ⚠ SQLite requires email to be unique + NOT NULL.
     *                      Placeholders are generated automatically.
     *                      Run GET /migrate/users/unfilled after migration
     *                      to get a list of all rows needing real emails.
     *   email_verified_at → null
     */

    // -------------------------------------------------------------------------
    // Preview
    // -------------------------------------------------------------------------

    public function preview(): JsonResponse
    {


        return response()->json([
            'table'     => 'users',
            'old_count' => DB::connection('old_db')->table('users')->count(),
            'new_count' => DB::table('users')->count(),
            'sample'    => DB::connection('old_db')
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
        $stats = $this->runMigration(
            newTable: 'users',
            oldTable: 'users',
            orderColumn: 'id',
            mapper: fn($r) => $this->mapRow($r),
            columnCount: 15,  // number of columns in mapRow() return value
            dedupeKey: 'id' // use old id for duplicate detection
        );

        return response()->json([
            'status'  => empty($stats['errors']) ? 'done' : 'done_with_errors',
            'note'    => 'Emails were auto-generated as placeholders. Call GET /migrate/users/unfilled to list them.',
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
            ->select(['id', 'name', 'email', 'first_name', 'last_name'])
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
        $deleted = DB::table('users')->delete();
        DB::table('users')->truncate();

        return response()->json(['status' => 'reset', 'deleted' => $deleted]);
    }

    // -------------------------------------------------------------------------
    // Row mapper
    // -------------------------------------------------------------------------

    private function mapRow(object $row): array
    {
        $firstName  = Str::title(str_replace('?', 'ñ', trim($row->firstname) ?? ''));
        $lastName   = Str::title(str_replace('?', 'ñ', trim($row->lastname) ?? ''));
        $middleName = Str::title(str_replace('?', 'ñ', trim($row->middlename) ?? ''));

        if (config('app.name') === 'HCGS') { // #HCGS - duplicate issue
            if (in_array($row->id, [6, 19, 7])) {
                $row->active = true;
            }

            if (in_array($row->id, [45, 47, 53])) { // Duplicates ()
                return [];
            }
        }

        return [
            'old_id' => $row->id,

            // Display name: "Firstname Lastname"
            'name' => "{$firstName} {$lastName}",

            // ⚠ Email placeholder — must be updated manually after migration.
            // Format: {firstName}@placeholder.email  (unique per row)
            'email'             => "{$row->id}@placeholder.email",
            'email_verified_at' => null,

            // Renamed columns
            'last_name'         => $lastName  ?: null,
            'first_name'        => $firstName ?: null,
            'middle_name'       => $middleName ?: null,

            // Nullable, preserved as-is
            'suffix'            => ($row->suffix !== '' && $row->suffix !== null)
                ? $row->suffix
                : null,

            // title → honorifics (direct map)
            'honorifics'        => ($row->title !== '' && $row->title !== null)
                ? $row->title
                : null,

            'active'            => (int) $row->active,
            'password'          => $row->password,
            'remember_token'    => $row->remember_token ?? null,

            // Preserve original timestamps from old DB if present
            'created_at'        => $row->created_at ?? now()->toDateTimeString(),
            'updated_at'        => $row->updated_at ?? now()->toDateTimeString(),
        ];
        // 14 columns — keep in sync with columnCount above
    }
}
