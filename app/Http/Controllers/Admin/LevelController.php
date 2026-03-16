<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LevelController extends Controller
{
    public function index()
{
    $levels = Level::withCount(['users', 'skills'])
        ->with('skills')
        ->orderBy('level_order')
        ->paginate(10);

    $levels->getCollection()->transform(function ($level) {
        $skillIds = $level->skills->pluck('skill_id');

        $level->questions_count = $skillIds->isNotEmpty()
            ? \App\Models\Question::whereIn('skill_id', $skillIds)->count()
            : 0;

        $level->videos_count = $skillIds->isNotEmpty()
            ? \App\Models\Video::whereIn('skill_id', $skillIds)->count()
            : 0;

        return $level;
    });

    return view('admin.levels.index', compact('levels'));
}

    public function create()
    {
        $maxOrder = Level::max('level_order') ?? 0;
        $nextOrder = $maxOrder + 1;

        return view('admin.levels.form', compact('nextOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'level_name' => 'required|string|max:255',
            'level_order' => 'required|integer|unique:levels,level_order',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $lastId = Level::max('level_id') ?? 0;
            $validated['level_id'] = $lastId + 1;

            Level::create($validated);

            DB::commit();

            return redirect()
                ->route('admin.levels.index')
                ->with('success', 'Level created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create level. ' . $e->getMessage());
        }
    }

    public function edit(Level $level)
    {
        return view('admin.levels.form', compact('level'));
    }

    public function update(Request $request, Level $level)
    {
        $validated = $request->validate([
            'level_name' => 'required|string|max:255',
            'level_order' => [
                'required',
                'integer',
                Rule::unique('levels', 'level_order')->ignore($level->level_id, 'level_id')
            ],
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $level->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.levels.index')
                ->with('success', 'Level updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update level. ' . $e->getMessage());
        }
    }

    public function destroy(Level $level)
{
    if ($level->users()->exists()) {
        $userCount = $level->users()->count();

        return redirect()
            ->route('admin.levels.index')
            ->with('error', "Cannot delete level because it has {$userCount} associated user(s). Please reassign or remove these users first.");
    }

    $skillIds = $level->skills()->pluck('skills.skill_id');

    $videoCount = $skillIds->isNotEmpty()
        ? \App\Models\Video::whereIn('skill_id', $skillIds)->count()
        : 0;

    if ($videoCount > 0) {
        return redirect()
            ->route('admin.levels.index')
            ->with('error', "Cannot delete level because it has {$videoCount} associated video(s). Please delete or reassign these videos first.");
    }

    $questionCount = $skillIds->isNotEmpty()
        ? \App\Models\Question::whereIn('skill_id', $skillIds)->count()
        : 0;

    if ($questionCount > 0) {
        return redirect()
            ->route('admin.levels.index')
            ->with('error', "Cannot delete level because it has {$questionCount} associated question(s). Please delete or reassign these questions first.");
    }

    try {
        DB::beginTransaction();

        $level->skills()->detach();
        $level->delete();

        DB::commit();

        return redirect()
            ->route('admin.levels.index')
            ->with('success', 'Level deleted successfully.');
    } catch (\Exception $e) {
        DB::rollBack();

        return back()->with('error', 'Failed to delete level. ' . $e->getMessage());
    }
}

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'levels' => 'required|array',
            'levels.*.level_id' => 'required|exists:levels,level_id',
            'levels.*.level_order' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['levels'] as $item) {
                Level::where('level_id', $item['level_id'])
                    ->update(['level_order' => $item['level_order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Levels reordered successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder levels.'
            ], 500);
        }
    }
}
