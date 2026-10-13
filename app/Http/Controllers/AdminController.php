<?php

namespace App\Http\Controllers;

use App\Models\Revenue;
use App\Models\User;
use App\Models\UserPage;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __invoke()
    {
        $users = User::query()
            ->with('userPage:id,user_id,slug,title')
            ->withCount('projects')
            ->withSum('revenues', 'amount')
            ->latest()
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'google' => $user->google_id !== null,
                'page' => $user->userPage ? [
                    'slug' => $user->userPage->slug,
                    'title' => $user->userPage->title,
                ] : null,
                'projectsCount' => $user->projects_count,
                'totalRevenue' => (float) ($user->revenues_sum_amount ?? 0),
                'createdAt' => $user->created_at,
            ]);

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'stats' => [
                'totalUsers' => $users->count(),
                'newThisWeek' => User::where('created_at', '>=', now()->subWeek())->count(),
                'totalPages' => UserPage::count(),
                'totalRevenue' => (float) Revenue::sum('amount'),
            ],
        ]);
    }
}
