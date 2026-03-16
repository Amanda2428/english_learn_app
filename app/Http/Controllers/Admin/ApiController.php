<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getSkillLevels($skillId)
    {
        $skill = Skill::with('levels')->findOrFail($skillId);
        return response()->json($skill->levels);
    }
}