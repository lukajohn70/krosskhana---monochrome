<?php
require_once 'includes/config.php';

// Fetch active events from database
$pastEvents = [];
$upcomingEvents = [];

try {
    $eventsPdo = getDBConnection();
    if ($eventsPdo) {
        // Ensure events table exists (self-healing)
        $eventsPdo->exec("
        CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            event_type ENUM('past', 'upcoming') NOT NULL DEFAULT 'upcoming',
            badge_text VARCHAR(100) DEFAULT NULL,
            event_date_text VARCHAR(100) NOT NULL,
            event_date DATE DEFAULT NULL,
            description TEXT NOT NULL,
            image_url VARCHAR(500) DEFAULT NULL,
            gradient_theme VARCHAR(50) DEFAULT 'from-white to-gray-300',
            button_text VARCHAR(100) DEFAULT 'View Details',
            button_link VARCHAR(500) DEFAULT NULL,
            sort_order INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_event_type (event_type),
            INDEX idx_is_active (is_active),
            INDEX idx_sort_order (sort_order),
            INDEX idx_event_date (event_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $eventsStmt = $eventsPdo->query("SELECT * FROM events WHERE is_active = 1 ORDER BY sort_order ASC, event_date DESC, id DESC");
        if ($eventsStmt) {
            $allDbEvents = $eventsStmt->fetchAll();
            foreach ($allDbEvents as $ev) {
                if ($ev['event_type'] === 'past') {
                    $pastEvents[] = $ev;
                } else {
                    $upcomingEvents[] = $ev;
                }
            }
        }
    }
} catch (Exception $e) {
    error_log('Error loading events in index.php: ' . $e->getMessage());
}

// Fetch active explore items
$exploreSlide1 = [];
$exploreSlide2 = [];
try {
    if ($eventsPdo) {
        $exploreStmt = $eventsPdo->query("SELECT * FROM explore_items WHERE is_active = 1 ORDER BY slide_number ASC, sort_order ASC, id ASC");
        if ($exploreStmt) {
            $allExplore = $exploreStmt->fetchAll();
            foreach ($allExplore as $item) {
                if ($item['slide_number'] == 2) {
                    $exploreSlide2[] = $item;
                } else {
                    $exploreSlide1[] = $item;
                }
            }
        }
    }
} catch (Exception $e) {
    error_log('Error loading explore items: ' . $e->getMessage());
}

// Fetch active team members
$teamMembers = [];
try {
    if ($eventsPdo) {
        $teamStmt = $eventsPdo->query("SELECT * FROM team_members WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
        if ($teamStmt) {
            $teamMembers = $teamStmt->fetchAll();
        }
    }
} catch (Exception $e) {
    error_log('Error loading team members: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krosskhana Motorsports - Automotive Lifestyle Event</title>
    <meta name="description" content="Krosskhana is an automotive lifestyle company that seamlessly blends motor sport with diverse forms of entertainment, including music, fashion, and cultural exhibitions.">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/logos/Krosskhana-05.png">
    <link rel="shortcut icon" type="image/png" href="assets/logos/Krosskhana-05.png">
    <link rel="apple-touch-icon" href="assets/logos/Krosskhana-05.png">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom Fonts -->
    <style>
        @font-face {
            font-family: 'Orbitron';
            src: url('assets/fonts/Orbitron-VariableFont_wght.ttf') format('truetype');
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Orbitron';
            src: url('assets/fonts/Orbitron-Regular.ttf') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Orbitron';
            src: url('assets/fonts/Orbitron-Bold.ttf') format('truetype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Orbitron';
            src: url('assets/fonts/Orbitron-Black.ttf') format('truetype');
            font-weight: 900;
            font-style: normal;
            font-display: swap;
        }
    </style>

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
                        orbitron: ['Orbitron', 'monospace'],
                    },
                    colors: {
                        'monochrome': { // Monochrome color palette
                            50: '#fafafa',
                            100: '#f5f5f5',
                            200: '#e5e5e5',
                            300: '#d4d4d4',
                            400: '#a3a3a3',
                            500: '#737373',
                            600: '#525252',
                            700: '#404040',
                            800: '#262626',
                            900: '#171717',
                            950: '#0a0a0a',
                        },
                        'gray': { // Enhanced gray scale for monochrome theme
                            50: '#fafafa',
                            100: '#f5f5f5',
                            200: '#e5e5e5',
                            300: '#d4d4d4',
                            400: '#a3a3a3',
                            500: '#737373',
                            600: '#525252',
                            700: '#404040',
                            800: '#262626',
                            900: '#171717',
                            950: '#0a0a0a',
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
                            '0%, 100%': { boxShadow: '0 0 20px rgba(255, 255, 255, 0.3)' },
                            '50%': { boxShadow: '0 0 40px rgba(255, 255, 255, 0.6)' },
                        },

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
            background-color: #ffffff; /* Use white for monochrome theme */
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
            background-color: #ffffff;
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
    <div id="splash-screen" class="fixed inset-0 z-[99999] bg-black opacity-0 transition-opacity duration-1000 ease-in-out">
        <!-- Video Background with Zoom Effect -->
        <div class="absolute inset-0 overflow-hidden">
            <video id="splash-video" class="absolute inset-0 w-full h-full object-cover scale-150 transition-transform duration-10000 ease-out" autoplay muted playsinline>
                <source src="assets/videos/2.mp4" type="video/mp4">
            </video>
        </div>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/30"></div>

        <!-- Skip Button -->
        <button id="skip-splash" class="absolute top-6 right-6 z-10 bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-full font-semibold hover:bg-white/30 transition-all duration-300 opacity-0 transform translate-y-4">
            Skip
        </button>

        <!-- Progress Bar Container -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 w-72 md:w-80 z-10 opacity-0 transform translate-y-4 px-4">
            <!-- Progress Bar -->
            <div class="w-full bg-white/20 backdrop-blur-sm rounded-full h-2 mb-4 relative">
                <div id="splash-progress" class="bg-gradient-to-r from-white to-gray-300 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                <!-- Overlay start indicator (subtle white line) -->
                <div id="overlay-indicator" class="absolute top-0 w-0.5 h-2 bg-white/60 rounded-full opacity-0 transition-opacity duration-300" style="right: 5%"></div>
            </div>
            <!-- Loading Text -->
            <p class="text-white text-center font-medium">Loading Experience...</p>
        </div>
    </div>

    <!-- Navigation Bar - Modern Glassmorphism (Moved outside main-app for better visibility) -->
    <nav class="bg-gray-800 border-b border-gray-700 p-4 fixed w-full z-[9999] top-0 transition-all duration-500 shadow-2xl">
        <div class="container mx-auto flex justify-between items-center py-2 px-4 md:px-0">
            <!-- Logo -->
            <div class="flex items-center space-x-4">
                <div class="relative group cursor-pointer" onclick="scrollToSection('home')">
                    <img src="assets/logos/nav-bar-footer-logo.jpg" alt="Krosskhana Logo" class="h-14 w-auto transition-transform duration-300 group-hover:scale-110" />
                </div>
            </div>



            <!-- Desktop Navigation -->
            <ul class="hidden md:flex space-x-4 text-white text-sm font-medium uppercase tracking-wider">
                <li>
                    <button onclick="scrollToSection('home')" class="nav-link relative px-3 py-2 rounded-full transition-all duration-300 group bg-gray-600 hover:bg-gray-500 text-white shadow-lg">
                        <span class="relative z-10">Home</span>
                    </button>
                </li>
                <li>
                    <button onclick="scrollToSection('about')" class="nav-link relative px-3 py-2 rounded-full transition-all duration-300 group text-gray-300 hover:bg-gray-600 hover:text-white">
                        <span class="relative z-10">About</span>
                    </button>
                </li>
                <li>
                    <button onclick="scrollToSection('mission')" class="nav-link relative px-3 py-2 rounded-full transition-all duration-300 group text-gray-300 hover:bg-gray-600 hover:text-white">
                        <span class="relative z-10">Mission</span>
                    </button>
                </li>
                <li>
                    <button onclick="scrollToSection('explore')" class="nav-link relative px-3 py-2 rounded-full transition-all duration-300 group text-gray-300 hover:bg-gray-600 hover:text-white">
                        <span class="relative z-10">Explore</span>
                    </button>
                </li>
                <li>
                    <button onclick="scrollToSection('events')" class="nav-link relative px-3 py-2 rounded-full transition-all duration-300 group text-gray-300 hover:bg-gray-600 hover:text-white">
                        <span class="relative z-10">Events</span>
                    </button>
                </li>
                <li>
                    <button onclick="scrollToSection('team')" class="nav-link relative px-3 py-2 rounded-full transition-all duration-300 group text-gray-300 hover:bg-gray-600 hover:text-white">
                        <span class="relative z-10">Team</span>
                    </button>
                </li>
                <li>
                    <button onclick="scrollToSection('participate')" class="nav-link relative px-3 py-2 rounded-full transition-all duration-300 group text-gray-300 hover:bg-gray-600 hover:text-white">
                        <span class="relative z-10">Join</span>
                    </button>
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
            <div class="bg-gray-700/90 backdrop-blur-xl border-t border-gray-600">
                <ul class="flex flex-col space-y-2 p-4 text-white text-sm font-medium uppercase tracking-wider">
                    <li>
                        <button onclick="scrollToSection('home')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group bg-gray-600 hover:bg-gray-500 text-white shadow-lg">
                            <span class="relative z-10">Home</span>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('about')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300 hover:bg-gray-600 hover:text-white">
                            <span class="relative z-10">About</span>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('mission')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300 hover:bg-gray-600 hover:text-white">
                            <span class="relative z-10">Mission</span>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('explore')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300 hover:bg-gray-600 hover:text-white">
                            <span class="relative z-10">Explore</span>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('events')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300 hover:bg-gray-600 hover:text-white">
                            <span class="relative z-10">Events</span>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('team')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300 hover:bg-gray-600 hover:text-white">
                            <span class="relative z-10">Team</span>
                        </button>
                    </li>
                    <li>
                        <button onclick="scrollToSection('participate')" class="mobile-nav-link relative w-full text-left px-4 py-3 rounded-lg transition-all duration-300 group text-gray-300 hover:bg-gray-600 hover:text-white">
                            <span class="relative z-10">Join</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Application -->
    <div id="main-app" class="font-poppins min-h-screen transition-all duration-1200 opacity-0 transform translate-y-2 hidden">
        <!-- Animated background gradient -->
        <div class="fixed inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900 z-[-2]"></div>
        <div class="fixed inset-0 bg-gradient-to-r from-white/5 via-transparent to-gray-300/5 z-[-1]"></div>

        <!-- Floating particles effect -->
        <div class="fixed inset-0 z-[-1] overflow-hidden">
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 10%; top: 20%; animation-delay: 0s; animation-duration: 2.5s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 20%; top: 80%; animation-delay: 1s; animation-duration: 3s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 30%; top: 40%; animation-delay: 2s; animation-duration: 2.8s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 40%; top: 60%; animation-delay: 0.5s; animation-duration: 3.2s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 50%; top: 10%; animation-delay: 1.5s; animation-duration: 2.7s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 60%; top: 90%; animation-delay: 0.8s; animation-duration: 3.1s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 70%; top: 30%; animation-delay: 2.3s; animation-duration: 2.9s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 80%; top: 70%; animation-delay: 1.2s; animation-duration: 3.3s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 90%; top: 50%; animation-delay: 0.3s; animation-duration: 2.6s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 15%; top: 85%; animation-delay: 1.8s; animation-duration: 3.4s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 25%; top: 15%; animation-delay: 0.7s; animation-duration: 2.4s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 35%; top: 75%; animation-delay: 2.1s; animation-duration: 3.5s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 45%; top: 25%; animation-delay: 1.4s; animation-duration: 2.3s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 55%; top: 95%; animation-delay: 0.9s; animation-duration: 3.6s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 65%; top: 5%; animation-delay: 2.7s; animation-duration: 2.2s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 75%; top: 55%; animation-delay: 1.6s; animation-duration: 3.7s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 85%; top: 35%; animation-delay: 0.4s; animation-duration: 2.1s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 95%; top: 65%; animation-delay: 2.9s; animation-duration: 3.8s;"></div>
            <div class="absolute w-2 h-2 bg-white/30 rounded-full animate-pulse" style="left: 5%; top: 45%; animation-delay: 1.1s; animation-duration: 2.8s;"></div>
        </div>



    <!-- Hero Section - Ultra Modern -->
    <section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden pt-20">
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
            <div class="w-full h-full bg-gradient-to-br from-black/80 via-black/60 to-gray-900/40"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/50 via-transparent to-black/50"></div>
        </div>

        <!-- Floating geometric shapes -->
        <div class="absolute inset-0 z-5 overflow-hidden pointer-events-none">
            <div
                class="absolute top-20 left-20 w-32 h-32 border border-white/30 rotate-45 animate-spin-slow"
                style="animation-duration: 20s;"
            ></div>
            <div
                class="absolute bottom-20 right-20 w-24 h-24 border border-gray-300/30 rounded-full animate-pulse"
                style="animation-delay: 2s;"
            ></div>
            <div
                class="absolute top-1/2 left-10 w-16 h-16 bg-gradient-to-r from-white to-gray-300 rounded-full opacity-20 animate-bounce-slow"
                style="animation-delay: 1s;"
            ></div>
        </div>

        <div class="container mx-auto text-center relative z-20 px-4">
            <!-- Main title -->
            <div class="relative mb-8">
                <div class="flex flex-col items-center justify-center animate-fade-in-down">
                    <img src="assets/logos/hero-section-logo.png" alt="Krosskhana Logo" class="h-56 md:h-80 lg:h-[28rem] w-auto mb-0 drop-shadow-2xl animate-fade-in-down" />
                    <h3 class="text-4xl md:text-6xl font-bold tracking-wider uppercase text-transparent bg-clip-text bg-gradient-to-r from-gray-300 to-gray-500 animate-fade-in-down font-orbitron -mt-16 md:-mt-20 lg:-mt-24" style="animation-delay: 0.3s">
                        MOTORSPORTS
                    </h3>
                </div>
                <div class="absolute inset-0 bg-gradient-to-r from-white to-gray-300 blur-3xl opacity-20 animate-pulse"></div>
            </div>

            <!-- Tagline -->
            <div class="relative mb-12">
                <p class="text-2xl md:text-4xl font-light text-gray-200 min-h-[3rem] animate-fade-in-up" style="animation-delay: 0.6s">
                    Fueling Culture. Driving Stories. Partnering for Impact.
                </p>
                <div class="absolute inset-0 bg-gradient-to-r from-white/20 to-gray-300/20 blur-xl"></div>
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
        <div class="absolute inset-0 bg-gradient-to-r from-gray-500/5 via-transparent to-gray-700/5"></div>

        <!-- Floating elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 right-20 w-64 h-64 bg-gradient-to-r from-gray-500/10 to-gray-700/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 left-20 w-48 h-48 bg-gradient-to-r from-gray-700/10 to-gray-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-20">
                <h3 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-gray-600 to-gray-800 mb-8 animate-fade-in-down font-orbitron">
                    About Us
                </h3>
                <div class="w-32 h-1 bg-gradient-to-r from-gray-500 to-gray-700 mx-auto rounded-full animate-fade-in-up"></div>
            </div>

            <div class="flex flex-col lg:flex-row items-center gap-16">
                <!-- Modern image container -->
                <div class="lg:w-1/2 animate-fade-in-left">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-gradient-to-r from-gray-500 to-gray-700 rounded-2xl blur-xl opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
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
                            <h4 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-gray-600 to-gray-800 mb-4 font-orbitron">KROSSKHANA MOTORSPORTS: IGNITING THE FUTURE OF AUTOMOTIVE CULTURE</h4>
                            <p class="relative">
                                <span class="absolute -left-4 top-0 w-1 h-full bg-gradient-to-b from-gray-500 to-gray-700 rounded-full"></span>
                                <span class="font-bold text-gray-700">We are revolution on wheels.</span> <span class="text-gray-600 font-semibold">Krosskhana</span> transcends traditional motorsports, fusing high-octane racing thrills with the pulse of music, fashion, and art.
                            </p>
                            <p>
                                We evolved into Abuja's premier automotive lifestyle experience – hosting four consecutive sold-out events (<span class="text-gray-600 font-bold">2,000+ attendees</span>) and the exclusive <span class="font-bold text-gray-700">Fast X premiere</span>.
                            </p>
                            <p>
                                We build <span class="text-gray-600 font-semibold">passionate communities</span> and redefine what <span class="text-gray-700 font-semibold">automotive culture</span> means.
                            </p>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section id="mission" class="py-8 md:py-20 lg:py-32 relative overflow-hidden">
        <!-- Background with gradient and effects -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-gray-500/5 via-transparent to-gray-700/5"></div>

        <!-- Floating elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 left-20 w-64 h-64 bg-gradient-to-r from-gray-500/10 to-gray-700/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-48 h-48 bg-gradient-to-r from-gray-700/10 to-gray-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-8 md:mb-20">
                <h3 class="text-3xl md:text-5xl lg:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-gray-600 to-gray-800 mb-4 md:mb-8 animate-fade-in-down font-orbitron">
                    WHAT FUELS OUR MISSION
                </h3>
                <div class="w-24 md:w-32 h-1 bg-gradient-to-r from-gray-500 to-gray-700 mx-auto rounded-full animate-fade-in-up"></div>
            </div>

            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    <!-- Safety First -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-4 md:p-6 shadow-2xl border border-white/20 animate-fade-in-up">
                        <div class="flex items-center mb-2 md:mb-3">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-r from-gray-500 to-gray-700 rounded-full flex items-center justify-center mr-2 md:mr-3">
                                <i class="fas fa-shield-alt text-white text-base md:text-lg"></i>
                            </div>
                            <h4 class="text-base md:text-lg font-bold text-gray-800">Safety First</h4>
                        </div>
                        <div class="space-y-1 md:space-y-2 text-gray-700 text-xs md:text-sm">
                            <p>Professional-tier safety standards adapted for community access.</p>
                            <p>Meticulous design, expert oversight & responsible driving advocacy.</p>
                            <p class="text-xs italic text-gray-600">(Track-certified barriers • Mandatory briefings • Onsite medics • Gear checks)</p>
                            <p class="font-bold text-gray-700">Your safety is paramount.</p>
                        </div>
                    </div>

                    <!-- Community-Driven -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-4 md:p-6 shadow-2xl border border-white/20 animate-fade-in-up">
                        <div class="flex items-center mb-2 md:mb-3">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-r from-gray-700 to-gray-500 rounded-full flex items-center justify-center mr-2 md:mr-3">
                                <i class="fas fa-users text-white text-base md:text-lg"></i>
                            </div>
                            <h4 class="text-base md:text-lg font-bold text-gray-800">Community-Driven</h4>
                        </div>
                        <div class="space-y-1 md:space-y-2 text-gray-700 text-xs md:text-sm">
                            <p>Uniting enthusiasts, artists & creators.</p>
                        </div>
                    </div>

                    <!-- Motor Sports-Centered -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-4 md:p-6 shadow-2xl border border-white/20 animate-fade-in-up">
                        <div class="flex items-center mb-2 md:mb-3">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-r from-gray-500 to-gray-700 rounded-full flex items-center justify-center mr-2 md:mr-3">
                                <i class="fas fa-car-side text-white text-base md:text-lg"></i>
                            </div>
                            <h4 class="text-base md:text-lg font-bold text-gray-800">Motorsports-Centered</h4>
                        </div>
                        <div class="space-y-1 md:space-y-2 text-gray-700 text-xs md:text-sm">
                            <p>Authentic racing & precision driving at our core.</p>
                        </div>
                    </div>

                    <!-- Culture-Infused -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-4 md:p-6 shadow-2xl border border-white/20 animate-fade-in-up">
                        <div class="flex items-center mb-2 md:mb-3">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-r from-gray-700 to-gray-500 rounded-full flex items-center justify-center mr-2 md:mr-3">
                                <i class="fas fa-palette text-white text-base md:text-lg"></i>
                            </div>
                            <h4 class="text-base md:text-lg font-bold text-gray-800">Culture-Infused</h4>
                        </div>
                        <div class="space-y-1 md:space-y-2 text-gray-700 text-xs md:text-sm">
                            <p>Music, fashion & art fused with automotive passion.</p>
                        </div>
                    </div>

                    <!-- Innovation-Led -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-4 md:p-6 shadow-2xl border border-white/20 animate-fade-in-up">
                        <div class="flex items-center mb-2 md:mb-3">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-r from-gray-500 to-gray-700 rounded-full flex items-center justify-center mr-2 md:mr-3">
                                <i class="fas fa-lightbulb text-white text-base md:text-lg"></i>
                            </div>
                            <h4 class="text-base md:text-lg font-bold text-gray-800">Innovation-Led</h4>
                        </div>
                        <div class="space-y-1 md:space-y-2 text-gray-700 text-xs md:text-sm">
                            <p>Cutting-edge tech & groundbreaking experiences.</p>
                        </div>
                    </div>

                    <!-- Excellence Focused -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-4 md:p-6 shadow-2xl border border-white/20 animate-fade-in-up">
                        <div class="flex items-center mb-2 md:mb-3">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-r from-gray-700 to-gray-500 rounded-full flex items-center justify-center mr-2 md:mr-3">
                                <i class="fas fa-star text-white text-base md:text-lg"></i>
                            </div>
                            <h4 class="text-base md:text-lg font-bold text-gray-800">Excellence Focused</h4>
                        </div>
                        <div class="space-y-1 md:space-y-2 text-gray-700 text-xs md:text-sm">
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
        <div class="absolute inset-0 bg-gradient-to-r from-white/5 via-transparent to-gray-300/5"></div>

        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 left-1/4 w-80 h-80 bg-gray-300/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-20">
                <h3 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-300 mb-8 animate-fade-in-down font-orbitron">
                    EXPLORE THE WORLD OF KROSSKHANA
                </h3>
                <div class="w-32 h-1 bg-gradient-to-r from-white to-gray-300 mx-auto rounded-full animate-fade-in-up"></div>
            </div>

            <!-- Carousel Container -->
            <div class="max-w-6xl mx-auto relative" style="z-index: 1;">
                <!-- Navigation Arrows -->
                <button id="carousel-prev" class="absolute left-4 top-1/2 transform -translate-y-1/2 z-20 w-12 h-12 bg-gray-900/80 backdrop-blur-xl rounded-full border border-gray-700/50 shadow-2xl hover:bg-gray-800/90 transition-all duration-300 flex items-center justify-center group">
                    <i class="fas fa-chevron-left text-white text-xl group-hover:text-gray-300 transition-colors duration-300"></i>
                </button>

                <button id="carousel-next" class="absolute right-4 top-1/2 transform -translate-y-1/2 z-20 w-12 h-12 bg-gray-900/80 backdrop-blur-xl rounded-full border border-gray-700/50 shadow-2xl hover:bg-gray-800/90 transition-all duration-300 flex items-center justify-center group">
                    <i class="fas fa-chevron-right text-white text-xl group-hover:text-gray-300 transition-colors duration-300"></i>
                </button>

                <!-- Carousel Track -->
                <div id="carousel-track" class="flex transition-transform duration-500 ease-in-out">
                    <!-- Slide 1 -->
                    <div class="w-full flex-shrink-0">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4">
                            <?php 
                            $displaySlide1 = !empty($exploreSlide1) ? $exploreSlide1 : [
                                ['title' => 'Autocross & Drifting Competitions', 'description' => 'Experience the raw thrill and precision of authentic racing.', 'modal_title' => 'Racing Excellence', 'modal_description' => 'Experience the raw thrill and precision of authentic racing competitions.', 'modal_image_url' => 'pictures/1.png', 'icon_class' => 'fas fa-car-side', 'gradient_theme' => 'from-white to-gray-300'],
                                ['title' => 'R&D Showcases & Innovations', 'description' => 'Discover cutting-edge tech and future automotive developments.', 'modal_title' => 'Innovation Hub', 'modal_description' => 'Discover cutting-edge technology and future automotive developments.', 'modal_image_url' => 'pictures/2.png', 'icon_class' => 'fas fa-microscope', 'gradient_theme' => 'from-gray-300 to-white'],
                                ['title' => 'Lifestyle Shows & Music Collabs', 'description' => 'Immerse yourself where automotive passion meets electrifying entertainment.', 'modal_title' => 'Entertainment Fusion', 'modal_description' => 'Where automotive passion meets electrifying entertainment experiences.', 'modal_image_url' => 'pictures/3.png', 'icon_class' => 'fas fa-music', 'gradient_theme' => 'from-white to-gray-300']
                            ];
                            foreach ($displaySlide1 as $item): 
                                $cardGrad = !empty($item['gradient_theme']) ? $item['gradient_theme'] : 'from-white to-gray-300';
                                $cardIcon = !empty($item['icon_class']) ? $item['icon_class'] : 'fas fa-car-side';
                                $cardImg = !empty($item['modal_image_url']) ? $item['modal_image_url'] : 'pictures/1.png';
                                $cardModalTitle = !empty($item['modal_title']) ? $item['modal_title'] : $item['title'];
                                $cardModalDesc = !empty($item['modal_description']) ? $item['modal_description'] : $item['description'];
                            ?>
                            <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-white/20 animate-fade-in-up relative">
                                <!-- Modal Hover Effect -->
                                <div class="absolute inset-0 bg-black/90 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-300 z-[99999] rounded-2xl flex items-center justify-center p-4" style="z-index: 99999 !important;">
                                    <div class="text-center transform scale-75 group-hover:scale-100 transition-transform duration-300 w-full">
                                        <!-- Modal Image -->
                                        <div class="w-80 h-80 md:w-96 md:h-96 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl overflow-hidden relative group-hover:scale-110 transition-transform duration-300">
                                            <img src="<?php echo htmlspecialchars($cardImg); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="w-full h-full object-cover">
                                        </div>
                                        <h3 class="text-2xl font-bold text-white mb-2"><?php echo htmlspecialchars($cardModalTitle); ?></h3>
                                        <p class="text-gray-300 text-sm max-w-xs"><?php echo htmlspecialchars($cardModalDesc); ?></p>
                                    </div>
                                </div>

                                <!-- Original Content -->
                                <div class="relative z-10">
                                    <div class="relative mb-4">
                                        <div class="w-16 h-16 bg-gradient-to-r <?php echo htmlspecialchars($cardGrad); ?> rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                            <i class="<?php echo htmlspecialchars($cardIcon); ?> text-black text-2xl"></i>
                                        </div>
                                        <div class="absolute inset-0 bg-gradient-to-r <?php echo htmlspecialchars($cardGrad); ?> rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                                    </div>
                                    <h4 class="text-xl font-bold mb-3 text-white text-center group-hover:text-gray-300 transition-colors duration-300">
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </h4>
                                    <p class="text-gray-300 text-center leading-relaxed text-sm">
                                        <?php echo htmlspecialchars($item['description']); ?>
                                    </p>
                                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r <?php echo htmlspecialchars($cardGrad); ?> rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div class="w-full flex-shrink-0">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4">
                            <?php 
                            $displaySlide2 = !empty($exploreSlide2) ? $exploreSlide2 : [
                                ['title' => 'Fashion & Brand Activations', 'description' => 'Showcase your brand through innovative displays and strategic partnerships.', 'modal_title' => 'Style & Branding', 'modal_description' => 'Showcase your brand through innovative displays and strategic partnerships.', 'modal_image_url' => 'pictures/4.png', 'icon_class' => 'fas fa-tshirt', 'gradient_theme' => 'from-gray-300 to-white'],
                                ['title' => 'Market Engagement & Fan Loyalty', 'description' => 'Build deep connections with our passionate, loyal community.', 'modal_title' => 'Community Love', 'modal_description' => 'Build deep connections with our passionate, loyal community.', 'modal_image_url' => 'pictures/5.png', 'icon_class' => 'fas fa-heart', 'gradient_theme' => 'from-white to-gray-300'],
                                ['title' => 'Exclusive High-End Events', 'description' => 'Experience premium motorsports crafted for discerning enthusiasts.', 'modal_title' => 'Premium Experience', 'modal_description' => 'Experience premium motorsports crafted for discerning enthusiasts.', 'modal_image_url' => 'pictures/6.png', 'icon_class' => 'fas fa-crown', 'gradient_theme' => 'from-gray-300 to-white']
                            ];
                            foreach ($displaySlide2 as $item): 
                                $cardGrad = !empty($item['gradient_theme']) ? $item['gradient_theme'] : 'from-gray-300 to-white';
                                $cardIcon = !empty($item['icon_class']) ? $item['icon_class'] : 'fas fa-tshirt';
                                $cardImg = !empty($item['modal_image_url']) ? $item['modal_image_url'] : 'pictures/4.png';
                                $cardModalTitle = !empty($item['modal_title']) ? $item['modal_title'] : $item['title'];
                                $cardModalDesc = !empty($item['modal_description']) ? $item['modal_description'] : $item['description'];
                            ?>
                            <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl p-6 border border-gray-700/50 shadow-2xl transform group-hover:scale-105 transition-all duration-500 hover:shadow-white/20 animate-fade-in-up relative">
                                <!-- Modal Hover Effect -->
                                <div class="absolute inset-0 bg-black/90 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-300 z-[99999] rounded-2xl flex items-center justify-center p-4" style="z-index: 99999 !important;">
                                    <div class="text-center transform scale-75 group-hover:scale-100 transition-transform duration-300 w-full">
                                        <!-- Modal Image -->
                                        <div class="w-80 h-80 md:w-96 md:h-96 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl overflow-hidden relative group-hover:scale-110 transition-transform duration-300">
                                            <img src="<?php echo htmlspecialchars($cardImg); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="w-full h-full object-cover">
                                        </div>
                                        <h3 class="text-2xl font-bold text-white mb-2"><?php echo htmlspecialchars($cardModalTitle); ?></h3>
                                        <p class="text-gray-300 text-sm max-w-xs"><?php echo htmlspecialchars($cardModalDesc); ?></p>
                                    </div>
                                </div>

                                <!-- Original Content -->
                                <div class="relative z-10">
                                    <div class="relative mb-4">
                                        <div class="w-16 h-16 bg-gradient-to-r <?php echo htmlspecialchars($cardGrad); ?> rounded-2xl flex items-center justify-center mx-auto transform group-hover:rotate-12 transition-transform duration-500">
                                            <i class="<?php echo htmlspecialchars($cardIcon); ?> text-black text-2xl"></i>
                                        </div>
                                        <div class="absolute inset-0 bg-gradient-to-r <?php echo htmlspecialchars($cardGrad); ?> rounded-2xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity duration-500"></div>
                                    </div>
                                    <h4 class="text-xl font-bold mb-3 text-white text-center group-hover:text-gray-300 transition-colors duration-300">
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </h4>
                                    <p class="text-gray-300 text-center leading-relaxed text-sm">
                                        <?php echo htmlspecialchars($item['description']); ?>
                                    </p>
                                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-1 bg-gradient-to-r <?php echo htmlspecialchars($cardGrad); ?> rounded-full group-hover:w-1/2 transition-all duration-500"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Carousel Indicators -->
                <div class="flex justify-center mt-8 space-x-3">
                    <button class="carousel-indicator w-3 h-3 bg-white/30 rounded-full transition-all duration-300 hover:bg-white/50 active" data-slide="0"></button>
                    <button class="carousel-indicator w-3 h-3 bg-white/30 rounded-full transition-all duration-300 hover:bg-white/50" data-slide="1"></button>
                </div>

                <!-- Swipe Notice -->
                <div class="text-center mt-6">
                    <p class="text-gray-400 text-sm flex items-center justify-center">
                        <i class="fas fa-hand-pointer mr-2"></i>
                        Swipe or use arrows to explore more
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Quote Section -->
    <section id="quote" class="py-20 md:py-32 relative overflow-hidden">
        <!-- Background with gradient and effects -->
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-gray-500/5 via-transparent to-gray-700/5"></div>

        <!-- Floating elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-20 right-20 w-64 h-64 bg-gradient-to-r from-gray-500/10 to-gray-700/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 left-20 w-48 h-48 bg-gradient-to-r from-gray-700/10 to-gray-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-12 shadow-2xl border border-white/20 animate-fade-in-up">
                    <div class="relative">
                        <!-- Quote icon -->
                        <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                            <div class="w-12 h-12 bg-gradient-to-r from-gray-500 to-gray-700 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-quote-left text-white text-xl"></i>
                            </div>
                        </div>

                        <!-- Quote text -->
                        <blockquote class="text-3xl md:text-4xl font-bold text-gray-800 leading-relaxed pt-8">
                            We don't just host events — we ignite movements, drive innovation, and create unforgettable experiences, all anchored in safety and excellence.
                        </blockquote>

                        <!-- Decorative elements -->
                        <div class="mt-8 flex justify-center space-x-4">
                            <div class="w-2 h-2 bg-gradient-to-r from-gray-500 to-gray-700 rounded-full"></div>
                            <div class="w-2 h-2 bg-gradient-to-r from-gray-700 to-gray-500 rounded-full"></div>
                            <div class="w-2 h-2 bg-gradient-to-r from-gray-500 to-gray-700 rounded-full"></div>
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
        <div class="absolute inset-0 bg-gradient-to-r from-white/5 via-transparent to-gray-300/5"></div>

        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-gray-300/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-20">
                <h3 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-300 mb-8 animate-fade-in-down font-orbitron">
                    Events
                </h3>
                <div class="w-32 h-1 bg-gradient-to-r from-white to-gray-300 mx-auto rounded-full animate-fade-in-up"></div>
            </div>

            <!-- Event Tabs -->
            <div class="flex justify-center mb-8 md:mb-12">
                <div class="bg-gray-900/50 backdrop-blur-xl rounded-full p-1 md:p-2 border border-gray-700/50 flex">
                    <button id="past-events-tab" onclick="switchToPastEvents()" class="px-3 md:px-8 py-1.5 md:py-3 rounded-full text-black font-semibold transition-all duration-300 bg-gradient-to-r from-white to-gray-300 shadow-lg text-xs md:text-base whitespace-nowrap">
                        Past Events
                    </button>
                    <button id="upcoming-events-tab" onclick="switchToUpcomingEvents()" class="px-3 md:px-8 py-1.5 md:py-3 rounded-full text-gray-300 font-semibold transition-all duration-300 hover:text-white hover:bg-white/10 text-xs md:text-base whitespace-nowrap">
                        Upcoming Events
                    </button>
                </div>
            </div>





            <!-- Past Events Carousel -->
            <div id="past-events-content" class="max-w-6xl mx-auto" style="display: block;">
                <?php if (empty($pastEvents)): ?>
                    <div class="text-center py-12">
                        <p class="text-gray-400">No past events recorded yet.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php foreach ($pastEvents as $event): 
                            $hasImage = !empty($event['image_url']);
                            $gradient = !empty($event['gradient_theme']) ? $event['gradient_theme'] : 'from-white to-gray-300';
                            $badge = !empty($event['badge_text']) ? $event['badge_text'] : 'Past Event';
                            $buttonText = !empty($event['button_text']) ? $event['button_text'] : 'View Gallery';
                            $slug = !empty($event['slug']) ? $event['slug'] : generateSlug($event['title']);
                            $buttonLink = trim($event['button_link'] ?? '');
                            $isExternalLink = !empty($buttonLink) && (strpos($buttonLink, 'http://') === 0 || strpos($buttonLink, 'https://') === 0 || strpos($buttonLink, '#') === 0 || strpos($buttonLink, '/') === 0);
                        ?>
                            <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl overflow-hidden shadow-2xl border border-gray-700/50 transform group-hover:scale-105 transition-all duration-500 hover:shadow-white/20 animate-fade-in-up flex flex-col justify-between">
                                <div class="relative h-48 <?php echo $hasImage ? 'bg-black' : ('bg-gradient-to-br ' . htmlspecialchars($gradient)); ?> overflow-hidden">
                                    <?php if ($hasImage): ?>
                                        <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="absolute inset-0 w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20"></div>
                                    <?php else: ?>
                                        <div class="absolute inset-0 bg-black/20"></div>
                                    <?php endif; ?>
                                    <div class="absolute top-4 right-4 bg-gray-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow">
                                        <?php echo htmlspecialchars($badge); ?>
                                    </div>
                                    <div class="absolute bottom-4 left-4 right-4 <?php echo $hasImage ? 'text-white' : (strpos($gradient, 'from-gray-800') !== false || strpos($gradient, 'from-red-700') !== false ? 'text-white' : 'text-black'); ?>">
                                        <h4 class="text-xl font-bold truncate drop-shadow"><?php echo htmlspecialchars($event['title']); ?></h4>
                                        <p class="text-sm opacity-90"><?php echo htmlspecialchars($event['event_date_text']); ?></p>
                                    </div>
                                </div>
                                <div class="p-6 flex-1 flex flex-col justify-between">
                                    <p class="text-gray-300 mb-6 leading-relaxed"><?php echo htmlspecialchars($event['description']); ?></p>
                                    <?php if ($isExternalLink): ?>
                                        <a href="<?php echo htmlspecialchars($buttonLink); ?>" class="w-full text-center block bg-gradient-to-r from-white to-gray-300 text-black font-semibold py-3 rounded-xl hover:from-gray-100 hover:to-gray-400 transition-all duration-300">
                                            <?php echo htmlspecialchars($buttonText); ?>
                                        </a>
                                    <?php else: ?>
                                        <button onclick="openEventGallery('<?php echo htmlspecialchars($slug); ?>')" class="w-full bg-gradient-to-r from-white to-gray-300 text-black font-semibold py-3 rounded-xl hover:from-gray-100 hover:to-gray-400 transition-all duration-300">
                                            <?php echo htmlspecialchars($buttonText); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Upcoming Events Carousel -->
            <div id="upcoming-events-content" class="max-w-6xl mx-auto" style="display: none;">
                <?php if (empty($upcomingEvents)): ?>
                    <div class="text-center py-12">
                        <p class="text-gray-400">No upcoming events scheduled at this moment. Stay tuned for announcements!</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php foreach ($upcomingEvents as $event): 
                            $hasImage = !empty($event['image_url']);
                            $gradient = !empty($event['gradient_theme']) ? $event['gradient_theme'] : 'from-white to-gray-300';
                            $badge = !empty($event['badge_text']) ? $event['badge_text'] : 'Coming Soon';
                            $buttonText = !empty($event['button_text']) ? $event['button_text'] : 'Learn More';
                            $slug = !empty($event['slug']) ? $event['slug'] : generateSlug($event['title']);
                            $buttonLink = trim($event['button_link'] ?? '');
                            $isExternalLink = !empty($buttonLink) && (strpos($buttonLink, 'http://') === 0 || strpos($buttonLink, 'https://') === 0 || strpos($buttonLink, '#') === 0 || strpos($buttonLink, '/') === 0);
                        ?>
                            <div class="group bg-gray-900/80 backdrop-blur-xl rounded-2xl overflow-hidden shadow-2xl border border-gray-700/50 transform group-hover:scale-105 transition-all duration-500 hover:shadow-white/20 animate-fade-in-up flex flex-col justify-between">
                                <div class="relative h-48 <?php echo $hasImage ? 'bg-black' : ('bg-gradient-to-br ' . htmlspecialchars($gradient)); ?> overflow-hidden">
                                    <?php if ($hasImage): ?>
                                        <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="absolute inset-0 w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20"></div>
                                    <?php else: ?>
                                        <div class="absolute inset-0 bg-black/20"></div>
                                    <?php endif; ?>
                                    <div class="absolute top-4 right-4 bg-gray-600 text-white px-3 py-1 rounded-full text-sm font-semibold shadow">
                                        <?php echo htmlspecialchars($badge); ?>
                                    </div>
                                    <div class="absolute bottom-4 left-4 right-4 <?php echo $hasImage ? 'text-white' : (strpos($gradient, 'from-gray-800') !== false || strpos($gradient, 'from-red-700') !== false ? 'text-white' : 'text-black'); ?>">
                                        <h4 class="text-xl font-bold truncate drop-shadow"><?php echo htmlspecialchars($event['title']); ?></h4>
                                        <p class="text-sm opacity-90"><?php echo htmlspecialchars($event['event_date_text']); ?></p>
                                    </div>
                                </div>
                                <div class="p-6 flex-1 flex flex-col justify-between">
                                    <p class="text-gray-300 mb-6 leading-relaxed"><?php echo htmlspecialchars($event['description']); ?></p>
                                    <?php if ($isExternalLink): ?>
                                        <a href="<?php echo htmlspecialchars($buttonLink); ?>" class="w-full text-center block bg-gradient-to-r from-white to-gray-300 text-black font-semibold py-3 rounded-xl hover:from-gray-100 hover:to-gray-400 transition-all duration-300">
                                            <?php echo htmlspecialchars($buttonText); ?>
                                        </a>
                                    <?php else: ?>
                                        <button onclick="openEventGallery('<?php echo htmlspecialchars($slug); ?>')" class="w-full bg-gradient-to-r from-white to-gray-300 text-black font-semibold py-3 rounded-xl hover:from-gray-100 hover:to-gray-400 transition-all duration-300">
                                            <?php echo htmlspecialchars($buttonText); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>



    <!-- Teams Section -->
    <section id="team" class="py-20 md:py-32 relative overflow-hidden bg-gray-50">
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-20">
                <h3 class="text-5xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-gray-600 to-gray-800 mb-8 font-orbitron">
                    Meet the Team
                </h3>
                <div class="w-32 h-1 bg-gradient-to-r from-gray-500 to-gray-700 mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 max-w-4xl mx-auto">
                <?php 
                $displayTeam = !empty($teamMembers) ? $teamMembers : [
                    ['name' => 'Stevyn', 'role' => 'Team Lead', 'icon_class' => 'fas fa-user-tie', 'gradient_theme' => 'from-gray-500 to-gray-700', 'image_url' => null, 'bio' => null],
                    ['name' => 'Quame', 'role' => 'Project Manager', 'icon_class' => 'fas fa-tasks', 'gradient_theme' => 'from-gray-700 to-gray-500', 'image_url' => null, 'bio' => null],
                    ['name' => 'Femi', 'role' => 'Technical Lead', 'icon_class' => 'fas fa-cogs', 'gradient_theme' => 'from-gray-500 to-gray-700', 'image_url' => null, 'bio' => null]
                ];
                foreach ($displayTeam as $tm): 
                    $tGrad = !empty($tm['gradient_theme']) ? $tm['gradient_theme'] : 'from-gray-500 to-gray-700';
                    $tIcon = !empty($tm['icon_class']) ? $tm['icon_class'] : 'fas fa-user-tie';
                ?>
                <div class="group bg-white rounded-2xl p-8 shadow-xl border border-gray-200 flex flex-col items-center text-center hover:shadow-2xl transition-all duration-300">
                    <div class="w-20 h-20 bg-gradient-to-r <?php echo htmlspecialchars($tGrad); ?> rounded-full flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300 overflow-hidden">
                        <?php if (!empty($tm['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($tm['image_url']); ?>" alt="<?php echo htmlspecialchars($tm['name']); ?>" class="w-full h-full object-cover rounded-full">
                        <?php else: ?>
                            <i class="<?php echo htmlspecialchars($tIcon); ?> text-white text-3xl"></i>
                        <?php endif; ?>
                    </div>
                    <h4 class="text-2xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($tm['name']); ?></h4>
                    <p class="text-lg text-gray-600 font-semibold mb-2"><?php echo htmlspecialchars($tm['role']); ?></p>
                    <?php if (!empty($tm['bio'])): ?>
                        <p class="text-sm text-gray-500 mt-2 max-w-xs leading-relaxed"><?php echo htmlspecialchars($tm['bio']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Join the Revolution Section -->
    <section id="participate" class="py-8 md:py-12 lg:py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-white/10 via-transparent to-gray-300/10"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-8 md:mb-12">
                <h3 class="text-2xl md:text-4xl lg:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-300 mb-3 md:mb-4 animate-fade-in-down font-orbitron">
                    JOIN THE REVOLUTION
                </h3>
                <p class="text-sm md:text-lg text-gray-200 mb-2 md:mb-3 animate-fade-in-up">Forward-thinking brands and passionate enthusiasts partner with Krosskhana to set <span class="underline decoration-white">trends and embrace</span> the future of the automotive industry.</p>
                <div class="w-20 md:w-24 h-1 bg-gradient-to-r from-white to-gray-300 mx-auto rounded-full animate-fade-in-up"></div>
            </div>

            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-6 md:mb-8">
                    <h4 class="text-xl md:text-2xl font-bold text-white mb-1 md:mb-2 animate-fade-in-up">
                        Connect with <span class="underline decoration-white">Krosskhana</span>
                    </h4>
                    <p class="text-sm md:text-base text-gray-300 italic mb-4 md:mb-6 animate-fade-in-up">Select your interest:</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <!-- Corporate Enquiry -->
                    <div class="group relative animate-fade-in-up" style="animation-delay: 0.1s">
                        <div class="absolute inset-0 bg-gradient-to-r from-white to-gray-300 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                        <a href="corporate-partnership.php" class="relative bg-white/10 backdrop-blur-xl rounded-2xl p-4 md:p-6 border border-white/20 shadow-2xl transform group-hover:scale-105 transition-all duration-500 cursor-pointer block">
                            <div class="text-center">
                                <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-r from-white to-gray-300 rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
                                    <i class="fas fa-building text-lg md:text-xl text-black"></i>
                                </div>
                                <h5 class="text-lg md:text-xl font-bold text-white mb-2 md:mb-3">Corporate Enquiry</h5>
                                <p class="text-gray-300 mb-3 md:mb-4 text-xs md:text-sm">Partner with us for business opportunities and corporate collaborations.</p>
                                <button class="w-full bg-gradient-to-r from-white to-gray-300 text-black font-semibold py-1.5 md:py-2 rounded-xl hover:from-gray-100 hover:to-gray-400 transition-all duration-300 text-xs md:text-sm">
                                    Get Started
                                </button>
                            </div>
                        </a>
                    </div>

                    <!-- Racer Registration -->
                    <div class="group relative animate-fade-in-up" style="animation-delay: 0.2s">
                        <div class="absolute inset-0 bg-gradient-to-r from-gray-300 to-white rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                        <a href="racer-registration.php" class="relative bg-white/10 backdrop-blur-xl rounded-2xl p-4 md:p-6 border border-white/20 shadow-2xl transform group-hover:scale-105 transition-all duration-500 cursor-pointer block">
                            <div class="text-center">
                                <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-r from-gray-300 to-white rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
                                    <i class="fas fa-car text-lg md:text-xl text-black"></i>
                                </div>
                                <h5 class="text-lg md:text-xl font-bold text-white mb-2 md:mb-3">Racer Registration</h5>
                                <p class="text-gray-300 mb-3 md:mb-4 text-xs md:text-sm">Join our racing events and showcase your skills on the track.</p>
                                <button class="w-full bg-gradient-to-r from-gray-300 to-white text-black font-semibold py-1.5 md:py-2 rounded-xl hover:from-gray-400 hover:to-gray-100 transition-all duration-300 text-xs md:text-sm">
                                    Register Now
                                </button>
                            </div>
                        </a>
                    </div>

                    <!-- Sponsorship Opportunities -->
                    <div class="group relative animate-fade-in-up" style="animation-delay: 0.3s">
                        <div class="absolute inset-0 bg-gradient-to-r from-white to-gray-300 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                        <a href="sponsorship.php" class="relative bg-white/10 backdrop-blur-xl rounded-2xl p-4 md:p-6 border border-white/20 shadow-2xl transform group-hover:scale-105 transition-all duration-500 cursor-pointer block">
                            <div class="text-center">
                                <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-r from-white to-gray-300 text-black rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
                                    <i class="fas fa-handshake text-lg md:text-xl text-black"></i>
                                </div>
                                <h5 class="text-lg md:text-xl font-bold text-white mb-2 md:mb-3">Sponsorship Opportunities</h5>
                                <p class="text-gray-300 mb-3 md:mb-4 text-xs md:text-sm">Explore sponsorship packages and brand partnership options.</p>
                                <button class="w-full bg-gradient-to-r from-white to-gray-300 text-black font-semibold py-1.5 md:py-2 rounded-xl hover:from-gray-100 hover:to-gray-400 transition-all duration-300 text-xs md:text-sm">
                                    Learn More
                                </button>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="container mx-auto px-4 py-16">
            <!-- Main Footer Content -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">

                <!-- Company Info -->
                <div class="md:col-span-2">
                    <div class="mb-6">
                        <img src="assets/logos/nav-bar-footer-logo.png" alt="Krosskhana Logo" class="h-16 w-auto mb-4" />
                        <p class="text-gray-300 leading-relaxed mb-6 max-w-md">
                            Where automotive culture meets artistic expression. Experience the fusion of motorsport, music, fashion, and cultural exhibitions.
                        </p>
                        <div class="flex space-x-4">
                            <a href="https://www.tiktok.com/@krosskhana" target="_blank" rel="noopener noreferrer"
                               class="w-10 h-10 bg-gray-700 hover:bg-gray-600 rounded-full flex items-center justify-center transition-colors duration-300">
                                <i class="fab fa-tiktok text-white"></i>
                            </a>
                            <a href="https://www.instagram.com/krosskhana/" target="_blank" rel="noopener noreferrer"
                               class="w-10 h-10 bg-gray-700 hover:bg-gray-600 rounded-full flex items-center justify-center transition-colors duration-300">
                                <i class="fab fa-instagram text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-white">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#about" class="text-gray-300 hover:text-white transition-colors duration-300">About Us</a></li>
                        <li><a href="#events" class="text-gray-300 hover:text-white transition-colors duration-300">Events</a></li>
                        <li><a href="#team" class="text-gray-300 hover:text-white transition-colors duration-300">Our Team</a></li>
                        <li><a href="corporate-partnership.php" class="text-gray-300 hover:text-white transition-colors duration-300">Partnership</a></li>
                        <li><a href="racer-registration.php" class="text-gray-300 hover:text-white transition-colors duration-300">Registration</a></li>
                    </ul>
                </div>

                <!-- Newsletter Signup -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-white">Stay Updated</h4>
                    <p class="text-gray-300 text-sm mb-4">Subscribe to our newsletter for the latest updates and exclusive content.</p>
                    <form id="newsletter-form" class="space-y-3">
                        <input type="text" name="name" placeholder="Enter your name" required
                               class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-gray-400 transition-colors duration-300" />
                        <input type="email" name="email" placeholder="Enter your email" required
                               class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-gray-400 transition-colors duration-300" />
                        <button type="submit"
                                class="w-full px-4 py-2 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-500 hover:to-gray-600 text-white font-medium rounded-lg transition-all duration-300 transform hover:scale-105">
                            Subscribe
                        </button>
                    </form>
                    <div id="newsletter-message" class="mt-3 text-sm hidden"></div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-700 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                    <div class="text-center md:text-left">
                        <p class="text-gray-400 text-sm">&copy; <?php echo date('Y'); ?> <span class="text-white font-semibold">Krosskhana Motorsports</span>. All rights reserved.</p>
                        <p class="text-gray-500 text-xs mt-1">Designed by <a href="https://www.jlm.com.ng" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors duration-300 underline">JLM</a></p>
                    </div>
                    <div class="flex space-x-6 text-sm">
                        <a href="privacy-policy.php" class="text-gray-400 hover:text-white transition-colors duration-300">Privacy Policy</a>
                        <a href="terms-of-service.php" class="text-gray-400 hover:text-white transition-colors duration-300">Terms of Service</a>
                        <a href="cookie-policy.php" class="text-gray-400 hover:text-white transition-colors duration-300">Cookie Policy</a>
                        <a href="admin/login.php" class="text-gray-500 hover:text-gray-300 transition-colors duration-300">Admin</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 z-50 bg-gradient-to-r from-gray-600 to-gray-800 hover:from-gray-700 hover:to-gray-900 text-white w-14 h-14 rounded-full shadow-2xl transform hover:scale-110 transition-all duration-300 flex items-center justify-center animate-fade-in group backdrop-blur-xl border border-white/20 hidden" aria-label="Back to top">
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
            <button id="modal-close" class="px-6 py-2 bg-gradient-to-r from-white to-gray-300 text-black font-bold rounded-full shadow transition">
                OK
            </button>
        </div>
    </div>

    <!-- Newsletter Form JavaScript -->
    <script>
        document.getElementById('newsletter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const messageDiv = document.getElementById('newsletter-message');
            
            // Show loading state
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
            submitBtn.disabled = true;
            
            // Hide any previous messages
            messageDiv.classList.add('hidden');
            
            // Submit form
            fetch('forms/process-newsletter.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Show success message
                    messageDiv.innerHTML = `<span class="text-green-400">${data.message}</span>`;
                    messageDiv.classList.remove('hidden');
                    
                    // Reset form
                    this.reset();
                    
                    // Hide message after 5 seconds
                    setTimeout(() => {
                        messageDiv.classList.add('hidden');
                    }, 5000);
                } else {
                    // Show error message
                    messageDiv.innerHTML = `<span class="text-red-400">${data.message}</span>`;
                    messageDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.innerHTML = '<span class="text-red-400">An error occurred. Please try again.</span>';
                messageDiv.classList.remove('hidden');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    </script>



</body>
</html>
