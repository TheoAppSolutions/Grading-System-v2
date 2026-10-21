<?php

namespace App\Providers;

use App\Policies\ActivityPolicy;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Activity::class => ActivityPolicy::class,
    ];

    public function boot(): void
    {
        Gate::define('create-backup', fn (User $user): bool => $user->isAdmin());
        Gate::define('download-backup', fn (User $user): bool => $user->isAdmin());
        Gate::define('delete-backup', fn (User $user): bool => $user->isAdmin());
    }
}
