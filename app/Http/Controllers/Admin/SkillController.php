<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\Level;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
       $skills = Skill::with('levels') 
        ->withCount(['levels', 'questions', 'videos'])
        ->orderBy('skill_name')
        ->get();
            
        $levels = Level::orderBy('level_order')->get();
        
        $stats = [
            'total' => Skill::count(),
            'active' => Skill::where('status', true)->count(),
            'total_questions' => \App\Models\Question::count(),
            'total_videos' => \App\Models\Video::count(),
        ];
        
        return view('admin.skills.index', compact('skills', 'levels', 'stats'));
    }

    public function create()
    {
        $levels = Level::orderBy('level_order')
            ->get();
            
        return view('admin.skills.form', compact('levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_name' => 'required|string|max:255|unique:skills',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'levels' => 'array',
            'levels.*' => 'exists:levels,level_id'
        ]);

        $skill = Skill::create([
            'skill_name' => $validated['skill_name'],
            'description' => $validated['description'] ?? null,
            'status' => $request->has('status'),
        ]);

        if ($request->has('levels')) {
            $skill->levels()->sync($request->levels);
        }

        return redirect()->route('admin.skills.index')
            ->with('success', 'Skill created successfully.');
    }

    public function edit(Skill $skill)
    {
        $levels = Level::orderBy('level_order')
            ->get();
            
        $skill->loadCount(['videos', 'questions', 'levels'])
              ->load('levels');
        
        return view('admin.skills.form', compact('skill', 'levels'));
    }

    public function show(Skill $skill)
{
    $skill->load([
        'levels' => function($query) {
            $query->orderBy('level_order');
        },
        'videos' => function($query) {
            $query->latest();
        },
        'questions' => function($query) {
            $query->withCount('answers')
                  ->latest();
        }
    ]);

    // Get statistics
    $totalVideos = $skill->videos->count();
    $totalQuestions = $skill->questions->count();
    $totalLevels = $skill->levels->count();
    
    $difficultyBreakdown = $skill->questions
        ->groupBy('difficulty')
        ->map(function ($questions, $difficulty) {
            return [
                'count' => $questions->count(),
                'total_points' => $questions->sum('points')
            ];
        });

    return view('admin.skills.show', compact(
        'skill', 
        'totalVideos', 
        'totalQuestions', 
        'totalLevels',
        'difficultyBreakdown'
    ));
}

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'skill_name' => 'required|string|max:255|unique:skills,skill_name,' . $skill->skill_id . ',skill_id',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'levels' => 'array',
            'levels.*' => 'exists:levels,level_id'
        ]);

        $skill->update([
            'skill_name' => $validated['skill_name'],
            'description' => $validated['description'] ?? null,
            'status' => $request->has('status'),
        ]);

        if ($request->has('levels')) {
            $skill->levels()->sync($request->levels);
        } else {
            $skill->levels()->detach();
        }

        return redirect()->route('admin.skills.index')
            ->with('success', 'Skill updated successfully.');
    }

    public function destroy(Skill $skill)
    {
        if ($skill->questions()->exists() || $skill->videos()->exists()) {
            return back()->with('error', 'Cannot delete skill with associated questions or videos.');
        }

        $skill->levels()->detach();
        $skill->delete();

        return redirect()->route('admin.skills.index')
            ->with('success', 'Skill deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:skills,skill_id'
        ]);

        
        return response()->json(['success' => true]);
    }

    public function getLevels(Skill $skill)
    {
        $levels = $skill->levels()->orderBy('level_order')->get();
        return response()->json(['levels' => $levels]);
    }
}