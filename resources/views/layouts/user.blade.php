<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'FluentEdge'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Styles -->
    @vite(['resources/css/app.css'])

    @livewireStyles
    @stack('styles')

    <style>
        /* Mobile menu styles */
        .mobile-link {
            display: block;
            padding: 0.75rem 1rem;
            color: #374151;
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }

        .mobile-link:hover {
            background-color: #f3f4f6;
            color: #2563eb;
        }

        .mobile-link.active {
            background-color: #eff6ff;
            color: #2563eb;
        }

        .nav-link {
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: #2563eb;
        }

        .nav-link.active {
            color: #2563eb;
            font-weight: 600;
        }

        /* Ensure mobile menu is above other content */
        #mobile-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            max-height: calc(100vh - 64px);
            overflow-y: auto;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        @media (min-width: 768px) {
            #mobile-menu {
                display: none !important;
            }
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-md border-b border-gray-200 fixed w-full top-0 z-[1000] shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                <!-- LEFT -->
                <div class="flex items-center space-x-3">

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button"
                        class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <i id="menu-icon" class="fas fa-bars text-lg text-gray-700"></i>
                    </button>

                    <!-- Logo -->
                    <a href="{{ route('welcome') }}" class="flex items-center space-x-1">
                        <span class="text-xl font-bold text-blue-600">Fluent</span>
                        <span class="text-xl font-bold text-gray-800">Edge</span>
                    </a>

                    <!-- Desktop Links -->
                    <div class="hidden md:flex ml-8 space-x-6">
                        <a href="{{ route('welcome') }}"
                            class="nav-link {{ request()->routeIs('welcome') ? 'active' : '' }}">
                            Home
                        </a>

                        @auth
                            <a href="{{ route('user.levels.index') }}"
                                class="nav-link {{ request()->routeIs('user.levels.*') ? 'active' : '' }}">
                                Levels
                            </a>

                            <a href="{{ route('user.skills.index') }}"
                                class="nav-link {{ request()->routeIs('user.skills.*') ? 'active' : '' }}">
                                Skills
                            </a>

                            @if (auth()->user()->isUser())
                                <a href="{{ route('user.progress.index') }}"
                                    class="nav-link {{ request()->routeIs('user.progress.index') ? 'active' : '' }}">
                                    Progress
                                </a>
                            @endif
                        @endauth

                        <a href="{{ route('help.center') }}"
                            class="nav-link {{ request()->routeIs('help.center') ? 'active' : '' }}">
                            Help Center
                        </a>
                    </div>
                </div>

                <!-- RIGHT SIDE -->
                <div class="flex items-center space-x-4">

                    @auth
                        @if (auth()->user()->isUser())
                            <!-- Chatbot Toggle -->
                            <button id="chatbot-toggle"
                                class="relative p-2 rounded-lg hover:bg-gray-100 transition flex items-center justify-center text-blue-600 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <i class="fas fa-comment-dots text-lg"></i>
                                <span
                                    class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                            </button>

                            <!-- Points -->
                            <div class="hidden sm:flex items-center bg-blue-50 px-3 py-1 rounded-full shadow-sm">
                                <i class="fas fa-star text-yellow-400 mr-2"></i>
                                <span class="text-sm font-semibold text-blue-700">
                                    {{ auth()->user()->total_points ?? 0 }}
                                </span>
                            </div>
                        @endif

                        <!-- USER DROPDOWN -->
                        <div class="relative z-[2000]" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition focus:outline-none focus:ring-2 focus:ring-blue-400">

                                <!-- Avatar with Profile Picture -->
                                @if (auth()->user()->profile && Storage::disk('public')->exists(auth()->user()->profile))
                                    <img src="{{ Storage::url(auth()->user()->profile) }}" alt="Profile Picture"
                                        class="w-9 h-9 rounded-full object-cover shadow">
                                @else
                                    <div
                                        class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif

                                <!-- Name -->
                                <span class="hidden md:block text-sm font-medium text-gray-800">
                                    {{ auth()->user()->name }}
                                </span>

                                <i class="fas fa-chevron-down text-gray-500 text-xs transition-transform"
                                    :class="{ 'rotate-180': open }"></i>
                            </button>

                            <!-- DROPDOWN MENU -->
                            <div x-show="open" x-cloak @click.away="open = false" x-transition
                                class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border divide-y divide-gray-100 z-[3000]">

                                <!-- User Info with Profile Picture -->
                                <div class="px-4 py-3">
                                    <div class="flex items-center space-x-3 mb-2">
                                        @if (auth()->user()->profile && Storage::disk('public')->exists(auth()->user()->profile))
                                            <img src="{{ Storage::url(auth()->user()->profile) }}" alt="Profile Picture"
                                                class="w-12 h-12 rounded-full object-cover shadow">
                                        @else
                                            <div
                                                class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow">
                                                {{ substr(auth()->user()->name, 0, 1) }}
                                            </div>
                                        @endif

                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                        </div>
                                    </div>

                                    @php
                                        $currentLevel = null;
                                        if (auth()->user()->level_id) {
                                            $currentLevel = \App\Models\Level::find(auth()->user()->level_id);
                                        }
                                    @endphp

                                    @if ($currentLevel)
                                        <div class="mt-2 pt-2 border-t border-gray-100">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs text-gray-500">Current Level</span>
                                                <span
                                                    class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                                                    {{ $currentLevel->level_name }}
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-2 pt-2 border-t border-gray-100">
                                            <a href="{{ route('user.levels.index') }}"
                                                class="text-xs text-blue-600 hover:text-blue-700 flex items-center justify-between">
                                                <span class="text-gray-500">Current Level</span>
                                                <span class="text-xs font-medium">Select Level →</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <div class="py-2 flex flex-col">
                                    <a href="{{ route('user.progress.index') }}"
                                        class="px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 rounded transition">
                                        <i class="fas fa-chart-line mr-2 text-gray-400"></i> My Progress
                                    </a>
                                    <a href="{{ route('profile.edit') }}"
                                        class="px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 rounded transition">
                                        <i class="fas fa-user-edit mr-2 text-gray-400"></i> Profile Settings
                                    </a>
                                    @if ($currentLevel)
                                        <a href="{{ route('user.levels.show', $currentLevel->level_id) }}"
                                            class="px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 rounded transition">
                                            <i class="fas fa-layer-group mr-2 text-gray-400"></i> Current Level:
                                            {{ $currentLevel->level_name }}
                                        </a>
                                    @endif
                                </div>

                                <div class="py-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded transition">
                                            <i class="fas fa-sign-out-alt mr-2 text-red-400"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition">
                            Login
                        </a>

                        <a href="{{ route('register') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow transition">
                            Sign Up
                        </a>
                    @endauth

                </div>
            </div>
        </div>

        <!-- MOBILE MENU -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t shadow-lg">
            <div class="px-4 py-4 space-y-2">
                <a href="{{ route('welcome') }}"
                    class="mobile-link {{ request()->routeIs('welcome') ? 'active' : '' }}">
                    <i class="fas fa-home mr-3 text-gray-400"></i> Home
                </a>

                @auth
                    <a href="{{ route('user.levels.index') }}"
                        class="mobile-link {{ request()->routeIs('user.levels.*') ? 'active' : '' }}">
                        <i class="fas fa-layer-group mr-3 text-gray-400"></i> Levels
                    </a>
                    <a href="{{ route('user.skills.index') }}"
                        class="mobile-link {{ request()->routeIs('user.skills.*') ? 'active' : '' }}">
                        <i class="fas fa-book-open mr-3 text-gray-400"></i> Skills
                    </a>

                    @if (auth()->user()->isUser())
                        <a href="{{ route('user.progress.index') }}"
                            class="mobile-link {{ request()->routeIs('user.progress.index') ? 'active' : '' }}">
                            <i class="fas fa-chart-line mr-3 text-gray-400"></i> My Progress
                        </a>
                    @endif
                @endauth

                <a href="{{ route('help.center') }}"
                    class="mobile-link {{ request()->routeIs('help.center') ? 'active' : '' }}">
                    <i class="fas fa-question-circle mr-3 text-gray-400"></i> Help Center
                </a>

                @auth
                    @if (auth()->user()->isUser())
                        <div class="border-t border-gray-100 my-2 pt-2">
                            <a href="{{ route('profile.edit') }}" class="mobile-link">
                                <i class="fas fa-user-edit mr-3 text-gray-400"></i> Profile Settings
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                @csrf
                                <button type="submit" class="mobile-link w-full text-left">
                                    <i class="fas fa-sign-out-alt mr-3 text-gray-400"></i> Logout
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="border-t border-gray-100 my-2 pt-2">
                        <a href="{{ route('login') }}" class="mobile-link">
                            <i class="fas fa-sign-in-alt mr-3 text-gray-400"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="mobile-link">
                            <i class="fas fa-user-plus mr-3 text-gray-400"></i> Sign Up
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    @auth
        @if (auth()->user()->isUser())
            <!-- Chatbot Widget -->
            <div id="chatbot-widget" class="fixed bottom-4 right-4 left-4 sm:left-auto sm:right-6 sm:w-96 z-50 hidden">
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-robot text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold text-sm sm:text-base">English Tutor Bot</h3>
                                <p class="text-blue-100 text-xs">Online • Ready to help</p>
                            </div>
                        </div>
                        <button id="chatbot-close" class="text-white/80 hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div id="chat-messages" class="h-80 sm:h-96 overflow-y-auto p-4 bg-gray-50 space-y-4">
                        <div class="flex justify-start">
                            <div class="flex items-start space-x-2 max-w-[85%] sm:max-w-xs">
                                <div
                                    class="w-7 h-7 sm:w-8 sm:h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-robot text-white text-xs sm:text-sm"></i>
                                </div>
                                <div class="bg-white rounded-2xl rounded-tl-none p-2 sm:p-3 shadow-sm">
                                    <p class="text-gray-800 text-xs sm:text-sm whitespace-pre-line">
                                        {!! $botIntro ?? '👋 Hello ' . auth()->user()->name . "! I'm your tutor. How can I help?" !!}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="quick-actions" class="px-4 py-2 border-t border-gray-100 bg-white overflow-x-auto">
                        <div class="flex flex-nowrap sm:flex-wrap gap-2">
                            @php
                                $quickLevels = \App\Models\Level::take(2)->get();
                                $quickSkills = \App\Models\Skill::where('status', true)->take(2)->get();
                            @endphp

                            @foreach ($quickLevels as $l)
                                <button onclick="sendQuickMessage('{{ $l->level_name }}')"
                                    class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs whitespace-nowrap hover:bg-green-100 transition">
                                    📚 {{ $l->level_name }}
                                </button>
                            @endforeach

                            @foreach ($quickSkills as $s)
                                <button onclick="sendQuickMessage('{{ $s->skill_name }}')"
                                    class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs whitespace-nowrap hover:bg-blue-100 transition">
                                    🗣️ {{ $s->skill_name }}
                                </button>
                            @endforeach

                            <button onclick="sendQuickMessage('more')"
                                class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-xs whitespace-nowrap">🔄
                                More</button>
                        </div>
                    </div>

                    <div class="p-3 sm:p-4 border-t border-gray-100 bg-white">
                        <div class="flex items-center space-x-2">
                            <input type="text" id="chat-input" placeholder="Type your message..."
                                class="flex-1 px-3 sm:px-4 py-1.5 sm:py-2 border border-gray-200 rounded-full focus:outline-none focus:border-blue-500 text-xs sm:text-sm">
                            <button onclick="sendMessage()"
                                class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-600 rounded-full text-white hover:bg-blue-700 transition flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-paper-plane text-xs sm:text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    <!-- Page Header -->
    @hasSection('header')
        <header class="bg-white border-b border-gray-200 pt-16 sm:pt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">@yield('title')</h1>
                @hasSection('subtitle')
                    <p class="text-sm sm:text-base text-gray-600 mt-1">@yield('subtitle')</p>
                @endif
            </div>
        </header>
    @endif

    <!-- Header Actions -->
    @hasSection('header-actions')
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
                <div class="flex justify-end">
                    @yield('header-actions')
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="pt-16 sm:pt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-12 sm:mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                <div class="text-center sm:text-left">
                    <h3 class="text-lg sm:text-xl font-bold mb-4">FluentEdge</h3>
                    <p class="text-sm sm:text-base text-gray-400">Master English skills with personalized learning
                        paths and interactive content.</p>
                </div>
                <div class="text-center sm:text-left">
                    <h4 class="text-base sm:text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('welcome') }}"
                                class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Home</a>
                        </li>
                        @auth
                            <li><a href="{{ route('user.levels.index') }}"
                                    class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Learning
                                    Levels</a></li>
                            <li><a href="{{ route('user.skills.index') }}"
                                    class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Skills</a>
                            </li>
                        @endauth
                        <li><a href="{{ route('help.center') }}"
                                class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Help
                                Center</a></li>
                    </ul>
                </div>
                <div class="text-center sm:text-left">
                    <h4 class="text-base sm:text-lg font-semibold mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('help.center') }}"
                                class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Help
                                Center</a></li>
                        <li><a href="mailto:support@fluentedgetest.com"
                                class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Contact
                                Us</a></li>
                    </ul>
                </div>
                <div class="text-center sm:text-left">
                    <h4 class="text-base sm:text-lg font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="#"
                                class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Privacy
                                Policy</a></li>
                        <li><a href="#"
                                class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Terms of
                                Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-6 sm:mt-8 pt-6 sm:pt-8 text-center text-gray-400">
                <p class="text-xs sm:text-sm">&copy; {{ date('Y') }} FluentEdge. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
    // 1. UTILITY FUNCTIONS
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function formatTime(timestamp) {
        if (!timestamp) return 'Just now';
        return new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // 2. RENDERING FUNCTIONS (UI Only)
    function renderUserMessage(message, time = null) {
        const messagesContainer = document.getElementById('chat-messages');
        if (!messagesContainer) return;
        
        const messageHtml = `
            <div class="flex justify-end mb-3">
                <div class="bg-blue-600 rounded-2xl rounded-tr-none p-3 shadow-sm max-w-[80%]">
                    <p class="text-white text-sm">${escapeHtml(message)}</p>
                    <span class="text-xs text-blue-200 mt-1 block">${formatTime(time)}</span>
                </div>
            </div>`;
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function renderBotMessage(text, linkUrl = null, linkTitle = null, time = null) {
        const messagesContainer = document.getElementById('chat-messages');
        if (!messagesContainer) return;

        let messageHtml = `
            <div class="flex justify-start mb-3">
                <div class="bg-white rounded-2xl rounded-tl-none p-3 shadow-sm max-w-[80%] border border-gray-100">
                    <div class="text-gray-800 text-sm whitespace-pre-line">${text}</div>`;

        if (linkUrl) {
            messageHtml += `
                <a href="${linkUrl}" class="mt-2 inline-block bg-blue-600 text-white text-xs px-3 py-1 rounded-lg hover:bg-blue-700 transition">
                    ${linkTitle ? linkTitle : 'Learn More'}
                </a>`;
        }

        messageHtml += `
                    <span class="text-xs text-gray-400 mt-1 block">${formatTime(time)}</span>
                </div>
            </div>`;

        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // 3. DATABASE INTERACTIONS
    window.loadChatHistory = function() {
        fetch("{{ route('user.chatbot.history') }}")
            .then(res => res.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        renderUserMessage(msg.user_message, msg.created_at);
                        renderBotMessage(msg.bot_response, msg.link_url, msg.link_title, msg.created_at);
                    });
                }
            })
            .catch(err => console.error("Error loading history:", err));
    };

    window.sendMessage = function() {
        const input = document.getElementById('chat-input');
        if (!input) return;
        const message = input.value.trim();
        if (message) {
            renderUserMessage(message); // Show in UI immediately
            input.value = '';

            fetch("{{ route('user.chatbot.send') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                // Response from Controller (which already saved it to DB)
                renderBotMessage(data.bot_response, data.link_url, data.link_title);
            })
            .catch(error => {
                console.error('Error:', error);
                renderBotMessage("I'm sorry, I'm having trouble connecting to my brain right now.");
            });
        }
    };

    window.sendQuickMessage = function(message) {
        if (!message) return;
        renderUserMessage(message);

        fetch("{{ route('user.chatbot.send') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            renderBotMessage(data.bot_response, data.link_url, data.link_title);
        });
    };

    window.sendQuickMessageFromPreview = function(message) {
        const chatbotToggle = document.getElementById('chatbot-toggle');
        const widget = document.getElementById('chatbot-widget');
        
        if (chatbotToggle) {
            if (widget && widget.classList.contains('hidden')) {
                widget.classList.remove('hidden');
            }
            
            setTimeout(() => {
                const input = document.getElementById('chat-input');
                if (input) {
                    input.value = message;
                    window.sendMessage();
                }
            }, 500);
        } else {
            window.location.href = '{{ route("login") }}';
        }
    };

    // 4. INITIALIZATION & EVENT LISTENERS
    document.addEventListener('DOMContentLoaded', function() {
        // Load History on startup
        window.loadChatHistory();

        // Mobile Menu
        const mobileBtn = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');

        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                mobileMenu.classList.toggle('hidden');
                if (menuIcon) {
                    menuIcon.classList.toggle('fa-bars');
                    menuIcon.classList.toggle('fa-times');
                }
            });
        }

        // Chatbot UI Toggle
        const chatBtn = document.getElementById('chatbot-toggle');
        const chatWidget = document.getElementById('chatbot-widget');
        const chatClose = document.getElementById('chatbot-close');
        const chatInput = document.getElementById('chat-input');

        if (chatBtn && chatWidget) {
            chatBtn.addEventListener('click', () => chatWidget.classList.toggle('hidden'));
        }

        if (chatClose && chatWidget) {
            chatClose.addEventListener('click', () => chatWidget.classList.add('hidden'));
        }

        if (chatInput) {
            chatInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    window.sendMessage();
                }
            });
        }
    });
</script>

    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }

            #chatbot-widget {
                z-index: 9999 !important;
            }

            #mobile-menu {
                z-index: 999;
            }

            .hidden {
                display: none !important;
            }
        </style>
    @endpush

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>

</html>
