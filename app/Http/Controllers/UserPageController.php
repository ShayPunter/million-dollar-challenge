<?php

namespace App\Http\Controllers;

use App\Models\UserPage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserPageController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->userPage) {
            return response()->json(['message' => 'User already has a page'], 422);
        }

        $validated = $request->validate([
            'slug' => 'required|unique:user_pages|alpha_dash|min:3|max:50',
            'title' => 'required|max:100',
            'description' => 'required|max:1000',
        ]);

        $page = $user->userPage()->create($validated);

        return redirect()->to('/dashboard');
    }

    public function show($slug)
    {
        $page = UserPage::where('slug', $slug)
            ->with(['user.projects' => function ($query) {
                $query->withSum('revenues', 'amount');
            }])
            ->firstOrFail();

        $lastUpdate = $page->user->revenues()
            ->latest()
            ->first();

        $totalRevenue = $page->user->revenues()->sum('amount');

        $stats = [
            'total_revenue' => $totalRevenue,
            'completed_cubes' => floor($totalRevenue / 1000),
            'progress_to_next_cube' => $totalRevenue % 1000,
            'last_updated' => $lastUpdate,
            'user' => $page->user,
            'projects' => $page->user->projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'total' => $project->revenues_sum_amount ?? 0,
                ];
            })
        ];

        return Inertia::render('PublicPage', [
            'page' => [
                'title' => $page->title,
                'description' => $page->description,
            ],
            'stats' => $stats,
        ]);
    }

    public function update(Request $request)
    {
        $page = $request->user()->userPage;

        if (!$page) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        $page->update($validated);

        return redirect()->back();
    }
}
