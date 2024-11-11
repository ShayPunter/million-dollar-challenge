<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Revenue;
use Challenge;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $project = Project::findOrFail($validated['project_id']);

        if ($project->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $revenue = $request->user()->revenues()->create([
            'project_id' => $validated['project_id'],
            'amount' => $validated['amount'],
        ]);

        return redirect()->to('/dashboard');
    }

    public function index($id)
    {
        $challenge = Challenge::where('unique_id', $id)->firstOrFail();
        $revenues = $challenge->revenues()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($revenues);
    }

    public function update(Request $request, Revenue $revenue)
    {
        // Ensure the revenue belongs to the authenticated user
        if ($revenue->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $revenue->update($validated);

        return redirect()->back();
    }

    public function destroy(Request $request, Revenue $revenue)
    {
        // Ensure the revenue belongs to the authenticated user
        if ($revenue->user_id !== $request->user()->id) {
            abort(403);
        }

        $revenue->delete();

        return redirect()->back();
    }
}
