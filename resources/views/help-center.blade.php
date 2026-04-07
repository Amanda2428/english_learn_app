@extends('layouts.user')

@section('title', 'Help Center')
@section('subtitle', 'Find answers to your questions and learn how to make the most of FluentEdge')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 text-white mb-8 text-center">
        <div class="max-w-2xl mx-auto">
            <i class="fas fa-question-circle text-5xl mb-4"></i>
            <h1 class="text-3xl font-bold mb-3">How can we help you?</h1>
            <p class="text-blue-100 mb-6">Find answers to common questions about FluentEdge</p>
            
            <!-- Search Bar -->
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" 
                       id="searchFaq" 
                       placeholder="Search for questions..." 
                       class="w-full pl-12 pr-4 py-3 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
        <div class="bg-white rounded-xl p-4 text-center border border-gray-100 hover:shadow-md transition cursor-pointer" onclick="scrollToCategory('getting-started')">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-rocket text-blue-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-900 text-sm">Getting Started</h3>
        </div>
        
        <div class="bg-white rounded-xl p-4 text-center border border-gray-100 hover:shadow-md transition cursor-pointer" onclick="scrollToCategory('learning')">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-graduation-cap text-green-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-900 text-sm">Learning Path</h3>
        </div>
        
        <div class="bg-white rounded-xl p-4 text-center border border-gray-100 hover:shadow-md transition cursor-pointer" onclick="scrollToCategory('technical')">
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-cog text-purple-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-900 text-sm">Technical</h3>
        </div>
        
        <div class="bg-white rounded-xl p-4 text-center border border-gray-100 hover:shadow-md transition cursor-pointer" onclick="scrollToCategory('account')">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-user-circle text-yellow-600 text-xl"></i>
            </div>
            <h3 class="font-semibold text-gray-900 text-sm">Account</h3>
        </div>
    </div>

    <!-- FAQ Categories -->
    <div class="space-y-8">
        <!-- Getting Started -->
        <div id="getting-started" class="scroll-mt-24">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-rocket text-blue-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Getting Started</h2>
            </div>
            
            <div class="space-y-3">
                <div class="faq-item bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button class="faq-question w-full text-left px-6 py-4 font-medium text-gray-900 hover:bg-gray-50 transition flex justify-between items-center">
                        <span>What is FluentEdge and how does it work?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4 text-gray-600 border-t border-gray-100 pt-3">
                        <p>FluentEdge is an English learning platform that helps you master English skills through structured levels, video lessons, and practice questions. Here's how it works:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Choose your proficiency level (Elementary, Intermediate, or Advanced)</li>
                            <li>Select skills you want to practice (Speaking, Listening, Reading, Writing)</li>
                            <li>Watch curated YouTube videos to learn concepts</li>
                            <li>Test your knowledge with practice questions</li>
                            <li>Earn points and track your progress on the dashboard</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button class="faq-question w-full text-left px-6 py-4 font-medium text-gray-900 hover:bg-gray-50 transition flex justify-between items-center">
                        <span>How do I choose my English level?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4 text-gray-600 border-t border-gray-100 pt-3">
                        <p>You can choose your level in two ways:</p>
                        <ol class="list-decimal list-inside mt-2 space-y-1">
                            <li><strong>From the Levels page:</strong> Browse through Elementary, Intermediate, and Advanced levels and select the one that matches your current English proficiency.</li>
                            <li><strong>From any skill page:</strong> When practicing a skill, you can select your preferred level from the dropdown menu.</li>
                        </ol>
                        <p class="mt-2">Don't worry - you can change your level at any time as you progress!</p>
                    </div>
                </div>

                <div class="faq-item bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button class="faq-question w-full text-left px-6 py-4 font-medium text-gray-900 hover:bg-gray-50 transition flex justify-between items-center">
                        <span>How do I earn points?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4 text-gray-600 border-t border-gray-100 pt-3">
                        <p>You can earn points by:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li><strong>Answering questions correctly:</strong> Each correct answer gives you points based on difficulty</li>
                            <li><strong>Completing videos:</strong> Watching videos helps reinforce learning</li>
                            <li><strong>Mastering skills:</strong> Completing all questions in a skill gives bonus points</li>
                            <li><strong>Completing levels:</strong> Finishing all skills in a level unlocks achievement points</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Learning Path -->
        <div id="learning" class="scroll-mt-24">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-green-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Learning Path</h2>
            </div>
            
            <div class="space-y-3">
                <div class="faq-item bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button class="faq-question w-full text-left px-6 py-4 font-medium text-gray-900 hover:bg-gray-50 transition flex justify-between items-center">
                        <span>What skills can I learn on FluentEdge?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4 text-gray-600 border-t border-gray-100 pt-3">
                        <p>FluentEdge offers comprehensive English skills training across multiple categories:</p>
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <i class="fas fa-microphone-alt text-blue-600 mb-1"></i>
                                <p class="font-medium text-sm">Speaking</p>
                                <p class="text-xs text-gray-600">Pronunciation, conversation, fluency</p>
                            </div>
                            <div class="bg-green-50 p-3 rounded-lg">
                                <i class="fas fa-headphones text-green-600 mb-1"></i>
                                <p class="font-medium text-sm">Listening</p>
                                <p class="text-xs text-gray-600">Comprehension, accents, audio</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg">
                                <i class="fas fa-book-open text-purple-600 mb-1"></i>
                                <p class="font-medium text-sm">Reading</p>
                                <p class="text-xs text-gray-600">Comprehension, vocabulary, speed</p>
                            </div>
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <i class="fas fa-pencil-alt text-yellow-600 mb-1"></i>
                                <p class="font-medium text-sm">Writing</p>
                                <p class="text-xs text-gray-600">Grammar, essays, emails</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button class="faq-question w-full text-left px-6 py-4 font-medium text-gray-900 hover:bg-gray-50 transition flex justify-between items-center">
                        <span>How do I track my progress?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4 text-gray-600 border-t border-gray-100 pt-3">
                        <p>You can track your progress in several ways:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li><strong>Dashboard:</strong> View overall statistics, points, and completion rates</li>
                            <li><strong>Progress Page:</strong> See detailed charts showing your mastery across skills</li>
                            <li><strong>Skill Pages:</strong> Each skill shows your completion percentage and mastery rate</li>
                            <li><strong>Leaderboard:</strong> Compare your progress with other learners</li>
                        </ul>
                        <p class="mt-2">Click on "My Progress" in the navigation menu to see your full learning journey!</p>
                    </div>
                </div>

                <div class="faq-item bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button class="faq-question w-full text-left px-6 py-4 font-medium text-gray-900 hover:bg-gray-50 transition flex justify-between items-center">
                        <span>What is the mastery rate?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4 text-gray-600 border-t border-gray-100 pt-3">
                        <p>Mastery rate shows how well you've learned a skill. It's calculated based on:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>The number of questions you've answered correctly</li>
                            <li>Your progress through video lessons</li>
                            <li>Consistent practice and review</li>
                        </ul>
                        <p class="mt-2">Aim for 100% mastery to fully complete a skill! The system tracks your progress until you master all concepts.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical -->
        <div id="technical" class="scroll-mt-24">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cog text-purple-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Technical Support</h2>
            </div>
            
            <div class="space-y-3">
                <div class="faq-item bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button class="faq-question w-full text-left px-6 py-4 font-medium text-gray-900 hover:bg-gray-50 transition flex justify-between items-center">
                        <span>Why aren't videos playing?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4 text-gray-600 border-t border-gray-100 pt-3">
                        <p>If videos aren't playing, try these solutions:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Check your internet connection</li>
                            <li>Disable ad-blockers or VPNs temporarily</li>
                            <li>Clear your browser cache and cookies</li>
                            <li>Try a different browser (Chrome, Firefox, or Edge recommended)</li>
                            <li>Make sure JavaScript is enabled in your browser</li>
                        </ul>
                        <p class="mt-2">If problems persist, please contact our support team.</p>
                    </div>
                </div>

                <div class="faq-item bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button class="faq-question w-full text-left px-6 py-4 font-medium text-gray-900 hover:bg-gray-50 transition flex justify-between items-center">
                        <span>Is FluentEdge mobile-friendly?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4 text-gray-600 border-t border-gray-100 pt-3">
                        <p>Yes! FluentEdge is fully responsive and works on all devices:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li><strong>Desktop:</strong> Best experience on larger screens</li>
                            <li><strong>Tablet:</strong> Fully optimized for iPad and Android tablets</li>
                            <li><strong>Mobile:</strong> Practice on-the-go with our mobile-friendly design</li>
                        </ul>
                        <p class="mt-2">You can access all features from your phone's browser - no app download required!</p>
                    </div>
                </div>

                <div class="faq-item bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button class="faq-question w-full text-left px-6 py-4 font-medium text-gray-900 hover:bg-gray-50 transition flex justify-between items-center">
                        <span>How do I use the AI Chatbot?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4 text-gray-600 border-t border-gray-100 pt-3">
                        <p>The AI Chatbot is your personal learning assistant! Here's how to use it:</p>
                        <ol class="list-decimal list-inside mt-2 space-y-1">
                            <li>Click the chat icon in the top-right corner of any page</li>
                            <li>Type your question about English learning</li>
                            <li>Ask about specific skills, levels, or get recommendations</li>
                            <li>The bot will provide helpful responses and link to relevant lessons</li>
                        </ol>
                        <p class="mt-2">Try asking: "Show me elementary level" or "How can I improve my speaking?"</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account -->
        <div id="account" class="scroll-mt-24">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-circle text-yellow-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Account Management</h2>
            </div>
            
            <div class="space-y-3">
                <div class="faq-item bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button class="faq-question w-full text-left px-6 py-4 font-medium text-gray-900 hover:bg-gray-50 transition flex justify-between items-center">
                        <span>How do I update my profile?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4 text-gray-600 border-t border-gray-100 pt-3">
                        <p>To update your profile:</p>
                        <ol class="list-decimal list-inside mt-2 space-y-1">
                            <li>Click on your name/avatar in the top-right corner</li>
                            <li>Select "Profile Settings" from the dropdown menu</li>
                            <li>Update your name, email, or profile picture</li>
                            <li>Click "Save Changes" to update your information</li>
                        </ol>
                        <p class="mt-2">You can also change your learning level from this page!</p>
                    </div>
                </div>

                <div class="faq-item bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button class="faq-question w-full text-left px-6 py-4 font-medium text-gray-900 hover:bg-gray-50 transition flex justify-between items-center">
                        <span>Is FluentEdge free?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4 text-gray-600 border-t border-gray-100 pt-3">
                        <p>Yes! FluentEdge is completely free to use. All features are available at no cost:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>✓ Access to all English levels (Elementary, Intermediate, Advanced)</li>
                            <li>✓ All skills and practice questions</li>
                            <li>✓ Video lessons and learning materials</li>
                            <li>✓ Progress tracking and analytics</li>
                            <li>✓ AI Chatbot assistance</li>
                        </ul>
                        <p class="mt-2">We believe in making quality English education accessible to everyone!</p>
                    </div>
                </div>

                <div class="faq-item bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button class="faq-question w-full text-left px-6 py-4 font-medium text-gray-900 hover:bg-gray-50 transition flex justify-between items-center">
                        <span>How do I reset my password?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="faq-answer hidden px-6 pb-4 text-gray-600 border-t border-gray-100 pt-3">
                        <p>To reset your password:</p>
                        <ol class="list-decimal list-inside mt-2 space-y-1">
                            <li>Click on "Login" in the navigation menu</li>
                            <li>Click the "Forgot your password?" link</li>
                            <li>Enter your registered email address</li>
                            <li>Check your email for password reset instructions</li>
                            <li>Follow the link to create a new password</li>
                        </ol>
                        <p class="mt-2">If you don't receive the email, check your spam folder or contact support.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Still Have Questions? -->
    <div class="mt-12 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8 text-center">
        <i class="fas fa-headset text-4xl text-blue-600 mb-4"></i>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Still have questions?</h3>
        <p class="text-gray-600 mb-6">We're here to help you succeed in your English learning journey</p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="openChatbot()" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md">
                <i class="fas fa-comment-dots mr-2"></i>
                Chat with AI Bot
            </button>
            <a href="mailto:support@fluentedgetest.com" class="inline-flex items-center px-6 py-3 border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition">
                <i class="fas fa-envelope mr-2"></i>
                Email Support
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // FAQ Accordion Functionality
    document.querySelectorAll('.faq-question').forEach(button => {
        button.addEventListener('click', () => {
            const answer = button.nextElementSibling;
            const icon = button.querySelector('.fa-chevron-down');
            
            // Close all other FAQs
            document.querySelectorAll('.faq-answer').forEach(otherAnswer => {
                if (otherAnswer !== answer && otherAnswer.classList.contains('show')) {
                    otherAnswer.classList.remove('show');
                    otherAnswer.classList.add('hidden');
                    const otherIcon = otherAnswer.previousElementSibling.querySelector('.fa-chevron-down');
                    if (otherIcon) {
                        otherIcon.style.transform = 'rotate(0deg)';
                    }
                }
            });
            
            // Toggle current FAQ
            answer.classList.toggle('show');
            answer.classList.toggle('hidden');
            
            // Rotate icon
            if (answer.classList.contains('show')) {
                icon.style.transform = 'rotate(180deg)';
            } else {
                icon.style.transform = 'rotate(0deg)';
            }
        });
    });
    
    // Search Functionality
    document.getElementById('searchFaq').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const faqItems = document.querySelectorAll('.faq-item');
        
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question span').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Smooth scroll to category
    function scrollToCategory(categoryId) {
        const element = document.getElementById(categoryId);
        if (element) {
            const offset = 80;
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - offset;
            
            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    }
    
    // Open Chatbot function
    function openChatbot() {
        const chatbotToggle = document.getElementById('chatbot-toggle');
        if (chatbotToggle) {
            chatbotToggle.click();
            // Scroll to bottom of page to see chatbot
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });
        } else {
            // If chatbot button doesn't exist, redirect to login
            window.location.href = '{{ route("login") }}';
        }
    }
    
    // Make functions available globally
    window.scrollToCategory = scrollToCategory;
    window.openChatbot = openChatbot;
</script>
@endpush

@push('styles')
<style>
    .faq-answer {
        transition: all 0.3s ease;
    }
    
    .faq-answer.show {
        display: block !important;
    }
    
    .faq-question .fa-chevron-down {
        transition: transform 0.3s ease;
    }
    
    .scroll-mt-24 {
        scroll-margin-top: 6rem;
    }
    
    /* Custom scrollbar for search results */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush
@endsection