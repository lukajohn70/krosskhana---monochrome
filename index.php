<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krosskhana Avant Garde - Automotive Lifestyle Event</title>
    <meta name="description" content="Krosskhana is an automotive lifestyle company that seamlessly blends motor sport with diverse forms of entertainment, including music, fashion, and cultural exhibitions.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/krosskhana.png">
    <link rel="shortcut icon" type="image/png" href="assets/images/krosskhana.png">
    <link rel="apple-touch-icon" href="assets/images/krosskhana.png">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        'orange': { // Custom orange color palette
                            50: '#fffaf0',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316', // Main accent orange
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                            950: '#43140a',
                        },
                        'gray': { // Re-defining gray scale for consistency with dark theme
                            50: '#f9fafb',
                            100: '#f3f4f6',
                            200: '#e5e7eb',
                            300: '#d1d5db',
                            400: '#9ca3af',
                            500: '#6b7280',
                            600: '#4b5563',
                            700: '#374151',
                            800: '#1f2937',
                            900: '#111827',
                            950: '#030712', // Deepest background
                        }
                    },
                    keyframes: {
                        fadeInDown: {
                            '0%': { opacity: '0', transform: 'translateY(-20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        fadeInLeft: {
                            '0%': { opacity: '0', transform: 'translateX(-30px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                        fadeInRight: {
                            '0%': { opacity: '0', transform: 'translateX(30px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                        slideInFromTop: {
                            '0%': { opacity: '0', transform: 'translateY(-100%)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideInFromBottom: {
                            '0%': { opacity: '0', transform: 'translateY(100%)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        scaleIn: {
                            '0%': { opacity: '0', transform: 'scale(0.8)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        },
                        spinSlow: {
                            '0%': { transform: 'rotate(0deg)' },
                            '100%': { transform: 'rotate(360deg)' },
                        },
                        bounceSlow: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        glow: {
                            '0%, 100%': { boxShadow: '0 0 20px rgba(239, 68, 68, 0.3)' },
                            '50%': { boxShadow: '0 0 40px rgba(239, 68, 68, 0.6)' },
                        }
                    },
                    animation: {
                        'fade-in-down': 'fadeInDown 1s ease-out forwards',
                        'fade-in-up': 'fadeInUp 1s ease-out forwards 0.5s',
                        'fade-in': 'fadeIn 1s ease-out forwards 1s',
                        'fade-in-left': 'fadeInLeft 1s ease-out forwards',
                        'fade-in-right': 'fadeInRight 1s ease-out forwards',
                        'slide-in-top': 'slideInFromTop 0.8s ease-out forwards',
                        'slide-in-bottom': 'slideInFromBottom 0.8s ease-out forwards',
                        'scale-in': 'scaleIn 0.6s ease-out forwards',
                        'spin-slow': 'spinSlow 20s linear infinite',
                        'bounce-slow': 'bounceSlow 2s infinite ease-in-out',
                        'float': 'float 3s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite',
                    },
                    spacing: {
                        '16': '4rem',
                        '20': '5rem',
                        '24': '6rem',
                        '32': '8rem',
                        '40': '10rem',
                        '48': '12rem', /* Added for hero section padding */
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
    <script>
        console.log('Main.js loaded in head:', typeof switchToUpcomingEvents);
    </script>
    <style>
        /* Custom scroll behavior for smooth scrolling when clicking nav links */
        html {
            scroll-behavior: smooth;
        }
        
        /* Enhanced smooth scrolling for better performance */
        * {
            scroll-behavior: smooth;
        }
        
        /* Custom height for hero section to ensure it fills most of the viewport, minus nav height */
        .min-h-screen-minus-nav {
            min-height: calc(100vh - 4rem); /* Assuming nav height is approx 4rem */
        }
        
        /* Style for the underline on headings */
        h3 span {
            display: block;
            width: 80px; /* Adjust width as needed */
            height: 4px;
            background-color: var(--color-orange-500); /* Use Tailwind's custom orange */
            margin: 10px auto 0;
            border-radius: 2px;
        }
        
        /* Enhanced navigation link animations */
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #f87171;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        /* Smooth section transitions */
        section {
            transition: all 0.5s ease-in-out;
        }
        
        /* Enhanced button hover effects */
        .btn-hover-effect {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-hover-effect:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        /* Scroll indicator animation */
        .scroll-indicator {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="font-poppins">
    <!-- Splash Screen -->
    <div id="splash-screen" class="fixed inset-0 z-50 bg-black">
        <!-- Video Background with Zoom Effect -->
        <div class="absolute inset-0 overflow-hidden">
            <video id="splash-video" class="absolute inset-0 w-full h-full object-cover scale-150 transition-transform duration-10000 ease-out" autoplay muted playsinline>
                <source src="assets/videos/2.mp4" type="video/mp4">
            </video>
        </div>
        
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/30"></div>
        
        <!-- Skip Button -->
        <button id="skip-splash" class="absolute top-6 right-6 z-10 bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-full font-semibold hover:bg-white/30 transition-all duration-300">
            Skip
        </button>
        
        <!-- Progress Bar Container -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 w-80 z-10">
            <!-- Progress Bar -->
            <div class="w-full bg-white/20 backdrop-blur-sm rounded-full h-2 mb-4">
                <div id="splash-progress" class="bg-gradient-to-r from-red-500 to-orange-500 h-2 rounded-full transition-all duration-1000" style="width: 0%"></div>
            </div>
            <!-- Loading Text -->
            <p class="text-white text-center font-medium">Loading Experience...</p>
        </div>
    </div>

    <!-- Main Application -->
    <div id="main-app" class="font-poppins min-h-screen transition-all duration-1000 hidden">
        <!-- Animated background gradient -->
        <div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-[-2]"></div>
        <div class="fixed inset-0 bg-gradient-to-r from-red-900/20 via-transparent to-orange-900/20 z-[-1]"></div>
        
        <!-- Floating particles effect -->
        <div class="fixed inset-0 z-[-1] overflow-hidden">
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 10%; top: 20%; animation-delay: 0s; animation-duration: 2.5s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 20%; top: 80%; animation-delay: 1s; animation-duration: 3s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 30%; top: 40%; animation-delay: 2s; animation-duration: 2.8s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 40%; top: 60%; animation-delay: 0.5s; animation-duration: 3.2s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 50%; top: 10%; animation-delay: 1.5s; animation-duration: 2.7s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 60%; top: 90%; animation-delay: 0.8s; animation-duration: 3.1s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 70%; top: 30%; animation-delay: 2.3s; animation-duration: 2.9s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 80%; top: 70%; animation-delay: 1.2s; animation-duration: 3.3s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 90%; top: 50%; animation-delay: 0.3s; animation-duration: 2.6s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 15%; top: 85%; animation-delay: 1.8s; animation-duration: 3.4s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 25%; top: 15%; animation-delay: 0.7s; animation-duration: 2.4s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 35%; top: 75%; animation-delay: 2.1s; animation-duration: 3.5s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 45%; top: 25%; animation-delay: 1.4s; animation-duration: 2.3s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 55%; top: 95%; animation-delay: 0.9s; animation-duration: 3.6s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 65%; top: 5%; animation-delay: 2.7s; animation-duration: 2.2s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 75%; top: 55%; animation-delay: 1.6s; animation-duration: 3.7s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 85%; top: 35%; animation-delay: 0.4s; animation-duration: 2.1s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 95%; top: 65%; animation-delay: 2.9s; animation-duration: 3.8s;"></div>
            <div class="absolute w-2 h-2 bg-red-500/30 rounded-full animate-pulse" style="left: 5%; top: 45%; animation-delay: 1.1s; animation-duration: 2.8s;"></div>
        </div>

        <!-- Navigation Bar - Modern Glassmorphism -->
    <nav class="bg-black/20 backdrop-blur-xl border-b border-white/10 p-4 fixed w-full z-50 top-0 transition-all duration-500 shadow-2xl">
        <div class="container mx-auto flex justify-between items-center py-2 px-4 md:px-0">
            <!-- Logo -->
            <div class="flex items-center space-x-4">
                <div class="relative group cursor-pointer" onclick="scrollToSection('home')">
                    <img src="assets/images/krosskhana.png" alt="Krosskhana Logo" class="h-14 w-auto transition-transform duration-300 group-hover:scale-110" />
                    <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                </div>
            </div>
            
            <!-- Mobile Centered Text -->
            <div class="md:hidden absolute left-1/2 transform -translate-x-1/2">
                <h1 class="text-xl font-black uppercase text-transparent bg-clip-text bg-gradient-to-r from-white via-red-400 to-orange-400">
                    KROSSKHANA
                </h1>
            </div>
            
            <!-- Desktop Navigation -->
            <ul class="hidden md:flex space-x-6 text-white text-sm font-medium uppercase tracking-wider">
                <li>
                    <button onclick="scrollToSection('home')" class="nav-link relative px-4 py-2 rounded-full transition-all duration-300 group bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg">
                        <span class="relative z-10">Home</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    </button>
                </li>
                <li>
                    <button onclick="scrollToSection('about')" class="nav-link relative px-4 py-2 rounded-full transition-all duration-300 group text-gray-300">
                        <span class="relative z-10">About</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    </button>
                </li>
                <li>
                    <button onclick="scrollToSection('events')" class="nav-link relative px-4 py-2 rounded-full transition-all duration-300 group text-gray-300">
                        <span class="relative z-10">Events</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    </button>
                </li>
                <li>
                    <button onclick="scrollToSection('team')" class="nav-link relative px-4 py-2 rounded-full transition-all duration-300 group text-gray-300">
                        <span class="relative z-10">Team</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    </button>
                </li>
                <li>
                    <button onclick="scrollToSection('participate')" class="nav-link relative px-4 py-2 rounded-full transition-all duration-300 group text-gray-300">
                        <span class="relative z-10">Join</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    </button>
                </li>
                <li>
                    <button onclick="scrollToSection('news-brief')" class="nav-link relative px-4 py-2 rounded-full transition-all duration-300 group text-gray-300">
                        <span class="relative z-10">News</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    </button>
                </li>
                <!-- More Dropdown -->
                <li class="relative group">
                    <button id="more-dropdown-btn" class="nav-link relative px-4 py-2 rounded-full transition-all duration-300 group text-gray-300 flex items-center space-x-1">
                        <span class="relative z-10">More</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    </button>
                    <!-- Dropdown Menu -->
                    <div id="more-dropdown" class="absolute top-full left-0 mt-2 w-48 bg-black/90 backdrop-blur-xl border border-white/10 rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform group-hover:translate-y-0 translate-y-2 z-50">
                        <div class="py-2">
                            <button onclick="scrollToSection('mission')" class="w-full text-left px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 flex items-center space-x-3">
                                <i class="fas fa-bullseye text-red-500"></i>
                                <span>Mission</span>
                            </button>
                            <button onclick="scrollToSection('explore')" class="w-full text-left px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 flex items-center space-x-3">
                                <i class="fas fa-compass text-orange-500"></i>
                                <span>Explore</span>
                            </button>
                            <button onclick="scrollToSection('highlights')" class="w-full text-left px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 flex items-center space-x-3">
                                <i class="fas fa-star text-yellow-500"></i>
                                <span>Highlights</span>
                            </button>
                            <button onclick="scrollToSection('sponsors')" class="w-full text-left px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 flex items-center space-x-3">
                                <i class="fas fa-handshake text-blue-500"></i>
                                <span>Sponsors</span>
                            </button>
                        </div>
                    </div>
                </li>

            </ul>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden text-white p-2 rounded-lg hover:bg-white/10 transition-colors duration-300">
                <div class="w-6 h-6 flex flex-col justify-center items-center">
                    <span class="block w-5 h-0.5 bg-white transition-all duration-300"></span>
                    <span class="block w-5 h-0.5 bg-white transition-all duration-300 mt-1"></span>
                    <span class="block w-5 h-0.5 bg-white transition-all duration-300 mt-1"></span>
                </div>
            </button>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu" class="md:hidden transition-all duration-300 ease-in-out max-h-0 opacity-0 overflow-hidden">
            <div class="bg-black/40 backdrop-blur-xl border-t border-white/10">
                <ul class="flex flex-col space-y-2 p-4 text-white text-sm font-medium uppercase tracking-wider">
                    <li>
                        <button onclick="scrollToSection('home')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group bg-gradient-to-r from-red-500 to-orange-500 text-white shadow-lg">
                            <span class="relative z-10">Home</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('about')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300">
                            <span class="relative z-10">About</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('mission')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300">
                            <span class="relative z-10">Mission</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('explore')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300">
                            <span class="relative z-10">Explore</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('events')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300">
                            <span class="relative z-10">Events</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('team')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300">
                            <span class="relative z-10">Team</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('highlights')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300">
                            <span class="relative z-10">Highlights</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('participate')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300">
                            <span class="relative z-10">Join</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('sponsors')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300">
                            <span class="relative z-10">Sponsors</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('news-brief')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300">
                            <span class="relative z-10">News</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        </button>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section - Ultra Modern -->
    <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Dynamic video background with enhanced overlay -->
        <video
            class="absolute inset-0 w-full h-full object-cover z-0"
            src="assets/videos/1.mp4"
            autoplay
            loop
            muted
            playsinline
        ></video>
        
        <!-- Advanced gradient overlays -->
        <div class="absolute inset-0 z-10 pointer-events-none">
            <div class="w-full h-full bg-gradient-to-br from-black/80 via-black/60 to-red-900/40"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/50 via-transparent to-black/50"></div>
        </div>

        <!-- Floating geometric shapes -->
        <div class="absolute inset-0 z-5 overflow-hidden pointer-events-none">
            <div 
                class="absolute top-20 left-20 w-32 h-32 border border-red-500/30 rotate-45 animate-spin-slow"
                style="animation-duration: 20s;"
            ></div>
            <div 
                class="absolute bottom-20 right-20 w-24 h-24 border border-orange-500/30 rounded-full animate-pulse"
                style="animation-delay: 2s;"
            ></div>
            <div 
                class="absolute top-1/2 left-10 w-16 h-16 bg-gradient-to-r from-red-500 to-orange-500 rounded-full opacity-20 animate-bounce-slow"
                style="animation-delay: 1s;"
            ></div>
        </div>

        <div class="container mx-auto text-center relative z-20 px-4">
            <!-- Main title -->
            <div class="relative mb-8">
                <h2 class="text-6xl md:text-9xl font-black mb-4 tracking-tight uppercase text-transparent bg-clip-text bg-gradient-to-r from-white via-red-400 to-orange-400 animate-fade-in-down drop-shadow-2xl">
                    KROSSKHANA
                </h2>
                <h3 class="text-4xl md:text-6xl font-bold tracking-wider uppercase text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-orange-400 animate-fade-in-down" style="animation-delay: 0.3s">
                    MOTOR SPORTS
                </h3>
                <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 blur-3xl opacity-20 animate-pulse"></div>
            </div>

            <!-- Tagline -->
            <div class="relative mb-12">
                <p class="text-2xl md:text-4xl font-light text-gray-200 min-h-[3rem] animate-fade-in-up" style="animation-delay: 0.6s">
                    Fueling Culture. Driving Stories. Partnering for Impact.
                </p>
                <div class="absolute inset-0 bg-gradient-to-r from-red-500/20 to-orange-500/20 blur-xl"></div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce pointer-events-none">
                <div class="w-6 h-10 border-2 border-white/50 rounded-full flex justify-center">
                    <div class="w-1 h-3 bg-white/70 rounded-full mt-2 animate-pulse"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section - Modern Glassmorphism -->
    <section id="about" class="py-20 md:py-32 relative overflow-hidden">
        <!-- Background with gradient and effects -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-red-500/5 via-transparent to-orange-500/5"></div>
        
        <!-- Floating elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 right-20 w-64 h-64 bg-gradient-to-r from-red-500/10 to-orange-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 left-20 w-48 h-48 bg-gradient-to-r from-orange-500/10 to-red-500/10 rounded-full blur-3xl"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-20">
                <h3 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-600 mb-8 animate-fade-in-down">
                    About Us
                </h3>
                <div class="w-32 h-1 bg-gradient-to-r from-red-500 to-orange-500 mx-auto rounded-full animate-fade-in-up"></div>
            </div>
            
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <!-- Modern image container -->
                <div class="lg:w-1/2 animate-fade-in-left">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl blur-xl opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                        <div class="relative bg-white rounded-2xl p-4 shadow-2xl transform group-hover:scale-105 transition-all duration-500">
                            <div class="aspect-[16/10] w-full">
                                <video
                                    class="rounded-xl w-full h-full object-cover"
                                    src="assets/videos/1.mp4"
                                    autoplay
                                    loop
                                    muted
                                    playsinline
                                    onended="cycleAboutVideo()"
                                ></video>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent rounded-xl"></div>
                        </div>
                    </div>
                </div>

                <!-- Modern content container -->
                <div class="lg:w-1/2 animate-fade-in-right">
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20">
                        <div class="space-y-6 text-lg leading-relaxed text-gray-700">
                            <h4 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-600 mb-4">KROSSKHANA MOTOR SPORTS: IGNITING THE FUTURE OF AUTOMOTIVE CULTURE</h4>
                            <p class="relative">
                                <span class="absolute -left-4 top-0 w-1 h-full bg-gradient-to-b from-red-500 to-orange-500 rounded-full"></span>
                                <span class="font-bold text-red-600">We are revolution on wheels.</span> <span class="text-red-500 font-semibold">Krosskhana</span> transcends traditional motor sport, fusing high-octane racing thrills with the pulse of music, fashion, and art.
                            </p>
                            <p>
                                We evolved into Abuja's premier automotive lifestyle experience – hosting four consecutive sold-out events (<span class="text-orange-600 font-bold">2,000+ attendees</span>) and the exclusive <span class="font-bold text-red-600">Fast X premiere</span>.
                            </p>
                            <p>
                                We build <span class="text-orange-600 font-semibold">passionate communities</span> and redefine what <span class="text-red-600 font-semibold">automotive culture</span> means.
                            </p>
                        </div>
                        

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section id="mission" class="py-20 md:py-32 relative overflow-hidden">
        <!-- Background with gradient and effects -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-red-500/5 via-transparent to-orange-500/5"></div>
        
        <!-- Floating elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-20 w-64 h-64 bg-gradient-to-r from-red-500/10 to-orange-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-48 h-48 bg-gradient-to-r from-orange-500/10 to-red-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-20">
                <h3 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-600 mb-8 animate-fade-in-down">
                    WHAT FUELS OUR MISSION
                </h3>
                <div class="w-32 h-1 bg-gradient-to-r from-red-500 to-orange-500 mx-auto rounded-full animate-fade-in-up"></div>
            </div>
            
            <div class="max-w-4xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Safety First -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20 animate-fade-in-up">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-orange-500 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-shield-alt text-white text-xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800">Safety First</h4>
                        </div>
                        <div class="space-y-3 text-gray-700">
                            <p>Professional-tier safety standards adapted for community access.</p>
                            <p>Meticulous design, expert oversight & responsible driving advocacy.</p>
                            <p class="text-sm italic text-gray-600">(Track-certified barriers • Mandatory briefings • Onsite medics • Gear checks)</p>
                            <p class="font-bold text-red-600">Your safety is paramount.</p>
                        </div>
                    </div>

                    <!-- Community-Driven -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20 animate-fade-in-up">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800">Community-Driven</h4>
                        </div>
                        <div class="space-y-3 text-gray-700">
                            <p>Uniting enthusiasts, artists & creators.</p>
                        </div>
                    </div>

                    <!-- Motor Sports-Centered -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20 animate-fade-in-up">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-orange-500 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-car-side text-white text-xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800">Motor Sports-Centered</h4>
                        </div>
                        <div class="space-y-3 text-gray-700">
                            <p>Authentic racing & precision driving at our core.</p>
                        </div>
                    </div>

                    <!-- Culture-Infused -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20 animate-fade-in-up">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-palette text-white text-xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800">Culture-Infused</h4>
                        </div>
                        <div class="space-y-3 text-gray-700">
                            <p>Music, fashion & art fused with automotive passion.</p>
                        </div>
                    </div>

                    <!-- Innovation-Led -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20 animate-fade-in-up">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-orange-500 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-lightbulb text-white text-xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800">Innovation-Led</h4>
                        </div>
                        <div class="space-y-3 text-gray-700">
                            <p>Cutting-edge tech & groundbreaking experiences.</p>
                        </div>
                    </div>

                    <!-- Excellence Focused -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20 animate-fade-in-up">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-star text-white text-xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800">Excellence Focused</h4>
                        </div>
                        <div class="space-y-3 text-gray-700">
                            <p>Uncompromising standards in everything we do.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Explore Section -->
    <section id="explore" class="py-20 md:py-32 relative overflow-hidden">
        <!-- Background with gradient and effects -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-red-900/20 via-transparent to-orange-900/20"></div>
        
        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 left-1/4 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-20">
                <h3 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500 mb-8 animate-fade-in-down">
                    EXPLORE THE WORLD OF KROSSKHANA
                </h3>
                <div class="w-32 h-1 bg-gradient-to-r from-yellow-400 to-orange-500 mx-auto rounded-full animate-fade-in-up"></div>
            </div>
            
            <!-- Carousel Container -->
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Autocross & Drifting Competitions -->
                    <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20 animate-fade-in-up">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                <i class="fas fa-car-side text-white text-3xl"></i>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white text-center group-hover:text-red-400 transition-colors duration-300">
                            Autocross & Drifting Competitions
                        </h4>
                        <p class="text-gray-300 text-center leading-relaxed">
                            Experience the raw thrill and precision of authentic racing.
                        </p>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-red-500 to-orange-500 rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                    </div>

                    <!-- R&D Showcases & Innovations -->
                    <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20 animate-fade-in-up">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                <i class="fas fa-microscope text-white text-3xl"></i>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white text-center group-hover:text-orange-400 transition-colors duration-300">
                            R&D Showcases & Innovations
                        </h4>
                        <p class="text-gray-300 text-center leading-relaxed">
                            Discover cutting-edge tech and future automotive developments.
                        </p>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-orange-500 to-red-500 rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                    </div>

                    <!-- Lifestyle Shows & Music Collabs -->
                    <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20 animate-fade-in-up">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                <i class="fas fa-music text-white text-3xl"></i>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white text-center group-hover:text-red-400 transition-colors duration-300">
                            Lifestyle Shows & Music Collabs
                        </h4>
                        <p class="text-gray-300 text-center leading-relaxed">
                            Immerse yourself where automotive passion meets electrifying entertainment.
                        </p>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-red-500 to-orange-500 rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                    </div>

                    <!-- Fashion & Brand Activations -->
                    <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20 animate-fade-in-up">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                <i class="fas fa-tshirt text-white text-3xl"></i>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white text-center group-hover:text-orange-400 transition-colors duration-300">
                            Fashion & Brand Activations
                        </h4>
                        <p class="text-gray-300 text-center leading-relaxed">
                            Showcase your brand through innovative displays and strategic partnerships.
                        </p>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-orange-500 to-red-500 rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                    </div>

                    <!-- Market Engagement & Fan Loyalty -->
                    <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20 animate-fade-in-up">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                <i class="fas fa-heart text-white text-3xl"></i>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white text-center group-hover:text-red-400 transition-colors duration-300">
                            Market Engagement & Fan Loyalty
                        </h4>
                        <p class="text-gray-300 text-center leading-relaxed">
                            Build deep connections with our passionate, loyal community.
                        </p>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-red-500 to-orange-500 rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                    </div>

                    <!-- Exclusive High-End Events -->
                    <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20 animate-fade-in-up">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                <i class="fas fa-crown text-white text-3xl"></i>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white text-center group-hover:text-orange-400 transition-colors duration-300">
                            Exclusive High-End Events
                        </h4>
                        <p class="text-gray-300 text-center leading-relaxed">
                            Experience premium motor sport crafted for discerning enthusiasts.
                        </p>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-orange-500 to-red-500 rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quote Section -->
    <section id="quote" class="py-20 md:py-32 relative overflow-hidden">
        <!-- Background with gradient and effects -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-red-500/5 via-transparent to-orange-500/5"></div>
        
        <!-- Floating elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 right-20 w-64 h-64 bg-gradient-to-r from-red-500/10 to-orange-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 left-20 w-48 h-48 bg-gradient-to-r from-orange-500/10 to-red-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-12 shadow-2xl border border-white/20 animate-fade-in-up">
                    <div class="relative">
                        <!-- Quote icon -->
                        <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                            <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-quote-left text-white text-xl"></i>
                            </div>
                        </div>
                        
                        <!-- Quote text -->
                        <blockquote class="text-3xl md:text-4xl font-bold text-gray-800 leading-relaxed pt-8">
                            We don't just host events — we ignite movements, drive innovation, and create unforgettable experiences, all anchored in safety and excellence.
                        </blockquote>
                        
                        <!-- Decorative elements -->
                        <div class="mt-8 flex justify-center space-x-4">
                            <div class="w-2 h-2 bg-gradient-to-r from-red-500 to-orange-500 rounded-full"></div>
                            <div class="w-2 h-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-full"></div>
                            <div class="w-2 h-2 bg-gradient-to-r from-red-500 to-orange-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section id="events" class="py-20 md:py-32 relative overflow-hidden">
        <!-- Background with gradient and effects -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-red-900/20 via-transparent to-orange-900/20"></div>
        
        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-20">
                <h3 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500 mb-8 animate-fade-in-down">
                    Events
                </h3>
                <div class="w-32 h-1 bg-gradient-to-r from-yellow-400 to-orange-500 mx-auto rounded-full animate-fade-in-up"></div>
            </div>

            <!-- Event Tabs -->
            <div class="flex justify-center mb-12">
                <div class="bg-gray-900/50 backdrop-blur-xl rounded-full p-2 border border-gray-700/50">
                    <button id="past-events-tab" onclick="switchToPastEvents()" class="px-8 py-3 rounded-full text-white font-semibold transition-all duration-300 bg-gradient-to-r from-red-500 to-orange-500 shadow-lg">
                        Past Events
                    </button>
                    <button id="upcoming-events-tab" onclick="switchToUpcomingEvents()" class="px-8 py-3 rounded-full text-gray-300 font-semibold transition-all duration-300 hover:text-white hover:bg-white/10">
                        Upcoming Events
                    </button>
                </div>
            </div>
            

            


            <!-- Past Events Carousel -->
            <div id="past-events-content" class="max-w-6xl mx-auto" style="display: block;">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Event 1 -->
                    <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl overflow-hidden shadow-2xl border border-gray-700/50 transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20 animate-fade-in-up">
                        <div class="relative h-48 bg-gradient-to-br from-red-500 to-orange-500">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <div class="absolute top-4 right-4 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Past Event
                            </div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h4 class="text-xl font-bold">Krosskhana 2023</h4>
                                <p class="text-sm opacity-90">December 2023</p>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-300 mb-4">Experience the thrill of our 2023 edition with over 2,000 attendees and exclusive Fast X premiere.</p>
                            <button onclick="openEventGallery('krosskhana-2023')" class="w-full bg-gradient-to-r from-red-500 to-orange-500 text-white font-semibold py-3 rounded-xl hover:from-red-600 hover:to-orange-600 transition-all duration-300">
                                View Gallery
                            </button>
                        </div>
                    </div>

                    <!-- Event 2 -->
                    <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl overflow-hidden shadow-2xl border border-gray-700/50 transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20 animate-fade-in-up">
                        <div class="relative h-48 bg-gradient-to-br from-orange-500 to-red-500">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <div class="absolute top-4 right-4 bg-orange-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Past Event
                            </div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h4 class="text-xl font-bold">Motor Sport Festival</h4>
                                <p class="text-sm opacity-90">October 2023</p>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-300 mb-4">A celebration of automotive culture featuring drifting competitions and live performances.</p>
                            <button onclick="openEventGallery('motor-sport-festival-2023')" class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold py-3 rounded-xl hover:from-orange-600 hover:to-red-600 transition-all duration-300">
                                View Gallery
                            </button>
                        </div>
                    </div>

                    <!-- Event 3 -->
                    <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl overflow-hidden shadow-2xl border border-gray-700/50 transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20 animate-fade-in-up">
                        <div class="relative h-48 bg-gradient-to-br from-red-500 to-orange-500">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <div class="absolute top-4 right-4 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Past Event
                            </div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h4 class="text-xl font-bold">Summer Drift</h4>
                                <p class="text-sm opacity-90">August 2023</p>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-300 mb-4">High-octane drifting competition with music and fashion showcases.</p>
                            <button onclick="openEventGallery('summer-drift-2023')" class="w-full bg-gradient-to-r from-red-500 to-orange-500 text-white font-semibold py-3 rounded-xl hover:from-red-600 hover:to-orange-600 transition-all duration-300">
                                View Gallery
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events Carousel -->
            <div id="upcoming-events-content" class="max-w-6xl mx-auto" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Avant Gardex -->
                    <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl overflow-hidden shadow-2xl border border-gray-700/50 transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20 animate-fade-in-up">
                        <div class="relative h-48 bg-gradient-to-br from-yellow-500 to-orange-500">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <div class="absolute top-4 right-4 bg-yellow-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Coming Soon
                            </div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h4 class="text-xl font-bold">Avant Gardex</h4>
                                <p class="text-sm opacity-90">March 2024</p>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-300 mb-4">The most anticipated event of the year - where innovation meets automotive excellence.</p>
                            <button onclick="openEventGallery('avant-gardex-2024')" class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white font-semibold py-3 rounded-xl hover:from-yellow-600 hover:to-orange-600 transition-all duration-300">
                                Learn More
                            </button>
                        </div>
                    </div>

                    <!-- Spring Racing -->
                    <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl overflow-hidden shadow-2xl border border-gray-700/50 transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20 animate-fade-in-up">
                        <div class="relative h-48 bg-gradient-to-br from-green-500 to-blue-500">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <div class="absolute top-4 right-4 bg-green-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Coming Soon
                            </div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h4 class="text-xl font-bold">Spring Racing</h4>
                                <p class="text-sm opacity-90">May 2024</p>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-300 mb-4">Revolutionary racing experience with cutting-edge technology and innovation showcases.</p>
                            <button onclick="openEventGallery('spring-racing-2024')" class="w-full bg-gradient-to-r from-green-500 to-blue-500 text-white font-semibold py-3 rounded-xl hover:from-green-600 hover:to-blue-600 transition-all duration-300">
                                Learn More
                            </button>
                        </div>
                    </div>

                    <!-- Summer Festival -->
                    <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl overflow-hidden shadow-2xl border border-gray-700/50 transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20 animate-fade-in-up">
                        <div class="relative h-48 bg-gradient-to-br from-purple-500 to-pink-500">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <div class="absolute top-4 right-4 bg-purple-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                Coming Soon
                            </div>
                            <div class="absolute bottom-4 left-4 text-white">
                                <h4 class="text-xl font-bold">Summer Festival</h4>
                                <p class="text-sm opacity-90">July 2024</p>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-300 mb-4">A fusion of motor sport, music, fashion, and art in the ultimate summer celebration.</p>
                            <button onclick="openEventGallery('summer-festival-2024')" class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white font-semibold py-3 rounded-xl hover:from-purple-600 hover:to-pink-600 transition-all duration-300">
                                Learn More
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Teams Section - Modern Grid -->
    <section id="team" class="py-20 md:py-32 relative overflow-hidden">
        <!-- Background with gradient and effects -->
        <div class="absolute inset-0 bg-gradient-to-br from-purple-50 via-white to-blue-100"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 via-transparent to-purple-500/5"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-20">
                <h3 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-8 animate-fade-in-down">
                    Meet the Team
                </h3>
                <div class="w-32 h-1 bg-gradient-to-r from-blue-500 to-purple-500 mx-auto rounded-full animate-fade-in-up"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 max-w-4xl mx-auto">
                <div class="group bg-white/80 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20 flex flex-col items-center text-center animate-fade-in-up">
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-user-tie text-white text-3xl"></i>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-800 mb-2">Stevyn</h4>
                    <p class="text-lg text-blue-600 font-semibold mb-2">Team Lead</p>
                </div>
                <div class="group bg-white/80 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20 flex flex-col items-center text-center animate-fade-in-up">
                    <div class="w-20 h-20 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-tasks text-white text-3xl"></i>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-800 mb-2">Quame</h4>
                    <p class="text-lg text-blue-600 font-semibold mb-2">Project Manager</p>
                </div>

                <div class="group bg-white/80 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20 flex flex-col items-center text-center animate-fade-in-up">
                    <div class="w-20 h-20 bg-gradient-to-r from-yellow-500 to-blue-500 rounded-full flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-cogs text-white text-3xl"></i>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-800 mb-2">Femi</h4>
                    <p class="text-lg text-blue-600 font-semibold mb-2">Technical Lead</p>
                </div>
            </div>

        </div>
    </section>

    <!-- Event Highlights Section - Modern Cards -->
    <section id="highlights" class="py-20 md:py-32 relative overflow-hidden">
        <!-- Background with gradient and effects -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-red-900/20 via-transparent to-orange-900/20"></div>
        
        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-red-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-20">
                <h3 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500 mb-8 animate-fade-in-down">
                    Event Highlights
                </h3>
                <p class="text-xl text-gray-300 mb-4 animate-fade-in-up">2024 Edition</p>
                <p class="text-lg text-yellow-400 font-semibold mb-4 animate-fade-in-up">Projected Attendance: 800+ Enthusiasts</p>
                <div class="w-32 h-1 bg-gradient-to-r from-yellow-400 to-orange-500 mx-auto rounded-full animate-fade-in-up"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="group relative animate-fade-in-up" style="animation-delay: 0.1s">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-500/20 to-orange-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                <i class="fas fa-microphone text-white text-3xl"></i>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white text-center group-hover:text-red-400 transition-colors duration-300">
                            Music & DJ Performances
                        </h4>
                        <p class="text-gray-300 text-center leading-relaxed">
                            Experience electrifying Afro Fusion EDM Music from homegrown talents and top DJs, bringing the event to life!
                        </p>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-red-500 to-orange-500 rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                    </div>
                </div>
                <div class="group relative animate-fade-in-up" style="animation-delay: 0.2s">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-500/20 to-orange-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                <i class="fas fa-palette text-white text-3xl"></i>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white text-center group-hover:text-red-400 transition-colors duration-300">
                            Artistic Impression & Live Paintings
                        </h4>
                        <p class="text-gray-300 text-center leading-relaxed">
                            Witness fine artists creating live paintings of car art, showcasing incredible automotive artistry.
                        </p>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-red-500 to-orange-500 rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                    </div>
                </div>
                <div class="group relative animate-fade-in-up" style="animation-delay: 0.3s">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-500/20 to-orange-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                <i class="fas fa-shopping-bag text-white text-3xl"></i>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white text-center group-hover:text-red-400 transition-colors duration-300">
                            Pop Up Sale & Trunk Sales
                        </h4>
                        <p class="text-gray-300 text-center leading-relaxed">
                            Discover exclusive merch, accessories, local arts, books, and unique items from our vibrant trunk sales.
                        </p>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-red-500 to-orange-500 rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                    </div>
                </div>
                <div class="group relative animate-fade-in-up" style="animation-delay: 0.4s">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-500/20 to-orange-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                <i class="fas fa-tshirt text-white text-3xl"></i>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white text-center group-hover:text-red-400 transition-colors duration-300">
                            Fashion Forward Showcase
                        </h4>
                        <p class="text-gray-300 text-center leading-relaxed">
                            Experience cutting-edge fashion from established designers and up-and-coming talents, emphasizing sustainable practices and innovative design.
                        </p>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-red-500 to-orange-500 rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                    </div>
                </div>
                <div class="group relative animate-fade-in-up" style="animation-delay: 0.5s">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-500/20 to-orange-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                <i class="fas fa-car-side text-white text-3xl"></i>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white text-center group-hover:text-red-400 transition-colors duration-300">
                            Classic & Custom Car/Bike Showcase
                        </h4>
                        <p class="text-gray-300 text-center leading-relaxed">
                            Marvel at stunning classic and custom vehicles with special automotive reviews hosted by male and female pro car enthusiasts.
                        </p>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-red-500 to-orange-500 rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                    </div>
                </div>
                <div class="group relative animate-fade-in-up" style="animation-delay: 0.6s">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-500/20 to-orange-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-red-500/20">
                        <div class="relative mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                <i class="fas fa-trophy text-white text-3xl"></i>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                        </div>
                        <h4 class="text-2xl font-bold mb-4 text-white text-center group-hover:text-red-400 transition-colors duration-300">
                            Concours d'Elegance
                        </h4>
                        <p class="text-gray-300 text-center leading-relaxed">
                            Witness the prestigious Concours d'Elegance competition showcasing the finest classic and luxury vehicles in their prime condition.
                        </p>
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r from-red-500 to-orange-500 rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Join the Revolution Section -->
    <section id="participate" class="py-20 md:py-32 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-red-900 via-red-800 to-orange-900"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-red-500/20 via-transparent to-orange-500/20"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-20">
                <h3 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-yellow-300 mb-8 animate-fade-in-down">
                    JOIN THE REVOLUTION
                </h3>
                <p class="text-xl text-gray-200 mb-4 animate-fade-in-up">Forward-thinking brands and passionate enthusiasts partner with Krosskhana to set <span class="underline decoration-red-500">trends and embrace</span> the future of the automotive industry.</p>
                <div class="w-32 h-1 bg-gradient-to-r from-red-500 to-orange-500 mx-auto rounded-full animate-fade-in-up"></div>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <h4 class="text-3xl font-bold text-white mb-4 animate-fade-in-up">
                        Connect with <span class="underline decoration-red-500">Krosskhana</span>
                    </h4>
                    <p class="text-lg text-gray-300 italic mb-8 animate-fade-in-up">Select your interest:</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Corporate Enquiry -->
                    <div class="group relative animate-fade-in-up" style="animation-delay: 0.1s">
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                        <div class="relative bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-2xl transform group-hover:scale-105 transition-all duration-500 cursor-pointer" onclick="openForm('corporate')">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-building text-2xl text-white"></i>
                                </div>
                                <h5 class="text-2xl font-bold text-white mb-4">Corporate Enquiry</h5>
                                <p class="text-gray-300 mb-6">Partner with us for business opportunities and corporate collaborations.</p>
                                <button class="w-full bg-gradient-to-r from-red-500 to-orange-500 text-white font-semibold py-3 rounded-xl hover:from-red-600 hover:to-orange-600 transition-all duration-300">
                                    Get Started
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Racer Registration -->
                    <div class="group relative animate-fade-in-up" style="animation-delay: 0.2s">
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                        <div class="relative bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-2xl transform group-hover:scale-105 transition-all duration-500 cursor-pointer" onclick="openForm('racer')">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-car text-2xl text-white"></i>
                                </div>
                                <h5 class="text-2xl font-bold text-white mb-4">Racer Registration</h5>
                                <p class="text-gray-300 mb-6">Join our racing events and showcase your skills on the track.</p>
                                <button class="w-full bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold py-3 rounded-xl hover:from-orange-600 hover:to-red-600 transition-all duration-300">
                                    Register Now
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sponsorship Opportunities -->
                    <div class="group relative animate-fade-in-up" style="animation-delay: 0.3s">
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-orange-500 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                        <div class="relative bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-2xl transform group-hover:scale-105 transition-all duration-500 cursor-pointer" onclick="openForm('sponsorship')">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-handshake text-2xl text-white"></i>
                                </div>
                                <h5 class="text-2xl font-bold text-white mb-4">Sponsorship Opportunities</h5>
                                <p class="text-gray-300 mb-6">Explore sponsorship packages and brand partnership options.</p>
                                <button class="w-full bg-gradient-to-r from-red-500 to-orange-500 text-white font-semibold py-3 rounded-xl hover:from-red-600 hover:to-orange-600 transition-all duration-300">
                                    Learn More
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sponsors Section -->
    <section id="sponsors" class="py-20 md:py-32 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-transparent to-purple-500/10"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-20">
                <h3 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-blue-400 mb-8 animate-fade-in-down">
                    Sponsorship Opportunities
                </h3>
                <p class="text-xl text-gray-300 mb-4 animate-fade-in-up">Partner with Excellence</p>
                <div class="w-32 h-1 bg-gradient-to-r from-yellow-400 to-blue-400 mx-auto rounded-full animate-fade-in-up"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-7xl mx-auto">
                <div class="group relative animate-fade-in-up" style="animation-delay: 0.1s">
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                    <div class="relative bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500">
                        <div class="mb-6">
                            <h4 class="text-2xl font-bold text-white mb-2">Primary Sponsor A (Le Mans)</h4>
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-xl font-bold text-yellow-400">₦10,000,000+</p>
                                <span class="text-sm text-blue-400 font-medium">70% Brand Exposure</span>
                            </div>
                        </div>
                        <div class="space-y-3 mb-8">
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Access to unique promotional opportunities</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Branded Merch</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Direct customer engagement throughout the events</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Use of the Show car/Bike</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Dedicated Social Media Post</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Targeted Digital media outreach</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="group relative animate-fade-in-up" style="animation-delay: 0.2s">
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                    <div class="relative bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500">
                        <div class="mb-6">
                            <h4 class="text-2xl font-bold text-white mb-2">Primary Sponsor B (F1)</h4>
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-xl font-bold text-yellow-400">₦5,000,000</p>
                                <span class="text-sm text-blue-400 font-medium">30% Brand Exposure</span>
                            </div>
                        </div>
                        <div class="space-y-3 mb-8">
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Access to unique promotional opportunities</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Branded Merch</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Direct customer engagement throughout the events</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Use of the Show car/Bike</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Dedicated Social Media Post</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-orange-500 to-red-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Targeted Digital media outreach</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="group relative animate-fade-in-up" style="animation-delay: 0.3s">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-purple-500 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                    <div class="relative bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500">
                        <div class="mb-6">
                            <h4 class="text-2xl font-bold text-white mb-2">Associate Partners (Dakar)</h4>
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-xl font-bold text-yellow-400">₦5,000,000 or Product/Service Value</p>
                                <span class="text-sm text-blue-400 font-medium">70% Brand Exposure</span>
                            </div>
                        </div>
                        <div class="space-y-3 mb-8">
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-red-500 to-purple-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Access to unique promotional opportunities</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-red-500 to-purple-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Branded Merch</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-red-500 to-purple-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Direct customer engagement throughout the events</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-red-500 to-purple-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Targeted media exposure across multiple channels</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="group relative animate-fade-in-up" style="animation-delay: 0.4s">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-blue-500 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                    <div class="relative bg-gray-900/80 backdrop-blur-xl rounded-2xl p-8 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500">
                        <div class="mb-6">
                            <h4 class="text-2xl font-bold text-white mb-2">Associate Partners (Touring)</h4>
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-xl font-bold text-yellow-400">₦3,000,000 or Product/Service Value</p>
                                <span class="text-sm text-blue-400 font-medium">30% Brand Exposure</span>
                            </div>
                        </div>
                        <div class="space-y-3 mb-8">
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Access to unique promotional opportunities</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Direct customer engagement throughout the events</span>
                            </div>
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                <span class="text-gray-300 text-sm">Targeted media exposure across multiple channels</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-center mt-12">
                <button onclick="scrollToSection('contact')" class="w-full max-w-xs py-4 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-bold rounded-xl transform hover:scale-105 transition-all duration-300 group-hover:shadow-lg group-hover:shadow-blue-500/25">
                    Contact Us for Sponsorship
                </button>
            </div>
        </div>
    </section>

    <!-- News Brief Section -->
    <section id="news-brief" class="py-20 md:py-32 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-green-900 via-emerald-800 to-teal-900"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-green-500/20 via-transparent to-teal-500/20"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-20">
                <h3 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-teal-400 mb-8 animate-fade-in-down">
                    Stay Updated
                </h3>
                <p class="text-xl text-gray-200 mb-4 animate-fade-in-up">Subscribe to Krosskhana's exclusive ignition briefings and gain priority entry to:</p>
                <div class="w-32 h-1 bg-gradient-to-r from-green-400 to-teal-400 mx-auto rounded-full animate-fade-in-up"></div>
            </div>

            <div class="max-w-2xl mx-auto animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20">
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-gradient-to-r from-green-500 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-newspaper text-white text-3xl"></i>
                        </div>
                        <h4 class="text-3xl font-bold text-white mb-4">News Brief Sign-up</h4>
                        <div class="space-y-3 text-gray-300 text-lg leading-relaxed text-left">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-green-500 to-teal-500 rounded-full"></div>
                                <span>High-voltage event reveals before public announcement</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-green-500 to-teal-500 rounded-full"></div>
                                <span>Behind-the-velvet-rope innovation previews</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-green-500 to-teal-500 rounded-full"></div>
                                <span>Curated motor sport culture intelligence</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-gradient-to-r from-green-500 to-teal-500 rounded-full"></div>
                                <span>VIP experience opportunities</span>
                            </div>
                        </div>
                    </div>

                    <form id="news-brief-form" class="space-y-6">
                        <div>
                            <label for="news-name" class="block text-gray-300 text-lg font-semibold mb-3">Name</label>
                            <input
                                type="text"
                                id="news-name"
                                name="name"
                                class="w-full py-4 px-5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                                placeholder="Your Full Name"
                                required
                            />
                        </div>
                        <div>
                            <label for="news-email" class="block text-gray-300 text-lg font-semibold mb-3">Email Address</label>
                            <input
                                type="email"
                                id="news-email"
                                name="email"
                                class="w-full py-4 px-5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300"
                                placeholder="your.email@example.com"
                                required
                            />
                        </div>
                        <div class="flex justify-center">
                            <button
                                type="submit"
                                class="relative px-12 py-4 bg-gradient-to-r from-green-500 to-teal-500 text-white font-bold text-xl uppercase tracking-wider rounded-full shadow-2xl transform hover:scale-105 transition-all duration-300 group overflow-hidden"
                            >
                                <span class="relative z-10">UNLOCK PREMIUM UPDATES</span>
                                <div class="absolute inset-0 bg-white/20 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 pt-6 border-t border-white/20">
                        <div class="flex items-center justify-center space-x-4 text-sm text-gray-300">
                            <i class="fas fa-shield-alt text-green-400"></i>
                            <span>We respect your privacy. Unsubscribe at any time.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Footer -->
    <footer class="relative py-16 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-red-500/10 via-transparent to-orange-500/10"></div>
        
        <div class="container mx-auto text-center px-4 relative z-10">
            <div class="mb-8">
                <img src="assets/images/krosskhana.png" alt="Krosskhana Logo" class="h-16 w-auto mx-auto mb-4" />
                <div class="w-32 h-1 bg-gradient-to-r from-red-500 to-orange-500 mx-auto rounded-full"></div>
            </div>
            
            <p class="text-lg text-gray-300 mb-4">&copy; <?php echo date('Y'); ?> Krosskhana. All rights reserved.</p>
            <p class="text-sm text-gray-400 mb-8">Designed with passion for automotive culture, arts, music & fashion.</p>
            
            <!-- Social Media Links -->
            <div class="flex justify-center gap-6 mb-8">
                <a href="https://www.tiktok.com/@krosskhana" target="_blank" rel="noopener noreferrer" class="w-14 h-14 flex items-center justify-center rounded-full bg-gradient-to-r from-black to-gray-800 shadow-lg hover:scale-110 transition-transform duration-300">
                    <i class="fab fa-tiktok text-white text-2xl"></i>
                </a>
                <a href="https://www.instagram.com/krosskhana/" target="_blank" rel="noopener noreferrer" class="w-14 h-14 flex items-center justify-center rounded-full bg-gradient-to-r from-pink-500 to-yellow-500 shadow-lg hover:scale-110 transition-transform duration-300">
                    <i class="fab fa-instagram text-white text-2xl"></i>
                </a>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 z-50 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white w-14 h-14 rounded-full shadow-2xl transform hover:scale-110 transition-all duration-300 flex items-center justify-center animate-fade-in group backdrop-blur-xl border border-white/20 hidden" aria-label="Back to top">
        <svg class="w-6 h-6 transform group-hover:-translate-y-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
        <div class="absolute inset-0 bg-white/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </button>

    </div> <!-- End of main-app -->

    <!-- Notification Modal -->
    <div id="notification-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full text-center border-2 border-gray-200">
            <div id="modal-icon" class="mb-4 text-3xl"></div>
            <div id="modal-message" class="text-lg font-semibold mb-6 text-gray-800"></div>
            <button id="modal-close" class="px-6 py-2 bg-gradient-to-r from-green-500 to-teal-500 text-white font-bold rounded-full shadow transition">
                OK
            </button>
        </div>
    </div>

    <!-- Corporate Partnership Form Modal -->
    <div id="corporate-form-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 hidden">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-800">Connect with Krosskhana - Corporate Partnership</h2>
                <button onclick="closeCorporateForm()" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">
                    ×
                </button>
            </div>

            <form id="corporate-partnership-form" class="space-y-6">
                <!-- Initial Selection -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Initial Selection</h3>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="brand_collaboration" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">I represent a brand seeking strategic collaboration</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vendor_opportunities" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">I'm exploring vendor/exhibitor opportunities</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="media_representative" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">I'm a media representative</span>
                        </label>
                    </div>
                </div>

                <!-- Company/Contact Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Company/Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Company/Organization</label>
                            <input type="text" name="company" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Contact Person</label>
                            <input type="text" name="contact_person" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Role/Title</label>
                            <input type="text" name="role_title" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Email</label>
                            <input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Phone</label>
                            <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Website/Social</label>
                            <input type="url" name="website_social" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Primary Interest -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Primary Interest</h3>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="event_integration" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Event Integration</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="product_showcase" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Product Showcase</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="brand_activation" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Brand Activation</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="content_collaboration" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Content Collaboration</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="rd_demonstration" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">R&D Demonstration</span>
                        </label>
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="other_interest" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Other:</span>
                            <input type="text" name="other_interest_text" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Please specify">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center pt-6">
                    <button type="submit" class="px-8 py-4 bg-gradient-to-r from-red-600 to-orange-600 text-white font-bold rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
                        Connect with Krosskhana
                    </button>
                </div>
            </form>
                 </div>
     </div>

    <!-- Racer Registration Form Modal -->
    <div id="racer-form-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 hidden">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-800">Connect with Krosskhana - Join the Grid</h2>
                <button onclick="closeRacerForm()" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">
                    ×
                </button>
            </div>

            <form id="racer-registration-form" class="space-y-6">
                <!-- Participation Type Selection -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Participation Type Selection</h3>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="autocross_competitor" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Autocross Competitor</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="drifting_challenge" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Drifting Challenge</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="exhibition_driver" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Exhibition Driver</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="safety_training" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Safety Training</span>
                        </label>
                    </div>
                </div>

                <!-- Personal Contact Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Personal Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Full Name</label>
                            <input type="text" name="full_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Racing Alias</label>
                            <input type="text" name="racing_alias" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Email</label>
                            <input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Phone</label>
                            <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-medium mb-2">Location</label>
                            <input type="text" name="location" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Vehicle Details -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Vehicle Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Make</label>
                            <input type="text" name="make" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Model</label>
                            <input type="text" name="model" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Year</label>
                            <input type="number" name="year" min="1900" max="2030" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Modifications</label>
                        <textarea name="modifications" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none" placeholder="Describe any modifications to your vehicle"></textarea>
                    </div>
                </div>

                <!-- Racing Experience -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Racing Experience</h3>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="novice" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Novice</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="intermediate" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Intermediate</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="semi_pro" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Semi-Pro</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="professional" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Professional</span>
                        </label>
                    </div>
                </div>

                <!-- Previous Series -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Previous Series</label>
                    <input type="text" name="previous_series" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="List any previous racing series or competitions">
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center pt-6">
                    <button type="submit" class="px-8 py-4 bg-gradient-to-r from-red-600 to-orange-600 text-white font-bold rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
                        Join the Grid
                    </button>
                </div>
            </form>
                 </div>
     </div>

    <!-- Sponsorship Form Modal -->
    <div id="sponsorship-form-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 hidden">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-800">Connect with Krosskhana - Fuel the Revolution</h2>
                <button onclick="closeSponsorshipForm()" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">
                    ×
                </button>
            </div>

            <form id="sponsorship-form" class="space-y-6">
                <!-- Sponsorship Type Selection -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Sponsorship Type Selection</h3>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="title_sponsorship" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Title Sponsorship</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="event_series_sponsor" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Event Series Sponsor</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="innovation_partner" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Innovation Partner</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="community_platform_sponsor" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Community Platform Sponsor</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="safety_initiative_partner" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Safety Initiative Partner</span>
                        </label>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Organization</label>
                            <input type="text" name="organization" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Contact Person</label>
                            <input type="text" name="contact_person" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Title</label>
                            <input type="text" name="title" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Email</label>
                            <input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Phone</label>
                            <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Website</label>
                            <input type="url" name="website" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Previous Motorsport Sponsorship Experience -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Previous Motorsport Sponsorship Experience</h3>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="none_experience" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">None</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="local_experience" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Local</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="national_experience" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">National</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="international_experience" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">International</span>
                        </label>
                    </div>
                </div>

                <!-- Target Activation Type -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Target Activation Type</h3>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="track_branding" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Track Branding</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="digital_integration" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Digital Integration</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="vip_experiences" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">VIP Experiences</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="product_placement" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Product Placement</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="tech_showcase" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Tech Showcase</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="safety_campaign" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">Safety Campaign</span>
                        </label>
                    </div>
                </div>

                <!-- Investment Range -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Investment Range</h3>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="investment_5k_15k" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">$5K-15K</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="investment_15k_50k" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">$15K-50K</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="investment_50k_100k" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">$50K-100K</span>
                        </label>
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="investment_100k_plus" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="text-gray-700">$100K+</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center pt-6">
                    <button type="submit" class="px-8 py-4 bg-gradient-to-r from-red-600 to-orange-600 text-white font-bold rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
                        Fuel the Revolution
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

