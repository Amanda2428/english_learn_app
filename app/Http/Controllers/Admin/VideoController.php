<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Skill;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::with('skill');

        if ($request->filled('skill_id')) {
            $query->where('skill_id', $request->skill_id);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $videos = $query->orderBy('created_at', 'desc')->paginate(12);
        $skills = Skill::all();

        return view('admin.videos.index', compact('videos', 'skills'));
    }

    public function create()
    {
        $skills = Skill::with('levels')->get();
        $levels = Level::all();
        
        return view('admin.videos.form', compact('skills', 'levels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_id' => 'nullable|exists:skills,skill_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_file' => 'required|file|mimes:mp4,mov,avi,wmv,flv,mkv|max:102400', // 100MB max
            'duration' => 'required|date_format:H:i:s',
            'level_id' => 'nullable|exists:levels,level_id',
        ]);

        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('videos', 'public');
            $validated['video_file'] = $path;
        }

        if (!empty($validated['level_id'])) {
            $levelMarker = "<!-- LEVEL:{$validated['level_id']} -->";
            $validated['description'] = $levelMarker . ($validated['description'] ?? '');
        }

        unset($validated['level_id']);

        Video::create($validated);

        return redirect()->route('admin.videos.index')
            ->with('success', 'Video uploaded successfully.');
    }

    public function edit(Video $video)
    {
        $skills = Skill::with('levels')->get();
        $levels = Level::all();
        
        $video->level_id = $video->level_id;
        
        return view('admin.videos.form', compact('video', 'skills', 'levels'));
    }

    public function update(Request $request, Video $video)
    {
        $validated = $request->validate([
            'skill_id' => 'nullable|exists:skills,skill_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv,flv,mkv|max:102400',
            'duration' => 'required|date_format:H:i:s',
            'level_id' => 'nullable|exists:levels,level_id',
        ]);

        if ($request->hasFile('video_file')) {

            if ($video->video_file) {
                Storage::disk('public')->delete($video->video_file);
            }
            
            $path = $request->file('video_file')->store('videos', 'public');
            $validated['video_file'] = $path;
        }

        $description = $validated['description'] ?? '';
        
        $description = preg_replace('/<!-- LEVEL:\d+ -->/', '', $description);
        
        if (!empty($validated['level_id'])) {
            $description = "<!-- LEVEL:{$validated['level_id']} -->" . $description;
        }
        
        $validated['description'] = $description;
        unset($validated['level_id']); 

        $video->update($validated);

        return redirect()->route('admin.videos.index')
            ->with('success', 'Video updated successfully.');
    }

    public function destroy(Video $video)
    {
        if ($video->video_file) {
            Storage::disk('public')->delete($video->video_file);
        }
        
        $video->delete();

        return redirect()->route('admin.videos.index')
            ->with('success', 'Video deleted successfully.');
    }

    public function show(Video $video)
    {
        $video->load(['skill', 'questions']);
        return view('admin.videos.show', compact('video'));
    }
}