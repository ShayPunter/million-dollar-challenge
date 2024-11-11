<?php

namespace App\Http\Controllers;

use Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChallengeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_date' => 'required|date|after:today',
        ]);

        $challenge = Challenge::create([
            'target_date' => $validated['target_date'],
            'unique_id' => Str::random(9),
        ]);

        return response()->json($challenge, 201);
    }

    public function show($id)
    {
        $challenge = Challenge::where('unique_id', $id)
            ->with(['revenues' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();

        return response()->json($challenge);
    }

    public function progress($id)
    {
        $challenge = Challenge::where('unique_id', $id)->firstOrFail();
        $totalRevenue = $challenge->total_revenue;
        $completedCubes = floor($totalRevenue / 1000);
        $progressToNextCube = $totalRevenue % 1000;

        return response()->json([
            'total_revenue' => $totalRevenue,
            'completed_cubes' => $completedCubes,
            'progress_to_next_cube' => $progressToNextCube,
        ]);
    }
}
