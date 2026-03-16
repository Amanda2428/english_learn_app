<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'E-Learn') }} Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>


<body class="font-sans antialiased bg-gray-100">

    <!-- Mobile Header -->
    <div
        class="lg:hidden bg-gradient-to-r from-blue-800 to-blue-900 text-white fixed top-0 left-0 right-0 z-50 shadow-lg">
        <div class="flex items-center justify-between p-4">
            <h1 class="text-xl font-bold flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                    </path>
                </svg>
                {{ config('app.name', 'E-Learn') }} Admin
            </h1>
            <button id="mobile-menu-button"
                class="p-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Overlay -->
    <div id="mobile-sidebar-overlay"
        class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden transition-opacity"></div>

    <!-- Sidebar -->
    <div id="sidebar"
        class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-blue-800 to-blue-900 text-white
               transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-50 shadow-xl">

        <div class="p-6 h-full flex flex-col">
            <!-- Logo Area -->
            <div class="mb-8 hidden lg:block">
                <h1 class="text-2xl font-bold flex items-center">
                    <svg class="w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    {{ config('app.name', 'E-Learn') }}
                </h1>
                <p class="text-blue-300 text-sm mt-1">Administration Panel</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-700 shadow-lg' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span class="truncate font-medium">Dashboard</span>
                </a>

                <!-- Content Management Section -->
                <div class="pt-4 mt-2 border-t border-blue-700">
                    <p class="px-4 text-xs font-semibold text-blue-300 uppercase tracking-wider">Content Management</p>
                </div>

                <a href="{{ route('admin.levels.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.levels*') ? 'bg-blue-700 shadow-lg' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    <span class="truncate font-medium">Levels</span>
                </a>

                <a href="{{ route('admin.videos.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.videos*') ? 'bg-blue-700 shadow-lg' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span class="truncate font-medium">Videos</span>
                </a>

                <a href="{{ route('admin.questions.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.questions*') ? 'bg-blue-700 shadow-lg' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    <span class="truncate font-medium">Questions</span>
                </a>

                <a href="{{ route('admin.skills.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.skills*') ? 'bg-blue-700 shadow-lg' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                        </path>
                    </svg>
                    <span class="truncate font-medium">Skills</span>
                </a>

                <!-- Chatbot Management Section -->
                <div class="pt-4 mt-2 border-t border-blue-700">
                    <p class="px-4 text-xs font-semibold text-blue-300 uppercase tracking-wider">Chatbot Management</p>
                </div>

                <a href="{{ route('admin.chatbot.rules.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.chatbot.rules*') ? 'bg-blue-700 shadow-lg' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span class="truncate font-medium">Bot Rules</span>
                </a>

                <a href="{{ route('admin.chatbot.sessions.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.chatbot.sessions*') ? 'bg-blue-700 shadow-lg' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                    <span class="truncate font-medium">Chat Sessions</span>
                </a>

                <!-- User Management Section -->
                <div class="pt-4 mt-2 border-t border-blue-700">
                    <p class="px-4 text-xs font-semibold text-blue-300 uppercase tracking-wider">User Management</p>
                </div>

                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.users*') ? 'bg-blue-700 shadow-lg' : '' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span class="truncate font-medium">Users</span>
                </a>
            </nav>

            <!-- User Profile -->
            <div class="mt-auto pt-4 border-t border-blue-700">
                <div class="flex items-center space-x-3 px-2">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                            <span class="text-white font-bold text-lg">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-blue-300 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                        @csrf
                        <button type="submit"
                            class="text-blue-300 hover:text-white p-1 rounded-lg hover:bg-blue-700 transition-colors"
                            title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-64 min-h-screen flex flex-col">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm sticky top-0 z-30">
            <div class="px-6 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h1>

                <!-- Quick Actions -->
                <div class="flex items-center space-x-3" x-data="{ bellOpen: false, settingsOpen: false }">
                    <!-- Bell -->
                    <div class="relative">
                        <button @click="bellOpen = !bellOpen; settingsOpen = false"
                            class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>

                            @php
                                $headerNotifications = collect();

                                if (isset($newUsersToday) && $newUsersToday > 0) {
                                    $headerNotifications->push([
                                        'title' => 'New Users',
                                        'message' => $newUsersToday . ' new user(s) registered today.',
                                        'link' => route('admin.users.index'),
                                    ]);
                                }

                                if (isset($activeSessionsToday) && $activeSessionsToday > 0) {
                                    $headerNotifications->push([
                                        'title' => 'Chat Sessions',
                                        'message' => $activeSessionsToday . ' active chatbot session(s) today.',
                                        'link' => route('admin.chatbot.sessions.index'),
                                    ]);
                                }

                                if (isset($storagePercentage) && $storagePercentage >= 80) {
                                    $headerNotifications->push([
                                        'title' => 'Storage Warning',
                                        'message' => 'Storage usage is at ' . $storagePercentage . '%.',
                                        'link' => '#',
                                    ]);
                                }
                            @endphp

                            @if ($headerNotifications->count() > 0)
                                <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                            @endif
                        </button>

                        <div x-show="bellOpen" @click.away="bellOpen = false" x-transition style="display: none;"
                            class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">

                            <div class="px-4 py-3 border-b bg-gray-50">
                                <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
                            </div>

                            <div class="max-h-80 overflow-y-auto">
                                @forelse($headerNotifications as $notification)
                                    <a href="{{ $notification['link'] }}"
                                        class="block px-4 py-3 hover:bg-gray-50 border-b last:border-b-0">
                                        <p class="text-sm font-medium text-gray-800">{{ $notification['title'] }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $notification['message'] }}</p>
                                    </a>
                                @empty
                                    <div class="px-4 py-6 text-center text-sm text-gray-500">
                                        No new notifications
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="relative">
                        <button @click="settingsOpen = !settingsOpen; bellOpen = false"
                            class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>

                        <div x-show="settingsOpen" @click.away="settingsOpen = false" x-transition
                            style="display: none;"
                            class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">

                            <div class="px-4 py-3 border-b bg-gray-50">
                                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="py-2">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    Dashboard
                                </a>

                                @if (Route::has('profile.edit'))
                                    <a href="{{ route('profile.edit') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        Edit Profile
                                    </a>
                                @endif

                            

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Breadcrumbs -->
            @hasSection('breadcrumbs')
                <div class="px-6 pb-2 text-sm text-gray-600">


                    @yield('breadcrumbs')
                </div>
            @endif
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6">
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm flex items-center justify-between"
                    role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm flex items-center justify-between"
                    role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-4 px-6 text-center text-gray-600 text-sm">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'E-Learn') }}. All rights reserved.</p>
        </footer>
    </div>

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-sidebar-overlay');
            const menuButton = document.getElementById('mobile-menu-button');

            function openMenu() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeMenu() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            menuButton?.addEventListener('click', openMenu);
            overlay?.addEventListener('click', closeMenu);

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
                    closeMenu();
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                } else if (!sidebar.classList.contains('-translate-x-full') && window.innerWidth < 1024) {
                    overlay.classList.remove('hidden');
                }
            });

            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert && alert.parentElement) {
                        alert.style.transition = 'opacity 0.5s';
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            if (alert && alert.parentElement) {
                                alert.remove();
                            }
                        }, 500);
                    }
                }, 5000);
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
