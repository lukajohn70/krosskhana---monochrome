<?php
require_once '../includes/config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();
$stats = [];

if ($pdo) {
    // Get campaign statistics
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN status = 'draft' THEN 1 END) as drafts,
        COUNT(CASE WHEN status = 'scheduled' THEN 1 END) as scheduled,
        COUNT(CASE WHEN status = 'sent' THEN 1 END) as sent,
        SUM(sent_count) as total_sent,
        SUM(opened_count) as total_opened,
        SUM(clicked_count) as total_clicked
        FROM campaigns");
    $stats = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Management - Krosskhana Admin</title>
    <link rel="icon" type="image/png" href="../assets/logos/Krosskhana-05.png">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Toast Notifications CSS -->
    <link rel="stylesheet" href="assets/notifications.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
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
    
    <!-- Quill Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-black to-gray-900 min-h-screen font-poppins">
    <?php $pageTitle = 'Email Campaigns'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Toast Container -->
    <div id="toast-container" class="toast-container"></div>


        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
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
                                <dt class="text-sm font-medium text-gray-600 truncate">Total Campaigns</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['total'] ?? 0; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">Drafts</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['drafts'] ?? 0; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                                <i class="fas fa-paper-plane text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">Total Sent</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['total_sent'] ?? 0; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                                <i class="fas fa-eye text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">Open Rate</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?php 
                                    $totalSent = $stats['total_sent'] ?? 0;
                                    $totalOpened = $stats['total_opened'] ?? 0;
                                    echo $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 1) . '%' : '0%';
                                    ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 mb-6">
            <div class="px-6 py-4 border-b border-gray-200/50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                    <h3 class="text-lg font-medium text-gray-900 font-orbitron">Email Campaigns</h3>
                    <div class="flex space-x-3">
                        <button onclick="openCreateCampaignModal()" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-plus mr-2"></i>Create Campaign
                        </button>
                        <button onclick="openTemplatesModal()" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-file-alt mr-2"></i>Templates
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Setup Notice -->
        <div id="setup-notice" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 hidden">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Campaign System Setup Required</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>The campaign system needs to be set up before you can create campaigns. Please run the setup script first.</p>
                    </div>
                    <div class="mt-4">
                        <a href="../setup-campaign-system.php" class="bg-yellow-100 text-yellow-800 px-3 py-2 rounded-md text-sm font-medium hover:bg-yellow-200 transition-colors duration-300">
                            Run Setup
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaigns Table -->
        <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 overflow-hidden">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Campaign
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Recipients
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Performance
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="campaigns-table-body-desktop" class="bg-white divide-y divide-gray-200">
                        <!-- Content will be loaded via AJAX -->
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-spinner fa-spin text-gray-600 text-2xl"></i>
                                <p class="text-gray-600 mt-2">Loading campaigns...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden" id="campaigns-table-body-mobile">
                <!-- Content will be loaded via AJAX -->
                <div class="p-6 text-center">
                    <i class="fas fa-spinner fa-spin text-gray-600 text-2xl"></i>
                    <p class="text-gray-600 mt-2">Loading campaigns...</p>
                </div>
            </div>
            
            <!-- Pagination -->
            <div id="pagination" class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                <!-- Pagination will be loaded via AJAX -->
            </div>
        </div>
    </div>

    <!-- Create Campaign Modal -->
    <div id="create-campaign-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Create New Campaign</h3>
                    <button onclick="closeCreateCampaignModal()" class="text-gray-600 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="campaign-form" class="space-y-6">
                    <!-- Campaign Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Campaign Name</label>
                            <input type="text" id="campaign-name" name="name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Subject</label>
                            <input type="text" id="campaign-subject" name="subject" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Target Audience -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="radio" id="all-subscribers" name="target_type" value="all" checked
                                    class="mr-2" onchange="updateTargetAudience()">
                                <label for="all-subscribers" class="text-sm text-gray-600">All Active Subscribers</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="tagged-subscribers" name="target_type" value="tags"
                                    class="mr-2" onchange="updateTargetAudience()">
                                <label for="tagged-subscribers" class="text-sm text-gray-600">Subscribers with Specific Tags</label>
                            </div>
                            <div id="tag-selection" class="ml-6 hidden">
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2" id="tags-container">
                                    <!-- Tags will be loaded via AJAX -->
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="custom-segment" name="target_type" value="custom"
                                    class="mr-2" onchange="updateTargetAudience()">
                                <label for="custom-segment" class="text-sm text-gray-600">Custom Segment</label>
                            </div>
                            <div id="custom-segment-options" class="ml-6 hidden space-y-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Subscribed After</label>
                                    <input type="date" id="subscribed-after" name="subscribed_after"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Subscribed Before</label>
                                    <input type="date" id="subscribed-before" name="subscribed_before"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Template Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Template</label>
                        <div class="flex items-center space-x-4 mb-3">
                            <button type="button" onclick="openTemplateSelector()" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-file-alt mr-2"></i>Choose Template
                            </button>
                            <button type="button" onclick="clearTemplate()" class="bg-gray-500 hover:bg-gray-300 text-gray-900 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-times mr-2"></i>Clear Template
                            </button>
                        </div>
                        <div id="selected-template-info" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-3 mb-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-medium text-gray-800">Selected Template: </span>
                                    <span id="template-name" class="text-sm text-gray-700"></span>
                                </div>
                                <button type="button" onclick="clearTemplate()" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Email Content -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Content</label>
                        <div id="editor" class="h-64"></div>
                        <input type="hidden" id="campaign-content" name="content">
                    </div>

                    <!-- Campaign Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Send Immediately</label>
                            <div class="flex items-center">
                                <input type="radio" id="send-now" name="send_type" value="now" checked
                                    class="mr-2" onchange="updateSendOptions()">
                                <label for="send-now" class="text-sm text-gray-600">Send Now</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="send-later" name="send_type" value="later"
                                    class="mr-2" onchange="updateSendOptions()">
                                <label for="send-later" class="text-sm text-gray-600">Schedule for Later</label>
                            </div>
                            <div id="schedule-options" class="mt-3 hidden">
                                <input type="datetime-local" id="schedule-time" name="scheduled_at"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preview Recipients</label>
                            <button type="button" onclick="previewRecipients()" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-eye mr-2"></i>Preview
                            </button>
                            <div id="recipient-count" class="mt-2 text-sm text-gray-600">
                                Recipients will be calculated when you select target audience
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" onclick="closeCreateCampaignModal()" class="px-4 py-2 bg-gray-300 text-gray-500 rounded-lg hover:bg-gray-400 transition-colors duration-300">
                            Cancel
                        </button>
                        <button type="button" onclick="saveDraft()" class="px-4 py-2 bg-gray-500 text-gray-900 rounded-lg hover:bg-gray-300 transition-colors">
                            Save Draft
                        </button>
                        <button type="submit" class="px-4 py-2 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-200 hover:to-gray-400 text-white rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                            Create Campaign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Template Selector Modal -->
    <div id="template-selector-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-5xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-medium text-gray-900">Choose Email Template</h3>
                    <button onclick="closeTemplateSelector()" class="text-gray-600 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="template-selector-container">
                    <!-- Templates will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Templates Modal -->
    <div id="templates-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-5xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-medium text-gray-900">Email Templates</h3>
                    <button onclick="closeTemplatesModal()" class="text-gray-600 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="templates-container">
                    <div class="flex items-center justify-center p-8">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin text-2xl text-gray-600 mb-2"></i>
                            <p class="text-gray-500">Loading templates...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script>
        let quill;
        let currentPage = 1;

        // Initialize Quill editor
        document.addEventListener('DOMContentLoaded', function() {
            quill = new Quill('#editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            loadCampaigns();
            loadTags();
            loadTemplates();
        });

        function loadCampaigns() {
            const params = new URLSearchParams({
                page: currentPage
            });

            fetch(`api/campaigns.php?${params}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Campaign data received:', data); // Debug log
                    if (data.status === 'success') {
                        renderCampaignsTable(data.data);
                        renderPagination(data.pagination);
                    } else {
                        console.error('API returned error:', data);
                        showError(data.message || 'Error loading campaigns');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    // Show setup notice on error
                    const setupNotice = document.getElementById('setup-notice');
                    if (setupNotice) {
                        setupNotice.style.display = 'block';
                    }
                    const tbody = document.getElementById('campaigns-table-body');
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-exclamation-triangle text-yellow-400 text-2xl"></i>
                                <p class="text-gray-600 mt-2">Campaign system not set up</p>
                                <p class="text-sm text-gray-600 mt-1">Please run the setup script first</p>
                            </td>
                        </tr>
                    `;
                });
        }

        function renderCampaignsTable(campaigns) {
            const desktopTbody = document.getElementById('campaigns-table-body-desktop');
            const mobileContainer = document.getElementById('campaigns-table-body-mobile');
            
            if (campaigns.length === 0) {
                // Show setup notice if no campaigns exist
                const setupNotice = document.getElementById('setup-notice');
                if (setupNotice) {
                    setupNotice.style.display = 'block';
                }
                
                desktopTbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-bullhorn text-gray-600 text-2xl"></i>
                            <p class="text-gray-600 mt-2">No campaigns found</p>
                            <p class="text-gray-600 mt-1">Set up the campaign system to get started</p>
                        </td>
                    </tr>
                `;
                mobileContainer.innerHTML = `
                    <div class="p-6 text-center">
                        <i class="fas fa-bullhorn text-gray-600 text-2xl"></i>
                        <p class="text-gray-600 mt-2">No campaigns found</p>
                        <p class="text-gray-600 mt-1">Set up the campaign system to get started</p>
                    </div>
                `;
                return;
            }
            
            // Hide setup notice if campaigns exist
            const setupNotice = document.getElementById('setup-notice');
            if (setupNotice) {
                setupNotice.style.display = 'none';
            }

            // Desktop table
            desktopTbody.innerHTML = campaigns.map(campaign => {
                const openRate = campaign.total_recipients > 0 ? 
                    Math.round((campaign.opened_count / campaign.total_recipients) * 100) : 0;
                const clickRate = campaign.total_recipients > 0 ? 
                    Math.round((campaign.clicked_count / campaign.total_recipients) * 100) : 0;

                return `
                    <tr class="hover:bg-gray-50 transition-colors duration-300">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">${campaign.name}</div>
                            <div class="text-sm text-gray-600">${campaign.subject}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(campaign.status)}">
                                ${campaign.status}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${campaign.sent_count}/${campaign.total_recipients}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>Open: ${openRate}%</div>
                            <div>Click: ${clickRate}%</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${new Date(campaign.created_at).toLocaleDateString()}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewCampaign(${campaign.id})" class="text-gray-600 hover:text-gray-900 mr-3">
                                View
                            </button>
                            ${campaign.status === 'draft' ? `
                                <button onclick="editCampaign(${campaign.id})" class="text-gray-600 hover:text-gray-900 mr-3">
                                    Edit
                                </button>
                                <button onclick="sendCampaign(${campaign.id})" class="text-green-600 hover:text-green-900 mr-3">
                                    Send
                                </button>
                            ` : ''}
                            <button onclick="deleteCampaign(${campaign.id})" class="text-gray-600 hover:text-gray-500 transition-colors duration-300">
                                Delete
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            // Mobile cards
            mobileContainer.innerHTML = campaigns.map(campaign => {
                const openRate = campaign.total_recipients > 0 ? 
                    Math.round((campaign.opened_count / campaign.total_recipients) * 100) : 0;
                const clickRate = campaign.total_recipients > 0 ? 
                    Math.round((campaign.clicked_count / campaign.total_recipients) * 100) : 0;

                return `
                    <div class="p-4 border-b border-gray-200 last:border-b-0">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-900 text-sm">${campaign.name}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(campaign.status)}">
                                ${campaign.status}
                            </span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-envelope w-4 mr-2 text-gray-400"></i>
                                <span>${campaign.subject}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-users w-4 mr-2 text-gray-400"></i>
                                <span>${campaign.sent_count}/${campaign.total_recipients} recipients</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-chart-line w-4 mr-2 text-gray-400"></i>
                                <span>Open: ${openRate}% | Click: ${clickRate}%</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar w-4 mr-2 text-gray-400"></i>
                                <span>${new Date(campaign.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                        <div class="flex space-x-3 mt-4">
                            <button onclick="viewCampaign(${campaign.id})" class="text-gray-500 hover:text-gray-900 text-sm transition-colors duration-300">View</button>
                            ${campaign.status === 'draft' ? `
                                <button onclick="editCampaign(${campaign.id})" class="text-gray-500 hover:text-gray-900 text-sm transition-colors duration-300">Edit</button>
                                <button onclick="sendCampaign(${campaign.id})" class="text-green-600 hover:text-green-900 text-sm transition-colors duration-300">Send</button>
                            ` : ''}
                            <button onclick="deleteCampaign(${campaign.id})" class="text-gray-600 hover:text-gray-500 text-sm transition-colors duration-300">Delete</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function getStatusClass(status) {
            switch (status) {
                case 'draft': return 'bg-gray-100 text-gray-800';
                case 'scheduled': return 'bg-gray-200 text-gray-500';
                case 'sending': return 'bg-gray-200 text-gray-500';
                case 'sent': return 'bg-gray-200 text-gray-500';
                case 'cancelled': return 'bg-gray-200 text-gray-500';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        function renderPagination(pagination) {
            const paginationDiv = document.getElementById('pagination');
            
            if (!pagination || pagination.total_pages <= 1) {
                paginationDiv.innerHTML = '';
                return;
            }

            let paginationHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button onclick="changePage(${pagination.current_page - 1})" 
                            ${pagination.current_page <= 1 ? 'disabled' : ''}
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 ${pagination.current_page <= 1 ? 'opacity-50 cursor-not-allowed' : ''}">
                            Previous
                        </button>
                        <button onclick="changePage(${pagination.current_page + 1})" 
                            ${pagination.current_page >= pagination.total_pages ? 'disabled' : ''}
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 ${pagination.current_page >= pagination.total_pages ? 'opacity-50 cursor-not-allowed' : ''}">
                            Next
                        </button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-600">
                                Showing <span class="font-medium">${pagination.from}</span> to <span class="font-medium">${pagination.to}</span> of <span class="font-medium">${pagination.total}</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
            `;

            // Previous button
            paginationHTML += `
                <button onclick="changePage(${pagination.current_page - 1})" 
                    ${pagination.current_page <= 1 ? 'disabled' : ''}
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 ${pagination.current_page <= 1 ? 'opacity-50 cursor-not-allowed' : ''}">
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;

            // Page numbers
            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i === pagination.current_page) {
                    paginationHTML += `
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-50 text-sm font-medium text-gray-600">
                            ${i}
                        </span>
                    `;
                } else {
                    paginationHTML += `
                        <button onclick="changePage(${i})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            ${i}
                        </button>
                    `;
                }
            }

            // Next button
            paginationHTML += `
                <button onclick="changePage(${pagination.current_page + 1})" 
                    ${pagination.current_page >= pagination.total_pages ? 'disabled' : ''}
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 ${pagination.current_page >= pagination.total_pages ? 'opacity-50 cursor-not-allowed' : ''}">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;

            paginationHTML += `
                            </nav>
                        </div>
                    </div>
                </div>
            `;

            paginationDiv.innerHTML = paginationHTML;
        }

        function changePage(page) {
            currentPage = page;
            loadCampaigns();
        }

        // Modal functions
        function openCreateCampaignModal() {
            document.getElementById('create-campaign-modal').classList.remove('hidden');
        }

        function closeCreateCampaignModal() {
            document.getElementById('create-campaign-modal').classList.add('hidden');
            document.getElementById('campaign-form').reset();
            quill.setText('');
        }

        function openTemplatesModal() {
            document.getElementById('templates-modal').classList.remove('hidden');
            // Load templates when modal is opened
            loadTemplates();
        }

        function closeTemplatesModal() {
            document.getElementById('templates-modal').classList.add('hidden');
        }



        // Form handling
        function updateTargetAudience() {
            const targetType = document.querySelector('input[name="target_type"]:checked').value;
            
            document.getElementById('tag-selection').classList.add('hidden');
            document.getElementById('custom-segment-options').classList.add('hidden');
            
            if (targetType === 'tags') {
                document.getElementById('tag-selection').classList.remove('hidden');
            } else if (targetType === 'custom') {
                document.getElementById('custom-segment-options').classList.remove('hidden');
            }
            
            previewRecipients();
        }

        function updateSendOptions() {
            const sendType = document.querySelector('input[name="send_type"]:checked').value;
            
            if (sendType === 'later') {
                document.getElementById('schedule-options').classList.remove('hidden');
            } else {
                document.getElementById('schedule-options').classList.add('hidden');
            }
        }

        function loadTags() {
            fetch('api/tags.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const container = document.getElementById('tags-container');
                        container.innerHTML = data.data.map(tag => `
                            <label class="flex items-center">
                                <input type="checkbox" name="tags[]" value="${tag.id}" class="mr-2">
                                <span class="text-sm text-gray-600">${tag.name}</span>
                            </label>
                        `).join('');
                    }
                })
                .catch(error => console.error('Error loading tags:', error));
        }

        function loadTemplates() {
            console.log('Loading templates...');
            fetch('api/templates.php')
                .then(response => response.json())
                .then(data => {
                    console.log('Templates data received:', data);
                    if (data.status === 'success') {
                        const container = document.getElementById('templates-container');
                        if (container) {
                            if (data.data.length > 0) {
                                                container.innerHTML = data.data.map(template => `
                    <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors h-full flex flex-col" onclick="useTemplate(${template.id})">
                        <div class="flex items-start justify-between mb-3">
                            <h4 class="font-medium text-gray-900 text-sm lg:text-base">${template.name}</h4>
                            ${template.is_default ? '<span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded flex-shrink-0">Default</span>' : ''}
                        </div>
                        <p class="text-sm text-gray-600 flex-grow">${template.subject}</p>
                        <div class="text-xs text-gray-500 mt-2">
                            <p>Created: ${new Date(template.created_at).toLocaleDateString()}</p>
                        </div>
                    </div>
                `).join('');
                                console.log(`Loaded ${data.data.length} templates`);
                            } else {
                                container.innerHTML = `
                                    <div class="col-span-2 flex items-center justify-center p-8">
                                        <div class="text-center">
                                            <i class="fas fa-file-alt text-4xl text-gray-500 mb-4"></i>
                                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Templates Found</h4>
                                            <p class="text-gray-500">No email templates are available. Create your first template to get started.</p>
                                        </div>
                                    </div>
                                `;
                                console.log('No templates found');
                            }
                        } else {
                            console.error('Templates container not found');
                        }
                    } else {
                        console.error('Templates API returned error:', data);
                    }
                })
                .catch(error => {
                    console.error('Error loading templates:', error);
                });
        }



        function previewRecipients() {
            const formData = new FormData(document.getElementById('campaign-form'));
            
            fetch('api/campaigns.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'preview_recipients',
                    target_audience: Object.fromEntries(formData)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('recipient-count').innerHTML = 
                        `<strong>${data.count}</strong> recipients will receive this campaign`;
                } else {
                    document.getElementById('recipient-count').innerHTML = 
                        'Error calculating recipients';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('recipient-count').innerHTML = 
                    'Error calculating recipients';
            });
        }

        // Template selector functions
        function openTemplateSelector() {
            document.getElementById('template-selector-modal').classList.remove('hidden');
            loadPredefinedTemplates();
        }

        function closeTemplateSelector() {
            document.getElementById('template-selector-modal').classList.add('hidden');
        }

        function loadPredefinedTemplates() {
            fetch('api/predefined-templates.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const container = document.getElementById('template-selector-container');
                        container.innerHTML = data.templates.map(template => `
                            <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors h-full flex flex-col" onclick="selectPredefinedTemplate('${template.key}')">
                                <div class="flex items-start justify-between mb-3">
                                    <h4 class="font-medium text-gray-900 text-sm lg:text-base">${template.name}</h4>
                                    <span class="text-xs bg-gray-200 text-gray-500 px-2 py-1 rounded flex-shrink-0">Template</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-3 flex-grow">${template.subject}</p>
                                <div class="text-xs text-gray-500 mt-auto">
                                    <p><strong>Use for:</strong> ${template.description}</p>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        console.error('Error loading predefined templates:', data.message);
                    }
                })
                .catch(error => console.error('Error loading predefined templates:', error));
        }

        function selectPredefinedTemplate(templateKey) {
            fetch(`api/predefined-templates.php?template=${templateKey}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const template = data.template;
                        
                        // Update form fields
                        document.getElementById('campaign-subject').value = template.subject;
                        quill.root.innerHTML = template.content;
                        
                        // Show selected template info
                        document.getElementById('template-name').textContent = template.name;
                        document.getElementById('selected-template-info').classList.remove('hidden');
                        
                        // Close modal
                        closeTemplateSelector();
                        
                        showSuccess(`Template "${template.name}" loaded successfully`);
                    } else {
                        showError('Error loading template');
                    }
                })
                .catch(error => {
                    console.error('Error loading template:', error);
                    showError('Error loading template');
                });
        }

        function clearTemplate() {
            document.getElementById('campaign-subject').value = '';
            quill.root.innerHTML = '';
            document.getElementById('selected-template-info').classList.add('hidden');
            showSuccess('Template cleared');
        }

        function useTemplate(templateId) {
            fetch(`api/templates.php?id=${templateId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const template = data.data;
                        document.getElementById('campaign-subject').value = template.subject;
                        quill.root.innerHTML = template.content;
                        closeTemplatesModal();
                    }
                })
                .catch(error => console.error('Error loading template:', error));
        }

        // Campaign actions
        function saveDraft() {
            const formData = new FormData(document.getElementById('campaign-form'));
            formData.append('content', quill.root.innerHTML);
            formData.append('action', 'create');
            formData.append('status', 'draft');
            
            fetch('api/campaigns.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showSuccess('Draft saved successfully');
                    closeCreateCampaignModal();
                    loadCampaigns();
                } else {
                    showError(data.message || 'Error saving draft');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error saving draft');
            });
        }

        // Form submission
        document.getElementById('campaign-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('content', quill.root.innerHTML);
            formData.append('action', 'create');
            
            fetch('api/campaigns.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showSuccess('Campaign created successfully');
                    closeCreateCampaignModal();
                    loadCampaigns();
                } else {
                    showError(data.message || 'Error creating campaign');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error creating campaign');
            });
        });



        function viewCampaign(id) {
            window.location.href = `campaign-details.php?id=${id}`;
        }

        function editCampaign(id) {
            window.location.href = `edit-campaign.php?id=${id}`;
        }

        function sendCampaign(id) {
            if (!confirm('Are you sure you want to send this campaign? This action cannot be undone.')) return;

            fetch('api/campaigns.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'send',
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showSuccess('Campaign sent successfully');
                    loadCampaigns();
                } else {
                    showError(data.message || 'Error sending campaign');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error sending campaign');
            });
        }

        function deleteCampaign(id) {
            if (!confirm('Are you sure you want to delete this campaign? This action cannot be undone.')) return;

            fetch('api/campaigns.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete',
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showSuccess('Campaign deleted successfully');
                    loadCampaigns();
                } else {
                    showError(data.message || 'Error deleting campaign');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error deleting campaign');
            });
        }

        function showSuccess(message) {
            showToast(message, 'success');
        }

        function showError(message) {
            showToast(message, 'error');
        }
    </script>
    </div>

    <!-- Toast Notifications JS -->
    <script src="assets/toast-notifications.js"></script>
    
    <!-- Sidebar JavaScript -->
    <script src="includes/sidebar.js"></script>
</body>
</html>
