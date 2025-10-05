<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    /**
     * Get all exercises
     */
    public function index(Request $request)
    {
        $query = Exercise::query()->where('is_active', true);

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by muscle group
        if ($request->has('muscle_group')) {
            $query->where('muscle_group', $request->muscle_group);
        }

        // Filter by equipment
        if ($request->has('equipment')) {
            $query->where('equipment', $request->equipment);
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $exercises = $query->orderBy('name')->get();

        return response()->json([
            'exercises' => $exercises,
        ]);
    }

    /**
     * Search exercises by name or aliases
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $searchTerm = strtolower($validated['query']);

        $exercises = Exercise::where('is_active', true)
            ->where(function ($query) use ($searchTerm) {
                $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereRaw('JSON_SEARCH(LOWER(aliases), "one", ?) IS NOT NULL', ["%{$searchTerm}%"]);
            })
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json([
            'exercises' => $exercises,
        ]);
    }

    /**
     * Get a single exercise
     */
    public function show($id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Exercise not found',
            ], 404);
        }

        return response()->json([
            'exercise' => $exercise,
        ]);
    }

    /**
     * Create a new exercise
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:exercises,name',
            'aliases' => 'nullable|array',
            'category' => 'required|string',
            'muscle_group' => 'required|string',
            'equipment' => 'nullable|string',
            'difficulty' => 'nullable|string|in:beginner,intermediate,advanced',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'tags' => 'nullable|array',
        ]);

        $exercise = Exercise::create($validated);

        return response()->json([
            'success' => true,
            'exercise' => $exercise,
            'message' => 'Exercise created successfully',
        ], 201);
    }

    /**
     * Update an exercise
     */
    public function update(Request $request, $id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Exercise not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|unique:exercises,name,' . $id,
            'aliases' => 'nullable|array',
            'category' => 'sometimes|string',
            'muscle_group' => 'sometimes|string',
            'equipment' => 'nullable|string',
            'difficulty' => 'nullable|string|in:beginner,intermediate,advanced',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'tags' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $exercise->update($validated);

        return response()->json([
            'success' => true,
            'exercise' => $exercise,
            'message' => 'Exercise updated successfully',
        ]);
    }

    /**
     * Delete an exercise (soft delete by setting is_active to false)
     */
    public function destroy($id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Exercise not found',
            ], 404);
        }

        $exercise->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Exercise deactivated successfully',
        ]);
    }
}
