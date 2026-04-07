@extends('layouts.user')

@section('title', 'My Learning Progress')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-chart-line text-blue-600 mr-3"></i>My Learning Progress
        </h1>
        <p class="text-gray-600">Track your improvement across levels, skills, mastery, and study time.</p>
    </div>

    <!-- Overall Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <h6 class="text-gray-500 text-sm font-medium">Total Points</h6>
                <i class="fas fa-trophy text-yellow-500 text-xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($overallStats->total_points) }}</h2>
            <small class="text-gray-600">{{ $overallStats->skills_started }} active skills</small>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <h6 class="text-gray-500 text-sm font-medium">Average Completion</h6>
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-green-600 mb-1">{{ $overallStats->avg_completion }}%</h2>
            <small class="text-gray-600">{{ $overallStats->completed_skills }} completed skills</small>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <h6 class="text-gray-500 text-sm font-medium">Mastery Rate</h6>
                <i class="fas fa-brain text-blue-500 text-xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-blue-600 mb-1">{{ $overallStats->mastery_rate }}%</h2>
            <small class="text-gray-600">
                {{ $overallStats->questions_mastered }}/{{ $overallStats->total_questions }} questions mastered
            </small>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-3">
                <h6 class="text-gray-500 text-sm font-medium">Time Spent</h6>
                <i class="fas fa-clock text-purple-500 text-xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-purple-600 mb-1">{{ $overallStats->total_time_spent }} min</h2>
            <small class="text-gray-600">{{ $overallStats->hours_spent }}h {{ $overallStats->minutes_spent }}m total</small>
        </div>
    </div>

    @if($recommendations->count() > 0)
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
            <h4 class="text-xl font-bold mb-4">
                <i class="fas fa-lightbulb mr-2"></i>Recommended for You
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($recommendations as $rec)
                <a href="{{ $rec->action_url }}" class="block bg-white/20 rounded-xl p-4 hover:bg-white/30 transition-all">
                    <i class="fas fa-{{ $rec->icon }} text-2xl mb-2"></i>
                    <h6 class="font-semibold mb-1">{{ $rec->title }}</h6>
                    <small class="text-blue-100">{{ $rec->message }}</small>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Charts -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>Learning Momentum
                </h4>
            </div>
            <canvas id="pointsChart" height="120"></canvas>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-chart-pie text-purple-600 mr-2"></i>Progress Breakdown
                </h4>
            </div>
            <canvas id="overallProgressChart" height="120"></canvas>
            <div class="text-center mt-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="border-r border-gray-200">
                        <h5 class="text-2xl font-bold text-green-600 mb-1">{{ $overallStats->completed_skills }}</h5>
                        <small class="text-gray-600">Completed Skills</small>
                    </div>
                    <div>
                        <h5 class="text-2xl font-bold text-blue-600 mb-1">{{ max($overallStats->skills_started - $overallStats->completed_skills, 0) }}</h5>
                        <small class="text-gray-600">In Progress</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-chart-bar text-green-600 mr-2"></i>Skill Improvement
                </h4>
            </div>
            <canvas id="masteryChart" height="100"></canvas>
        </div>
    </div>

    <!-- Level Progress -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="border-b border-gray-100 p-6">
                <h4 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-layer-group text-yellow-600 mr-2"></i>Level Progress
                </h4>
                <p class="text-gray-500 text-sm mt-1">Progress is based on all skills assigned to each level.</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($levelsProgress as $level)
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl p-5 border border-gray-100 hover:shadow-lg transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold">
                                        {{ $level->level_order }}
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-gray-900 text-lg">{{ $level->level_name }}</h5>
                                        <p class="text-xs text-gray-500">Level {{ $level->level_order }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($level->completion == 100)
                                <i class="fas fa-trophy text-yellow-500 text-2xl"></i>
                            @elseif($level->completion > 0)
                                <i class="fas fa-chart-line text-green-500 text-2xl"></i>
                            @else
                                <i class="fas fa-flag-checkered text-gray-400 text-2xl"></i>
                            @endif
                        </div>

                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Progress</span>
                                <span class="font-semibold text-gray-900">{{ $level->completion }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-full h-2 transition-all duration-500" style="width: {{ $level->completion }}%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center">
                                <p class="text-lg font-bold text-blue-600">{{ $level->completed_skills }}</p>
                                <small class="text-gray-500 text-xs">Completed</small>
                            </div>
                            <div class="text-center border-l border-r border-gray-200">
                                <p class="text-lg font-bold text-purple-600">{{ $level->points }}</p>
                                <small class="text-gray-500 text-xs">Points</small>
                            </div>
                            <div class="text-center">
                                <p class="text-lg font-bold text-green-600">{{ $level->mastery }}%</p>
                                <small class="text-gray-500 text-xs">Mastery</small>
                            </div>
                        </div>

                        <div class="mt-3 text-sm text-gray-500">
                            {{ $level->questions_mastered }}/{{ $level->total_questions }} questions mastered
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-xs text-gray-500">
                                {{ $level->completed_skills }}/{{ $level->total_skills }} skills completed
                            </span>
                            <a href="{{ route('user.levels.show', $level->level_id) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                Explore Level <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Skills Detailed Progress -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="border-b border-gray-100 p-6">
                <h4 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-table text-blue-600 mr-2"></i>Active Skills Progress
                </h4>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Skill</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Level</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Progress</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Videos</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Mastery</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Time</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Points</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($skillsProgress as $skill)
                        <tr class="hover:bg-gray-50 transition-all">
                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-900">{{ $skill->skill_name }}</div>
                            </td>

                            <td class="px-4 py-4 text-gray-600">{{ $skill->level_name }}</td>

                            <td class="px-4 py-4 min-w-[180px]">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-full h-2 transition-all duration-500" style="width: {{ $skill->completion }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-700 min-w-[48px]">{{ $skill->completion }}%</span>
                                </div>
                            </td>

                            <td class="px-4 py-4 text-gray-600">
                                @if($skill->total_videos > 0)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-video text-blue-500"></i>
                                        <span>{{ $skill->videos_watched }}/{{ $skill->total_videos }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                @if($skill->total_questions > 0)
                                    @php
                                        $masteryColor = $skill->mastery >= 70 ? 'bg-green-100 text-green-700' : ($skill->mastery >= 40 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700');
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $masteryColor }}">
                                            {{ $skill->mastery }}%
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            ({{ $skill->questions_answered }}/{{ $skill->total_questions }})
                                        </span>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">No questions</span>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-gray-600">{{ $skill->time_spent_minutes }} min</td>

                            <td class="px-4 py-4 font-semibold text-gray-900">{{ number_format($skill->points) }}</td>

                            <td class="px-4 py-4">
                                @if($skill->status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <i class="fas fa-check-circle mr-1"></i> Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        <i class="fas fa-play-circle mr-1"></i> In Progress
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <a href="{{ route('user.skills.show', $skill->skill_id) }}"
                                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-all">
                                    {{ $skill->status === 'completed' ? 'Review' : 'Continue' }}
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-gray-500">
                                <i class="fas fa-folder-open text-4xl mb-3"></i>
                                <p>No active progress yet. Start learning to see your stats!</p>
                                <a href="{{ route('user.skills.index') }}" class="inline-flex items-center mt-3 text-blue-600 hover:text-blue-700">
                                    Browse Skills <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="border-b border-gray-100 p-6">
                <h4 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-history text-purple-600 mr-2"></i>Recent Activity
                </h4>
            </div>
            <div class="p-6">
                @if($recentActivity->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentActivity as $activity)
                        <div class="flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 transition-all">
                            <div class="flex-shrink-0">
                                @if($activity->type === 'completed')
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-green-600"></i>
                                    </div>
                                @elseif($activity->type === 'started')
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-play text-blue-600"></i>
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-chart-line text-purple-600"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $activity->message }}</p>
                                        <p class="text-sm text-gray-500 mt-1">{{ $activity->level_name }}</p>
                                    </div>
                                    <small class="text-gray-500">
                                        {{ \Carbon\Carbon::parse($activity->date)->diffForHumans() }}
                                    </small>
                                </div>

                                <div class="flex flex-wrap gap-4 mt-3 text-sm text-gray-600">
                                    <span>
                                        <i class="fas fa-percent text-blue-500 mr-1"></i>
                                        {{ $activity->completion }}% complete
                                    </span>
                                    <span>
                                        <i class="fas fa-brain text-green-500 mr-1"></i>
                                        {{ $activity->questions_mastered }}/{{ $activity->total_questions }} mastered
                                    </span>
                                    <span>
                                        <i class="fas fa-clock text-purple-500 mr-1"></i>
                                        {{ $activity->time_spent_minutes }} min
                                    </span>
                                    <span>
                                        <i class="fas fa-star text-yellow-500 mr-1"></i>
                                        {{ number_format($activity->points) }} points
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <i class="fas fa-smile-wink text-5xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No recent activity yet.</p>
                        <a href="{{ route('user.skills.index') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                            Start Learning <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pointsCtx = document.getElementById('pointsChart');
    if (pointsCtx) {
        new Chart(pointsCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($pointsHistory->pluck('date')->map(function ($date) {
                    return \Carbon\Carbon::parse($date)->format('M d');
                })) !!},
                datasets: [{
                    label: 'Points Earned',
                    data: {!! json_encode($pointsHistory->pluck('points')) !!},
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37, 99, 235, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#2563EB'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Points'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                }
            }
        });
    }

    const masteryCtx = document.getElementById('masteryChart');
    if (masteryCtx) {
        new Chart(masteryCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($skillMastery->pluck('skill_name')) !!},
                datasets: [
                    {
                        label: 'Completion %',
                        data: {!! json_encode($skillMastery->pluck('completion')) !!},
                        backgroundColor: 'rgba(34, 197, 94, 0.75)',
                        borderRadius: 8
                    },
                    {
                        label: 'Mastery %',
                        data: {!! json_encode($skillMastery->pluck('mastery')) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.75)',
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Percentage (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Skills'
                        }
                    }
                }
            }
        });
    }

    const overallCtx = document.getElementById('overallProgressChart');
    if (overallCtx) {
        new Chart(overallCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Completed Skills', 'In Progress'],
                datasets: [{
                    data: [
                        {{ $overallStats->completed_skills }},
                        {{ max($overallStats->skills_started - $overallStats->completed_skills, 0) }}
                    ],
                    backgroundColor: [
                        '#22C55E',
                        '#3B82F6'
                    ],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }

    canvas {
        max-height: 320px;
        width: 100% !important;
    }

    .hover\:shadow-lg:hover,
    .hover\:shadow-md:hover {
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.08), 0 6px 10px -6px rgba(0, 0, 0, 0.08);
    }

    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
</style>
@endpush