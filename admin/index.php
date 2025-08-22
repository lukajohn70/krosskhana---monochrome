<?php
require_once '../includes/config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get dashboard statistics
$pdo = getDBConnection();
$stats = [];

if ($pdo) {
    // Newsletter stats
    $stmt = $pdo->query("SELECT COUNT(*) as total, COUNT(CASE WHEN status = 'active' THEN 1 END) as active FROM newsletter_subscriptions");
    $newsletterStats = $stmt->fetch();
    
    // Racer registration stats
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
        COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
        COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected
        FROM racer_registrations");
    $racerStats = $stmt->fetch();
    
    // Corporate inquiry stats
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
        COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted,
        COUNT(CASE WHEN status = 'qualified' THEN 1 END) as qualified
        FROM corporate_inquiries");
    $corporateStats = $stmt->fetch();
    
    // Sponsorship inquiry stats
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
        COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted,
        COUNT(CASE WHEN status = 'qualified' THEN 1 END) as qualified
        FROM sponsorship_inquiries");
    $sponsorshipStats = $stmt->fetch();
    
    // Contact inquiry stats
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
        COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted,
        COUNT(CASE WHEN status = 'resolved' THEN 1 END) as resolved
        FROM contact_inquiries");
    $contactStats = $stmt->fetch();
    
    // Campaign stats - check if table exists first
    try {
        $stmt = $pdo->query("SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN status = 'draft' THEN 1 END) as drafts,
            COUNT(CASE WHEN status = 'scheduled' THEN 1 END) as scheduled,
            COUNT(CASE WHEN status = 'sent' THEN 1 END) as sent,
            SUM(sent_count) as total_sent,
            SUM(opened_count) as total_opened,
            SUM(clicked_count) as total_clicked
            FROM campaigns");
        $campaignStats = $stmt->fetch();
    } catch (PDOException $e) {
        // Campaign table doesn't exist yet, use default values
        $campaignStats = [
            'total' => 0,
            'drafts' => 0,
            'scheduled' => 0,
            'sent' => 0,
            'total_sent' => 0,
            'total_opened' => 0,
            'total_clicked' => 0
        ];
    }
    
    $stats = [
        'newsletter' => $newsletterStats,
        'racer' => $racerStats,
        'corporate' => $corporateStats,
        'sponsorship' => $sponsorshipStats,
        'contact' => $contactStats,
        'campaign' => $campaignStats
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Krosskhana</title>
    <link rel="icon" type="image/png" href="../assets/logos/Krosskhana-05.png">
    
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
                        orbitron: ['Orbitron', 'monospace'],
                    },
                    colors: {
                        'gray': {
                            400: '#9ca3af',
                            500: '#6b7280',
                            600: '#4b5563',
                            700: '#374151',
                            800: '#1f2937',
                            900: '#111827',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Custom Fonts -->
    <style>
        @font-face {
            font-family: 'Orbitron';
            src: url('../assets/fonts/Orbitron-VariableFont_wght.ttf') format('truetype');
            font-weight: 100 900;
            font-style: normal;
        }
        
        @font-face {
            font-family: 'Orbitron';
            src: url('../assets/fonts/Orbitron-Regular.ttf') format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        
        @font-face {
            font-family: 'Orbitron';
            src: url('../assets/fonts/Orbitron-Bold.ttf') format('truetype');
            font-weight: 700;
            font-style: normal;
        }
        
        @font-face {
            font-family: 'Orbitron';
            src: url('../assets/fonts/Orbitron-Black.ttf') format('truetype');
            font-weight: 900;
            font-style: normal;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-black to-gray-900 min-h-screen font-poppins">
    <!-- Top Navigation -->
    <nav class="bg-gray-900/80 backdrop-blur-xl shadow-2xl border-b border-gray-700/50 fixed top-0 left-0 right-0 z-40">
        <div class="flex justify-between h-16 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center">
                <button id="sidebar-toggle" class="text-gray-300 hover:text-white p-2 rounded-md lg:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex-shrink-0 flex items-center ml-4 lg:ml-0">
                    <img class="h-8 w-auto hover:scale-110 transition-transform duration-300" src="../assets/logos/Krosskhana-02.png" alt="Krosskhana">
                    <span class="ml-3 text-xl font-bold text-white font-orbitron">Admin Dashboard</span>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-300">Welcome, Admin</span>
                <a href="logout.php" class="bg-gradient-to-r from-white to-gray-300 hover:from-gray-200 hover:to-gray-400 text-black px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900/95 backdrop-blur-xl shadow-2xl border-r border-gray-700/50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="flex flex-col h-full">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-center h-16 border-b border-gray-700/50">
                <img class="h-10 w-auto" src="../assets/logos/Krosskhana-02.png" alt="Krosskhana">
            </div>
            
            <!-- Sidebar Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="index.php" class="flex items-center px-4 py-3 text-white bg-gray-800/50 rounded-lg border border-gray-700/50 transition-all duration-300 hover:bg-gray-700/50 hover:shadow-lg">
                    <i class="fas fa-tachometer-alt mr-3 text-gray-300"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                
                <a href="newsletter.php" class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg">
                    <i class="fas fa-newspaper mr-3"></i>
                    <span class="font-medium">Newsletter</span>
                </a>
                
                <a href="registrations.php" class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg">
                    <i class="fas fa-car mr-3"></i>
                    <span class="font-medium">Registrations</span>
                </a>
                
                <a href="contact.php" class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg">
                    <i class="fas fa-envelope mr-3"></i>
                    <span class="font-medium">Contact</span>
                </a>
                
                <a href="corporate.php" class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg">
                    <i class="fas fa-building mr-3"></i>
                    <span class="font-medium">Corporate</span>
                </a>
                
                <a href="sponsorship.php" class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg">
                    <i class="fas fa-handshake mr-3"></i>
                    <span class="font-medium">Sponsorship</span>
                </a>
                
                <a href="campaigns.php" class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg">
                    <i class="fas fa-bullhorn mr-3"></i>
                    <span class="font-medium">Campaigns</span>
                </a>
            </nav>
            
            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-gray-700/50">
                <div class="text-center">
                    <p class="text-xs text-gray-500">Krosskhana Admin</p>
                    <p class="text-xs text-gray-600 mt-1">v1.0.0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay for mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden hidden"></div>

    <!-- Main Content -->
    <div class="lg:ml-64 pt-16 bg-gray-50 min-h-screen">
        <div class="p-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
            <!-- Newsletter Subscribers -->
            <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                            <i class="fas fa-newspaper text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 truncate">Newsletter Subscribers</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo $stats['newsletter']['total'] ?? 0; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-200/50">
                    <div class="text-sm">
                        <span class="text-gray-500 font-medium"><?php echo $stats['newsletter']['active'] ?? 0; ?></span>
                        <span class="text-gray-600">active</span>
                    </div>
                </div>
            </div>

            <!-- Racer Registrations -->
            <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                                <i class="fas fa-car text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">Racer Registrations</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['racer']['total'] ?? 0; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-200/50">
                    <div class="text-sm">
                        <span class="text-gray-500 font-medium"><?php echo $stats['racer']['pending'] ?? 0; ?></span>
                        <span class="text-gray-600">pending</span>
                    </div>
                </div>
            </div>

            <!-- Corporate Inquiries -->
            <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                                <i class="fas fa-building text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">Corporate Inquiries</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['corporate']['total'] ?? 0; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-200/50">
                    <div class="text-sm">
                        <span class="text-gray-500 font-medium"><?php echo $stats['corporate']['pending'] ?? 0; ?></span>
                        <span class="text-gray-600">pending</span>
                    </div>
                </div>
            </div>

            <!-- Sponsorship Inquiries -->
            <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                                <i class="fas fa-handshake text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">Sponsorship Inquiries</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['sponsorship']['total'] ?? 0; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-200/50">
                    <div class="text-sm">
                        <span class="text-gray-500 font-medium"><?php echo $stats['sponsorship']['pending'] ?? 0; ?></span>
                        <span class="text-gray-600">pending</span>
                    </div>
                </div>
            </div>

            <!-- Contact Inquiries -->
            <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">Contact Inquiries</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['contact']['total'] ?? 0; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-200/50">
                    <div class="text-sm">
                        <span class="text-gray-500 font-medium"><?php echo $stats['contact']['pending'] ?? 0; ?></span>
                        <span class="text-gray-600">pending</span>
                    </div>
                </div>
            </div>

            <!-- Campaigns -->
            <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                                <i class="fas fa-bullhorn text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">Email Campaigns</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['campaign']['total'] ?? 0; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-gray-200/50">
                    <div class="text-sm">
                        <span class="text-gray-500 font-medium"><?php echo $stats['campaign']['sent'] ?? 0; ?></span>
                        <span class="text-gray-600">sent</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaign System Setup Notice -->
        <?php if (!isset($campaignStats) || $campaignStats['total'] === 0): ?>
        <div class="bg-gray-50 backdrop-blur-xl border border-gray-200/50 rounded-2xl p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-gray-600"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-500">Campaign System Setup Required</h3>
                    <div class="mt-2 text-sm text-gray-600">
                        <p>The campaign system needs to be set up before you can create email campaigns.</p>
                        <p class="mt-1">
                            <a href="../setup-campaign-system.php" class="font-medium underline hover:text-gray-500 transition-colors duration-300">
                                Click here to run the setup script
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 mb-8">
            <div class="px-6 py-4 border-b border-gray-200/50">
                <h3 class="text-lg font-medium text-gray-900 font-orbitron">Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                    <a href="newsletter.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-200/50 transition-all duration-300 transform hover:scale-105 border border-gray-200/50">
                        <i class="fas fa-newspaper text-gray-500 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-500">Newsletter</span>
                    </a>
                    <a href="registrations.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-200/50 transition-all duration-300 transform hover:scale-105 border border-gray-200/50">
                        <i class="fas fa-car text-gray-500 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-500">Registrations</span>
                    </a>
                    <a href="corporate.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-200/50 transition-all duration-300 transform hover:scale-105 border border-gray-200/50">
                        <i class="fas fa-building text-gray-500 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-500">Corporate</span>
                    </a>
                    <a href="sponsorship.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-200/50 transition-all duration-300 transform hover:scale-105 border border-gray-200/50">
                        <i class="fas fa-handshake text-gray-500 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-500">Sponsorship</span>
                    </a>
                    <a href="contact.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-200/50 transition-all duration-300 transform hover:scale-105 border border-gray-200/50">
                        <i class="fas fa-envelope text-gray-500 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-500">Contact</span>
                    </a>
                    <a href="campaigns.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-200/50 transition-all duration-300 transform hover:scale-105 border border-gray-200/50">
                        <i class="fas fa-bullhorn text-gray-500 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-gray-500">Campaigns</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Newsletter Subscriptions -->
            <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50">
                <div class="px-6 py-4 border-b border-gray-200/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 font-orbitron">Recent Newsletter Subscriptions</h3>
                        <a href="newsletter.php" class="text-gray-500 hover:text-gray-900 text-sm font-medium transition-colors duration-300">View All</a>
                    </div>
                </div>
                <div class="p-6">
                    <div id="recent-newsletter" class="space-y-4">
                        <!-- Content will be loaded via AJAX -->
                        <div class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-gray-600 text-2xl"></i>
                            <p class="text-gray-600 mt-2">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Racer Registrations -->
            <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50">
                <div class="px-6 py-4 border-b border-gray-200/50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 font-orbitron">Recent Racer Registrations</h3>
                        <a href="registrations.php" class="text-gray-500 hover:text-gray-900 text-sm font-medium transition-colors duration-300">View All</a>
                    </div>
                </div>
                <div class="p-6">
                    <div id="recent-registrations" class="space-y-4">
                        <!-- Content will be loaded via AJAX -->
                        <div class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-gray-600 text-2xl"></i>
                            <p class="text-gray-600 mt-2">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        // Sidebar functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            // Toggle sidebar on mobile
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('-translate-x-full');
                sidebarOverlay.classList.toggle('hidden');
            });

            // Close sidebar when clicking overlay
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
            });

            // Close sidebar on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                }
            });

            // Highlight current page in sidebar
            const currentPage = window.location.pathname.split('/').pop();
            const sidebarLinks = document.querySelectorAll('#sidebar nav a');
            sidebarLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.remove('text-gray-500');
                    link.classList.add('text-gray-900', 'bg-gray-50', 'border', 'border-gray-200/50');
                }
            });
        });

        // Load recent data
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentNewsletter();
            loadRecentRegistrations();
        });

        function loadRecentNewsletter() {
            fetch('api/newsletter.php?limit=5')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recent-newsletter');
                    if (data.status === 'success' && data.data.length > 0) {
                        container.innerHTML = data.data.map(sub => `
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200/50">
                                <div>
                                    <p class="font-medium text-gray-900">${sub.name}</p>
                                    <p class="text-sm text-gray-600">${sub.email}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full ${sub.status === 'active' ? 'bg-gray-200 text-gray-500' : 'bg-gray-300 text-gray-600'}">
                                    ${sub.status}
                                </span>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<p class="text-gray-600 text-center py-4">No recent subscriptions</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading newsletter data:', error);
                    document.getElementById('recent-newsletter').innerHTML = '<p class="text-gray-600 text-center py-4">Error loading data</p>';
                });
        }

        function loadRecentRegistrations() {
            fetch('api/registrations.php?limit=5')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recent-registrations');
                    if (data.status === 'success' && data.data.length > 0) {
                        container.innerHTML = data.data.map(reg => `
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200/50">
                                <div>
                                    <p class="font-medium text-gray-900">${reg.full_name}</p>
                                    <p class="text-sm text-gray-600">${reg.email}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(reg.status)}">
                                    ${reg.status}
                                </span>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<p class="text-gray-600 text-center py-4">No recent registrations</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading registration data:', error);
                    document.getElementById('recent-registrations').innerHTML = '<p class="text-gray-600 text-center py-4">Error loading data</p>';
                });
        }

        function getStatusColor(status) {
            switch(status) {
                case 'pending': return 'bg-gray-300 text-gray-500';
                case 'approved': return 'bg-gray-200 text-gray-500';
                case 'rejected': return 'bg-gray-500 text-gray-600';
                case 'contacted': return 'bg-gray-200 text-gray-500';
                default: return 'bg-gray-300 text-gray-600';
            }
        }
    </script>
</body>
</html>
