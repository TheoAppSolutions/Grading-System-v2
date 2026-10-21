<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GitUpdater;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UpdaterController extends Controller
{
    public function __construct(private readonly GitUpdater $updater) {}

    public function index(): View
    {
        $this->ensureAdmin();

        try {
            $status = $this->updaterStatus();
        } catch (\Throwable $exception) {
            report($exception);
            $status = ['error' => 'Unable to check the remote repository.'];
        }

        return view('admin.updater', compact('status'));
    }

    public function pull(): RedirectResponse
    {
        $this->ensureAdmin();

        try {
            $result = $this->updater->update();

            return to_route('admin.updater')->with(
                $result['updated'] ? 'success' : 'status',
                $result['message'],
            );
        } catch (\Throwable $exception) {
            report($exception);

            return to_route('admin.updater')->with('error', $exception->getMessage());
        }
    }

    private function updaterStatus(): array
    {
        return $this->updater->status();
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}
