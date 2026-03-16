<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoApiController extends Controller
{
    public function index(Request $request)
    {
        $videos = Video::with('skill')->get()->map(function($video) {
            return [
                'video_id' => $video->video_id,
                'skill_id' => $video->skill_id,
                'title' => $video->title,
                'description' => $video->description,
                'duration' => $video->duration,
                'level_id' => $video->level_id,
                'level_name' => $video->level_name,
                'skill_name' => $video->skill->skill_name ?? null,
            ];
        });

        return response()->json($videos);
    }
}