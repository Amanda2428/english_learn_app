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
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-md border-b border-gray-200 fixed w-full top-0 z-[1000] shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                <!-- LEFT -->
                <div class="flex items-center space-x-3">

                    <!-- Mobile Menu -->
                    <button id="mobile-menu-button" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-bars text-lg text-gray-700"></i>
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

                        <a href="{{ route('user.levels.index') }}"
                            class="nav-link {{ request()->routeIs('user.levels.*') ? 'active' : '' }}">
                            Levels
                        </a>

                        <a href="{{ route('user.skills.index') }}"
                            class="nav-link {{ request()->routeIs('user.skills.*') ? 'active' : '' }}">
                            Skills
                        </a>

                        @auth
                            @if (auth()->user()->isUser())
                                <a href="{{ route('user.dashboard') }}"
                                    class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                                    Progress
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

            <!-- RIGHT SIDE -->
<div class="flex items-center space-x-4">

    @auth
        @if(auth()->user()->isUser())
            <!-- Chatbot Toggle -->
            <button id="chatbot-toggle"
                class="relative p-2 rounded-lg hover:bg-gray-100 transition flex items-center justify-center text-blue-600 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <i class="fas fa-comment-dots text-lg"></i>
                <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
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

                <!-- Avatar -->
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>

                <!-- Name -->
                <span class="hidden md:block text-sm font-medium text-gray-800">
                    {{ auth()->user()->name }}
                </span>

                <i class="fas fa-chevron-down text-gray-500 text-xs transition-transform" :class="{'rotate-180': open}"></i>
            </button>

            <!-- DROPDOWN MENU -->
            <div x-show="open" x-cloak @click.away="open = false" x-transition
                class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border divide-y divide-gray-100 z-[3000]">

                <!-- User Info -->
                <div class="px-4 py-3">
                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                </div>

                <!-- Links -->
                <div class="py-2 flex flex-col">
                    <a href="{{ route('user.dashboard') }}"
                       class="px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 rounded transition">
                        My Progress
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       class="px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 rounded transition">
                        Profile Settings
                    </a>
                </div>

                <!-- Logout -->
                <div class="py-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <!-- GUEST -->
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
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-4 py-4 space-y-2">
                <a href="{{ route('welcome') }}" class="mobile-link">Home</a>
                <a href="{{ route('user.levels.index') }}" class="mobile-link">Levels</a>
                <a href="{{ route('user.skills.index') }}" class="mobile-link">Skills</a>

                @auth
                    <a href="{{ route('user.dashboard') }}" class="mobile-link">My Progress</a>
                @endauth
            </div>
        </div>
    </nav>
    @auth
        @if (auth()->user()->isUser())
            <!-- Chatbot Widget (only for logged-in users) - Mobile Responsive -->
            <div id="chatbot-widget" class="fixed bottom-4 right-4 left-4 sm:left-auto sm:right-6 sm:w-96 z-50 hidden">
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
                    <!-- Chat Header -->
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

                    <!-- Chat Messages -->
                    <div id="chat-messages" class="h-80 sm:h-96 overflow-y-auto p-4 bg-gray-50 space-y-4">
                        <!-- Initial Bot Message -->
                        <div class="flex justify-start">
                            <div class="flex items-start space-x-2 max-w-[85%] sm:max-w-xs">
                                <div
                                    class="w-7 h-7 sm:w-8 sm:h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-robot text-white text-xs sm:text-sm"></i>
                                </div>
                                <div class="bg-white rounded-2xl rounded-tl-none p-2 sm:p-3 shadow-sm">
                                    <p class="text-gray-800 text-xs sm:text-sm">👋 Hello {{ auth()->user()->name }}! I'm
                                        your personal English tutor. I can help you find lessons, practice skills, or track
                                        your progress. What would you like to learn today?</p>
                                    <span class="text-xs text-gray-400 mt-1 block">Just now</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions (scrollable on mobile) -->
                    <div id="quick-actions" class="px-4 py-2 border-t border-gray-100 bg-white overflow-x-auto">
                        <div class="flex flex-nowrap sm:flex-wrap gap-2">
                            <button onclick="sendQuickMessage('Show me elementary level')"
                                class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs whitespace-nowrap hover:bg-green-100 transition">
                                📚 Elementary
                            </button>
                            <button onclick="sendQuickMessage('I want to practice speaking')"
                                class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs whitespace-nowrap hover:bg-blue-100 transition">
                                🗣️ Speaking
                            </button>
                            <button onclick="sendQuickMessage('Show my progress')"
                                class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-xs whitespace-nowrap hover:bg-purple-100 transition">
                                📊 My Progress
                            </button>
                            <button onclick="sendQuickMessage('Recommend videos')"
                                class="px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-xs whitespace-nowrap hover:bg-orange-100 transition">
                                🎥 Recommendations
                            </button>
                        </div>
                    </div>

                    <!-- Chat Input -->
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

    <!-- Footer - Mobile Responsive -->
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
                        <li><a href="#"
                                class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Home</a>
                        </li>
                        <li><a href="{{ route('user.levels.index') }}"
                                class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Learning
                                Levels</a></li>
                        <li><a href="{{ route('user.skills.index') }}"
                                class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Skills</a>
                        </li>
                    </ul>
                </div>
                <div class="text-center sm:text-left">
                    <h4 class="text-base sm:text-lg font-semibold mb-4">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="#"
                                class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Help
                                Center</a></li>
                        <li><a href="#"
                                class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">Contact
                                Us</a></li>
                        <li><a href="#"
                                class="text-sm sm:text-base text-gray-400 hover:text-white transition-colors">FAQ</a>
                        </li>
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

    @auth
        @if (auth()->user()->isUser())
            <!-- Chatbot Script (only for logged-in users) -->
            <script>
                // Wait for DOM to be fully loaded
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize chatbot elements
                    const chatbotToggle = document.getElementById('chatbot-toggle');
                    const chatbotWidget = document.getElementById('chatbot-widget');
                    const chatbotClose = document.getElementById('chatbot-close');
                    const chatInput = document.getElementById('chat-input');
                    const mobileMenuButton = document.getElementById('mobile-menu-button');

                    // Toggle chatbot with error handling
                    if (chatbotToggle) {
                        chatbotToggle.addEventListener('click', function(e) {
                            e.preventDefault();
                            if (chatbotWidget) {
                                chatbotWidget.classList.toggle('hidden');
                            }
                        });
                    }

                    // Close chatbot
                    if (chatbotClose) {
                        chatbotClose.addEventListener('click', function(e) {
                            e.preventDefault();
                            if (chatbotWidget) {
                                chatbotWidget.classList.add('hidden');
                            }
                        });
                    }

                    // Handle Enter key
                    if (chatInput) {
                        chatInput.addEventListener('keypress', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                sendMessage();
                            }
                        });
                    }

                    // Mobile menu toggle
                    if (mobileMenuButton) {
                        mobileMenuButton.addEventListener('click', function(e) {
                            e.preventDefault();
                            const menu = document.getElementById('mobile-menu');
                            if (menu) {
                                menu.classList.toggle('hidden');
                            }
                        });
                    }
                });

                // Chatbot functions with error handling
                function sendMessage() {
                    try {
                        const input = document.getElementById('chat-input');
                        if (!input) return;

                        const message = input.value.trim();
                        if (message) {
                            addUserMessage(message);

                            // Simulate bot response (since route might not exist yet)
                            setTimeout(() => {
                                processBotResponse(message);
                            }, 1000);

                            input.value = '';
                        }
                    } catch (error) {
                        console.log('Error sending message:', error);
                    }
                }

                function sendQuickMessage(message) {
                    try {
                        if (!message) return;
                        addUserMessage(message);

                        // Simulate bot response
                        setTimeout(() => {
                            processBotResponse(message);
                        }, 1000);
                    } catch (error) {
                        console.log('Error sending quick message:', error);
                    }
                }

                function processBotResponse(message) {
                    try {
                        const lowerMessage = message.toLowerCase();
                        let response = '';
                        let links = [];

                        // Basic response logic
                        if (lowerMessage.includes('elementary') || lowerMessage.includes('beginner')) {
                            response =
                                '📚 **Elementary Level (A1-A2)**\n\nAt this level, you will learn:\n• Basic greetings and introductions\n• Simple present tense\n• Everyday vocabulary\n• Basic conversation skills';
                            links = [{
                                    title: 'Basic Greetings Lesson',
                                    url: '#',
                                    icon: 'play'
                                },
                                {
                                    title: 'Simple Present Tense',
                                    url: '#',
                                    icon: 'play'
                                }
                            ];
                        } else if (lowerMessage.includes('intermediate')) {
                            response =
                                '📗 **Intermediate Level (B1-B2)**\n\nAt this level, you will master:\n• Past tenses and narratives\n• Future plans and predictions\n• Business English basics';
                            links = [{
                                    title: 'Past Tense Stories',
                                    url: '#',
                                    icon: 'play'
                                },
                                {
                                    title: 'Business English',
                                    url: '#',
                                    icon: 'play'
                                }
                            ];
                        } else if (lowerMessage.includes('advanced')) {
                            response =
                                '📘 **Advanced Level (C1-C2)**\n\nAt this level, you will perfect:\n• Advanced grammar\n• Academic writing\n• Professional presentations';
                            links = [{
                                    title: 'Advanced Grammar',
                                    url: '#',
                                    icon: 'play'
                                },
                                {
                                    title: 'Academic Writing',
                                    url: '#',
                                    icon: 'play'
                                }
                            ];
                        } else if (lowerMessage.includes('speaking')) {
                            response =
                                '🗣️ **Speaking Practice**\n\nImprove your speaking skills with:\n• Pronunciation exercises\n• Conversation practice\n• Public speaking tips';
                            links = [{
                                    title: 'Pronunciation Practice',
                                    url: '#',
                                    icon: 'microphone'
                                },
                                {
                                    title: 'Daily Conversations',
                                    url: '#',
                                    icon: 'comments'
                                }
                            ];
                        } else if (lowerMessage.includes('listening')) {
                            response =
                                '👂 **Listening Comprehension**\n\nEnhance your listening with:\n• Podcast episodes\n• News articles\n• Movie dialogues';
                            links = [{
                                    title: 'English Podcasts',
                                    url: '#',
                                    icon: 'headphones'
                                },
                                {
                                    title: 'News in English',
                                    url: '#',
                                    icon: 'newspaper'
                                }
                            ];
                        } else if (lowerMessage.includes('progress')) {
                            response =
                                '📊 **Your Progress**\n\nYou can view your detailed progress in the My Progress section! Click on "My Progress" in the navigation menu to see your stats.';
                        } else if (lowerMessage.includes('video') || lowerMessage.includes('recommend')) {
                            response =
                                '🎥 **Video Recommendations**\n\nCheck out our featured videos section below for the latest learning resources!';
                        } else {
                            response =
                                '👋 I can help you with English learning! Try asking about:\n\n📚 **Levels:** elementary, intermediate, advanced\n🗣️ **Skills:** speaking, listening, writing, reading\n\nWhat would you like to practice?';
                        }

                        addBotMessage(response, links);
                    } catch (error) {
                        console.log('Error processing response:', error);
                    }
                }

                function addUserMessage(message) {
                    try {
                        const messagesContainer = document.getElementById('chat-messages');
                        if (!messagesContainer) return;

                        const messageHtml = `
                        <div class="flex justify-end">
                            <div class="flex items-start space-x-2 max-w-[85%] sm:max-w-xs">
                                <div class="bg-blue-600 rounded-2xl rounded-tr-none p-2 sm:p-3 shadow-sm">
                                    <p class="text-white text-xs sm:text-sm">${escapeHtml(message)}</p>
                                    <span class="text-xs text-blue-200 mt-1 block">Just now</span>
                                </div>
                                <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-user text-gray-600 text-xs sm:text-sm"></i>
                                </div>
                            </div>
                        </div>
                    `;

                        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    } catch (error) {
                        console.log('Error adding user message:', error);
                    }
                }

                function addBotMessage(text, links = []) {
                    try {
                        const messagesContainer = document.getElementById('chat-messages');
                        if (!messagesContainer) return;

                        let linksHtml = '';
                        if (links && links.length > 0) {
                            linksHtml = '<div class="mt-2 sm:mt-3 space-y-1 sm:space-y-2">';
                            links.forEach(link => {
                                linksHtml += `
                                <a href="${link.url}" class="block px-2 sm:px-3 py-1.5 sm:py-2 bg-blue-50 text-blue-600 rounded-lg text-xs sm:text-sm hover:bg-blue-100 transition">
                                    <i class="fas fa-${link.icon || 'play'} mr-2 text-blue-600"></i>
                                    ${escapeHtml(link.title)}
                                </a>
                            `;
                            });
                            linksHtml += '</div>';
                        }

                        const messageHtml = `
                        <div class="flex justify-start">
                            <div class="flex items-start space-x-2 max-w-[85%] sm:max-w-xs">
                                <div class="w-6 h-6 sm:w-8 sm:h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-robot text-white text-xs sm:text-sm"></i>
                                </div>
                                <div class="bg-white rounded-2xl rounded-tl-none p-2 sm:p-3 shadow-sm">
                                    <div class="text-gray-800 text-xs sm:text-sm whitespace-pre-line">${escapeHtml(text)}</div>
                                    ${linksHtml}
                                    <span class="text-xs text-gray-400 mt-1 sm:mt-2 block">Just now</span>
                                </div>
                            </div>
                        </div>
                    `;

                        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    } catch (error) {
                        console.log('Error adding bot message:', error);
                    }
                }

                // Helper function to escape HTML and prevent XSS
                function escapeHtml(unsafe) {
                    if (!unsafe) return '';
                    return unsafe
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;")
                        .replace(/\n/g, '<br>');
                }

                // Function for welcome page preview
                function sendQuickMessageFromPreview(message) {
                    try {
                        const chatbotToggle = document.getElementById('chatbot-toggle');
                        if (chatbotToggle) {
                            chatbotToggle.click();
                            setTimeout(() => {
                                const input = document.getElementById('chat-input');
                                if (input) {
                                    input.value = message;
                                    sendMessage();
                                }
                            }, 500);
                        }
                    } catch (error) {
                        console.log('Error from preview:', error);
                    }
                }

                // Make functions globally available
                window.sendMessage = sendMessage;
                window.sendQuickMessage = sendQuickMessage;
                window.sendQuickMessageFromPreview = sendQuickMessageFromPreview;
            </script>
        @endif
    @endauth
    @push('styles')
        <style>
            /* Fix for Chrome extension conflicts */
            [x-cloak] {
                display: none !important;
            }

            /* Ensure chatbot appears above everything */
            #chatbot-widget {
                z-index: 9999;
            }

            /* Mobile menu z-index */
            #mobile-menu {
                z-index: 50;
            }

            /* Ensure dropdown menus appear above content */
            .z-50 {
                z-index: 50;
            }
        </style>
    @endpush
    @push('scripts')
        <script>
            function sendQuickMessageFromPreview(message) {
                try {
                    const chatbotToggle = document.getElementById('chatbot-toggle');
                    if (chatbotToggle) {
                        chatbotToggle.click();
                        setTimeout(() => {
                            const input = document.getElementById('chat-input');
                            if (input) {
                                input.value = message;
                                if (typeof window.sendMessage === 'function') {
                                    window.sendMessage();
                                }
                            }
                        }, 500);
                    } else {
                        // If chatbot isn't available, redirect to login
                        window.location.href = '{{ route('login') }}';
                    }
                } catch (error) {
                    console.log('Error:', error);
                    window.location.href = '{{ route('login') }}';
                }
            }
        </script>
    @endpush

    <!-- Scripts -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>

</html>
