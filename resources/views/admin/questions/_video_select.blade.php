@props(['selectedVideoIds' => [], 'skillId' => null])

@php
    $selectedVideoId = is_array($selectedVideoIds) ? $selectedVideoIds[0] ?? null : $selectedVideoIds;
@endphp

<div x-data="videoSelector()" x-init="init({{ json_encode($selectedVideoId) }}, {{ $skillId ?? 'null' }})" class="space-y-4">

    <label class="block text-sm font-medium text-gray-700 mb-2">
        Associated Video (Optional)
    </label>

    <!-- Video Search/Filter -->
    <div class="flex gap-2">
        <select x-model="filters.level_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">All Levels</option>
            @foreach (App\Models\Level::orderBy('level_order')->get() as $level)
                <option value="{{ $level->level_id }}">{{ $level->level_name }}</option>
            @endforeach
        </select>

        <input type="text" x-model="filters.search" placeholder="Search videos..."
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-4 text-gray-500">
        <svg class="animate-spin h-5 w-5 mx-auto text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
        <span class="text-sm">Loading videos...</span>
    </div>

    <!-- Available Videos -->
    <div x-show="!loading" class="border border-gray-200 rounded-lg max-h-60 overflow-y-auto p-2">
        <template x-for="video in filteredVideos" :key="video.video_id">
            <div class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer"
                :class="{ 'bg-blue-50': selectedVideo == video.video_id }" @click="selectVideo(video)">
                <input type="radio" name="video_selection" :value="video.video_id" x-model="selectedVideo"
                    :id="'video_' + video.video_id" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">

                <label :for="'video_' + video.video_id" class="ml-3 flex-1 flex items-center cursor-pointer">
                    <svg class="w-5 h-5 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900" x-text="video.title"></p>
                        <p class="text-xs text-gray-500">
                            <span x-text="getLevelName(video)"></span>
                            <span x-text="' • ' + (video.duration_formatted || video.duration)"></span>
                        </p>
                    </div>
                </label>
            </div>
        </template>

        <!-- Option for no video -->
        <div class="flex items-center p-2 hover:bg-gray-50 rounded border-t border-gray-100 mt-2 cursor-pointer"
            :class="{ 'bg-blue-50': selectedVideo === null }" @click="selectNoVideo()">
            <input type="radio" name="video_selection" :value="null" x-model="selectedVideo" id="video_none"
                class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
            <label for="video_none" class="ml-3 flex-1 flex items-center cursor-pointer">
                <svg class="w-5 h-5 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                    </path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-gray-900">No Video</p>
                    <p class="text-xs text-gray-500">Question without video association</p>
                </div>
            </label>
        </div>

        <div x-show="!loading && filteredVideos.length === 0" class="text-center py-8 text-gray-500">
            No videos found
        </div>
    </div>

    <!-- Hidden input for form submission -->
    <input type="hidden" name="video_id" :value="selectedVideo">

    <!-- Selected Video Summary -->
    <div x-show="selectedVideo && selectedVideoDetails" class="bg-blue-50 p-3 rounded-lg">
        <p class="text-sm font-medium text-blue-800 mb-2">Selected Video:</p>
        <div class="flex items-center justify-between">
            <div class="flex items-center flex-1">
                <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                    </path>
                </svg>
                <div class="flex-1">
                    <span class="text-sm text-blue-800 font-medium block" x-text="selectedVideoDetails.title"></span>
                    <p class="text-xs text-blue-600">
                        <span x-text="selectedVideoDetails.levelName"></span>
                        <span x-text="' • ' + selectedVideoDetails.duration"></span>
                    </p>
                </div>
            </div>
            <button @click="selectNoVideo()" type="button"
                class="text-blue-600 hover:text-blue-800 ml-2 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Show message when no video selected -->
    <div x-show="!selectedVideo" class="text-sm text-gray-500 italic">
        No video selected
    </div>
</div>

