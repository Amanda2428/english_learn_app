@props(['selectedVideoIds' => [], 'skillId' => null])

<div x-data="videoSelector()" x-init="init({{ json_encode($selectedVideoIds) }}, {{ $skillId ?? 'null' }})" class="space-y-4">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Associated Video (Optional)
    </label>
    
    <!-- Video Search/Filter -->
    <div class="flex gap-2">
        <select x-model="filters.level_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">All Levels</option>
            @foreach(App\Models\Level::all() as $level)
                <option value="{{ $level->level_id }}">{{ $level->level_name }}</option>
            @endforeach
        </select>
        
        <input type="text" 
               x-model="filters.search" 
               placeholder="Search videos..."
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
    </div>
    
    <!-- Loading State -->
    <div x-show="loading" class="text-center py-4 text-gray-500">
        <svg class="animate-spin h-5 w-5 mx-auto text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm">Loading videos...</span>
    </div>
    
    <!-- Available Videos -->
    <div x-show="!loading" class="border border-gray-200 rounded-lg max-h-60 overflow-y-auto p-2">
        <template x-for="video in filteredVideos" :key="video.video_id">
            <div class="flex items-center p-2 hover:bg-gray-50 rounded"
                 :class="{ 'bg-blue-50': selectedVideo == video.video_id }">
                <input type="radio" 
                       name="video_selection"
                       :value="video.video_id" 
                       x-model="selectedVideo"
                       :id="'video_' + video.video_id"
                       class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                
                <label :for="'video_' + video.video_id" class="ml-3 flex-1 flex items-center cursor-pointer">
                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <div>
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
        <div class="flex items-center p-2 hover:bg-gray-50 rounded border-t border-gray-100 mt-2"
             :class="{ 'bg-blue-50': selectedVideo === null }">
            <input type="radio" 
                   name="video_selection"
                   :value="null" 
                   x-model="selectedVideo"
                   id="video_none"
                   class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
            <label for="video_none" class="ml-3 flex-1 flex items-center cursor-pointer">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
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
    
    <!-- Hidden input for form submission (single video ID) -->
    <input type="hidden" name="video_id" :value="selectedVideo">
    
    <!-- Debug Info (remove after fixing) -->
    <div class="text-xs text-gray-400 p-2 border border-gray-200 rounded">
        <div>Selected Video ID: <span x-text="selectedVideo"></span></div>
        <div>Videos Loaded: <span x-text="videos.length"></span></div>
        <div>Loading: <span x-text="loading"></span></div>
    </div>
    
    <!-- Selected Video Summary -->
    <div x-show="selectedVideo" class="bg-blue-50 p-3 rounded-lg">
        <p class="text-sm font-medium text-blue-800 mb-2">Selected Video:</p>
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <div>
                    <span class="text-sm text-blue-800 font-medium" x-text="getSelectedVideoTitle()"></span>
                    <p class="text-xs text-blue-600" x-text="getSelectedVideoDetails()"></p>
                </div>
            </div>
            <button @click="selectedVideo = null" type="button" class="text-blue-600 hover:text-blue-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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
        loading: true,
        filters: {
            search: '',
            level_id: '',
            skill_id: null
        },
        
        async init(selectedIds, skillId) {
            console.log('=== VIDEO SELECTOR INIT ===');
            console.log('Selected IDs (raw):', selectedIds);
            console.log('Skill ID:', skillId);
            
            if (selectedIds) {
                if (Array.isArray(selectedIds) && selectedIds.length > 0) {
                    this.selectedVideo = selectedIds[0];
                } else if (typeof selectedIds === 'number' || typeof selectedIds === 'string') {
                    this.selectedVideo = selectedIds;
                }
            }
            
            console.log('Selected video (parsed):', this.selectedVideo, 'Type:', typeof this.selectedVideo);
            
            this.filters.skill_id = skillId;
            
       
            await this.fetchLevels();
            
        
            await this.fetchVideos();
            
            this.loading = false;
            console.log('Initialization complete');
            console.log('Final selected video:', this.selectedVideo);
            console.log('Videos loaded:', this.videos);
            console.log('========================');
        },
        
        async fetchLevels() {
            try {
                const response = await fetch('/admin/api/levels');
                const data = await response.json();
                this.levels = data;
                console.log('Levels loaded:', this.levels.length);
            } catch (error) {
                console.error('Error loading levels:', error);
                this.levels = [];
            }
        },
        
        async fetchVideos() {
            try {
                let url = '/admin/api/videos';
                if (this.filters.skill_id) {
                    url += `?skill_id=${this.filters.skill_id}`;
                }
                console.log('Fetching videos from:', url);
                
                const response = await fetch(url);
                const data = await response.json();
                console.log('Raw video data:', data);
                
                this.videos = data.map(video => {
             
                    return {
                        ...video,
                        video_id: Number(video.video_id), 
                        duration_formatted: this.formatDuration(video.duration)
                    };
                });
                
                console.log('Processed videos:', this.videos);
                
                // Check if selected video exists in loaded videos
                if (this.selectedVideo) {
                    const foundVideo = this.videos.find(v => v.video_id === Number(this.selectedVideo));
                    console.log('Selected video found in loaded videos:', foundVideo ? 'YES' : 'NO');
                    if (foundVideo) {
                        console.log('Found video title:', foundVideo.title);
                    } else {
                        console.log('Video IDs in loaded videos:', this.videos.map(v => v.video_id));
                        console.log('Looking for ID:', Number(this.selectedVideo));
                    }
                }
            } catch (error) {
                console.error('Error loading videos:', error);
                this.videos = [];
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
                if (duration.includes(':')) {
                    return duration;
                }
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
                if (this.filters.search && !video.title.toLowerCase().includes(this.filters.search.toLowerCase())) {
                    return false;
                }
                
                // Level filter
                if (this.filters.level_id) {
                    const videoLevelId = this.getVideoLevelId(video);
                    if (videoLevelId != this.filters.level_id) {
                        return false;
                    }
                }
                
                return true;
            });
        },
        
        getVideoLevelId(video) {
            if (video.description) {
                const match = video.description.match(/<!-- LEVEL:(\d+) -->/);
                if (match) {
                    return parseInt(match[1]);
                }
            }
            return null;
        },
        
        getLevelName(video) {
            const levelId = this.getVideoLevelId(video);
            if (levelId && this.levels.length > 0) {
                const level = this.levels.find(l => l.level_id === levelId);
                return level ? level.level_name : `Level ${levelId}`;
            }
            return 'No Level';
        },
        
        getSelectedVideoTitle() {
            if (!this.selectedVideo) return 'No Video';
            
            if (this.loading) return 'Loading...';
            
            // Convert both to numbers for comparison
            const selectedId = Number(this.selectedVideo);
            const video = this.videos.find(v => Number(v.video_id) === selectedId);
            
            if (video) {
                return video.title;
            }
            
            console.log('Video not found. Selected ID:', selectedId, 'Available IDs:', this.videos.map(v => Number(v.video_id)));
            return 'Video not found';
        },
        
        getSelectedVideoDetails() {
            if (!this.selectedVideo) return '';
            
            if (this.loading) return 'Loading details...';
            
            const selectedId = Number(this.selectedVideo);
            const video = this.videos.find(v => Number(v.video_id) === selectedId);
            
            if (video) {
                const levelName = this.getLevelName(video);
                const duration = video.duration_formatted || video.duration || '';
                return `${levelName} • ${duration}`;
            }
            
            return '';
        }
    }
}
</script>