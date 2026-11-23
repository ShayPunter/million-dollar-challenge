<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $hasPage = $user->userPage()->exists();

        $page = null;
        if ($hasPage) {
            $page = $user->userPage;
        }

        // Get projects with their total revenue. The dashboard only shows each
        // revenue's date and amount (and edits it by id), so load just those (#5751).
        $projects = $user->projects()
            ->with(['revenues' => fn ($query) => $query->select('id', 'project_id', 'amount', 'created_at')])
            ->withSum('revenues as amount', 'amount')
            ->get();

        // Calculate total progress
        $totalRevenue = $user->revenues()->sum('amount');
        $completedCubes = floor($totalRevenue / 1000);
        $progressToNextCube = $totalRevenue % 1000;

        return Inertia::render('Dashboard', [
            'hasPage' => $hasPage,
            'projects' => $projects,
            'stats' => [
                'totalRevenue' => $totalRevenue,
                'completedCubes' => $completedCubes,
                'progressToNextCube' => $progressToNextCube,
            ],
            'page' => $page ? [
                'title' => $page->title,
                'description' => $page->description,
                'slug' => $page->slug,
            ] : null,
        ]);
    }
}
