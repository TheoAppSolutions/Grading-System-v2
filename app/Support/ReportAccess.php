<?php

namespace App\Support;

use App\Models\Enrollment;
use App\Models\Homeroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class ReportAccess
{
    public static function canAccessResource(?User $user, ?int $schoolYearId): bool
    {
        if (! $user || ! $schoolYearId) {
            return false;
        }

        return $user->isOffice()
            || self::homeroomAssignments($user, $schoolYearId)->exists();
    }

    public static function canViewEnrollment(?User $user, Enrollment $enrollment): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isOffice()) {
            return true;
        }

        return self::homeroomAssignments($user, (int) $enrollment->school_year_id)
            ->where('grade_level_id', $enrollment->grade_level_id)
            ->exists();
    }

    public static function canViewGradeLevel(?User $user, int $schoolYearId, int $gradeLevelId): bool
    {
        if (! $user) {
            return false;
        }

        return $user->isOffice()
            || self::homeroomAssignments($user, $schoolYearId)
                ->where('grade_level_id', $gradeLevelId)
                ->exists();
    }

    public static function canViewStudentHistory(?User $user): bool
    {
        return $user?->isOffice() === true;
    }

    /**
     * Return the grade levels a user may see for a school year.
     * A null result means the user may see every grade level.
     */
    public static function accessibleGradeLevelIds(?User $user, ?int $schoolYearId): ?array
    {
        if (! $user || ! $schoolYearId) {
            return [];
        }

        if ($user->isOffice()) {
            return null;
        }

        return self::homeroomAssignments($user, $schoolYearId)
            ->distinct()
            ->pluck('grade_level_id')
            ->map(fn ($gradeLevelId): int => (int) $gradeLevelId)
            ->all();
    }

    /**
     * Limit a student query to enrollments visible to the current user.
     */
    public static function scopeStudents(Builder $query, ?User $user, ?int $schoolYearId): Builder
    {
        $gradeLevelIds = self::accessibleGradeLevelIds($user, $schoolYearId);

        return $query->whereHas('enrollments', function (Builder $enrollmentQuery) use ($schoolYearId, $gradeLevelIds): void {
            $enrollmentQuery
                ->where('school_year_id', $schoolYearId)
                ->when(
                    $gradeLevelIds !== null,
                    fn (Builder $query) => $query->whereIn('grade_level_id', $gradeLevelIds ?: [-1])
                );
        });
    }

    private static function homeroomAssignments(User $user, int $schoolYearId): Builder
    {
        return Homeroom::query()
            ->where('school_year_id', $schoolYearId)
            ->where(function (Builder $query) use ($user): void {
                $query
                    ->where('user_id', $user->getKey())
                    ->orWhere('user2_id', $user->getKey());
            });
    }
}