<script>
    function videoSelector() {
        return {
            videos: [],
            levels: [],
            selectedVideo: null,
            selectedVideoDetails: null,
            loading: true,
            filters: {
                search: '',
                level_id: '',
                skill_id: null
            },

            async init(selectedVideoId, skillId) {
                // Set selected video
                this.selectedVideo = selectedVideoId ? Number(selectedVideoId) : null;
                this.filters.skill_id = skillId ? Number(skillId) : null;

                // Load all data
                await this.loadAllData();

                this.loading = false;
            },

            async loadAllData() {
                try {
                    await Promise.all([
                        this.fetchLevels(),
                        this.fetchVideos()
                    ]);

                    await this.setupSelectedVideo();

                } catch (error) {
                    console.error('Error loading data:', error);
                }
            },

            async fetchLevels() {
                try {
                    const response = await fetch('/admin/api/levels');
                    if (response.ok) {
                        this.levels = await response.json();
                    }
                } catch (error) {
                    console.error('Error loading levels:', error);
                    this.levels = [];
                }
            },


            async fetchVideos() {
                try {
                    const url = '/admin/api/videos/all';

                    const response = await fetch(url);
                    if (response.ok) {
                        const data = await response.json();
                        this.videos = data.map(video => this.processVideo(video));
                    }
                } catch (error) {
                    console.error('Error loading videos:', error);
                    this.videos = [];
                }
            },

            processVideo(video) {
                let levelId = video.level_id;
                if (!levelId && video.description) {
                    const match = video.description.match(/<!-- LEVEL:(\d+) -->/);
                    if (match) {
                        levelId = parseInt(match[1]);
                    }
                }

                return {
                    ...video,
                    video_id: Number(video.video_id),
                    level_id: levelId,
                    duration_formatted: this.formatDuration(video.duration)
                };
            },

            async setupSelectedVideo() {
                if (!this.selectedVideo) return;

                let video = this.videos.find(v => v.video_id === this.selectedVideo);

                if (!video) {
                    try {
                        const response = await fetch(`/admin/api/video/${this.selectedVideo}`);
                        if (response.ok) {
                            const data = await response.json();
                            video = this.processVideo(data);
                            this.videos.push(video);
                        }
                    } catch (error) {
                        console.error('Error fetching video:', error);
                    }
                }

                if (video) {
                    this.updateSelectedVideoDetails(video);
                }
            },

            selectVideo(video) {
                this.selectedVideo = video.video_id;
                this.updateSelectedVideoDetails(video);
            },

            selectNoVideo() {
                this.selectedVideo = null;
                this.selectedVideoDetails = null;
            },

            updateSelectedVideoDetails(video) {
                if (video) {
                    this.selectedVideoDetails = {
                        title: video.title,
                        levelName: this.getLevelName(video),
                        duration: video.duration_formatted || video.duration || '0:00'
                    };
                }
            },

            formatDuration(duration) {
                if (!duration) return '0:00';
                if (typeof duration === 'number') {
                    const minutes = Math.floor(duration / 60);
                    const seconds = duration % 60;
                    return `${minutes}:${seconds.toString().padStart(2, '0')}`;
                }
                if (typeof duration === 'string') {
                    if (duration.includes(':')) return duration;
                    const seconds = parseInt(duration);
                    if (!isNaN(seconds)) {
                        const minutes = Math.floor(seconds / 60);
                        const remainingSeconds = seconds % 60;
                        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
                    }
                }
                return duration || '0:00';
            },

            get filteredVideos() {
                return this.videos.filter(video => {
                    // Search filter
                    if (this.filters.search && !video.title.toLowerCase().includes(this.filters.search
                            .toLowerCase())) {
                        return false;
                    }
                    // Level filter
                    if (this.filters.level_id) {
                        const videoLevelId = this.getVideoLevelId(video);
                        if (videoLevelId !== parseInt(this.filters.level_id)) {
                            return false;
                        }
                    }
                    return true;
                });
            },

            getVideoLevelId(video) {
                return video.level_id !== undefined && video.level_id !== null ? video.level_id : null;
            },

            getLevelName(video) {
                const levelId = this.getVideoLevelId(video);

                if (levelId && this.levels.length > 0) {
                    const level = this.levels.find(l => l.level_id === levelId);
                    return level ? level.level_name : `Level ${levelId}`;
                }
                return 'No Level';
            }
        }
    }
</script>
