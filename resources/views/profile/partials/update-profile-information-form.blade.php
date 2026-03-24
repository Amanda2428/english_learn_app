<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="profile_image" :value="__('Profile Picture')" />
            
            <div class="mt-2 flex items-center space-x-6">
                <div class="relative">
                    <div id="avatar-container" class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-200 flex items-center justify-center">
                        @if($user->profile)
                            <img src="{{ Storage::url($user->profile) }}" 
                                 alt="Profile Picture" 
                                 class="w-full h-full object-cover">
                        @else
                            <div id="initials-avatar" class="w-full h-full flex items-center justify-center bg-gradient-to-r from-blue-500 to-blue-600">
                                <span class="text-white text-2xl font-medium">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <button type="button" 
                            onclick="document.getElementById('profile_image_input').click()"
                            class="absolute bottom-0 right-0 bg-white rounded-full p-1.5 shadow-lg border border-gray-300 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex flex-col space-y-2">
                    <p class="text-sm text-gray-600">
                        Upload a profile picture (Max 2MB)
                    </p>
                    <button type="button" 
                            onclick="document.getElementById('profile_image_input').click()"
                            class="text-sm font-semibold text-blue-600 hover:text-blue-800 text-left">
                        Choose photo
                    </button>
                </div>
            </div>

            <input type="file" 
                   id="profile_image_input" 
                   name="profile_image" 
                   accept="image/*"
                   class="hidden"
                   onchange="previewImage(this)">

            <x-input-error class="mt-2" :messages="$errors->get('profile_image')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="bio" :value="__('Bio / About Me')" />
            <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>

@push('scripts')
<script>
    /**
     * This function handles the "Instant Preview" in the circle
     */
    function previewImage(input) {
        const container = document.getElementById('avatar-container');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Check file size (2MB limit)
            if (file.size > 2 * 1024 * 1024) {
                alert('Image is too large (max 2MB)');
                input.value = '';
                return;
            }
            
            // Check file type
            if (!file.type.match('image/jpeg') && !file.type.match('image/png') && !file.type.match('image/gif')) {
                alert('Please upload a valid image file (JPEG, PNG, or GIF)');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Clear the container
                container.innerHTML = '';
                
                // Create the new image element
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-full h-full object-cover';
                
                // Append it to the circle
                container.appendChild(img);
            }
            
            reader.readAsDataURL(file);
        } else {
            // If no file selected, reset to original
            const originalImage = '{{ $user->profile ? Storage::url($user->profile) : '' }}';
            const userName = '{{ $user->name }}';
            
            container.innerHTML = '';
            
            if (originalImage) {
                const img = document.createElement('img');
                img.src = originalImage;
                img.className = 'w-full h-full object-cover';
                container.appendChild(img);
            } else {
                const div = document.createElement('div');
                div.className = 'w-full h-full flex items-center justify-center bg-gradient-to-r from-blue-500 to-blue-600';
                div.innerHTML = `<span class="text-white text-2xl font-medium">${userName.charAt(0).toUpperCase()}</span>`;
                container.appendChild(div);
            }
        }
    }
</script>
@endpush