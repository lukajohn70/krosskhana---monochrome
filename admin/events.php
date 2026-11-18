<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();
$stats = [
    'total' => 0,
    'past' => 0,
    'upcoming' => 0,
    'active' => 0
];

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN event_type = 'past' THEN 1 END) as past,
            COUNT(CASE WHEN event_type = 'upcoming' THEN 1 END) as upcoming,
            COUNT(CASE WHEN is_active = 1 THEN 1 END) as active
            FROM events");
        $stats = $stmt->fetch() ?: $stats;
    } catch (PDOException $e) {
        error_log('Events stats error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management - Krosskhana Admin</title>
    <link rel="icon" type="image/png" href="../assets/logos/Krosskhana-05.png">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Toast Notifications CSS -->
    <link rel="stylesheet" href="assets/notifications.css">
    
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
        .event-card {
            transition: all 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-4px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-black to-gray-900 min-h-screen font-poppins">
    <?php $pageTitle = 'Events Management'; ?>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    
    <!-- Toast Container -->
    <div id="toast-container" class="toast-container"></div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Events -->
        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-r from-gray-700 to-gray-900 rounded-xl flex items-center justify-center shadow">
                            <i class="fas fa-calendar-alt text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Events</dt>
                            <dd id="stat-total" class="text-2xl font-bold text-gray-900"><?php echo $stats['total']; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50/80 px-5 py-2 border-t border-gray-100 text-xs text-gray-500">
                All created events
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-r from-emerald-600 to-teal-700 rounded-xl flex items-center justify-center shadow">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Events</dt>
                            <dd id="stat-upcoming" class="text-2xl font-bold text-emerald-600"><?php echo $stats['upcoming']; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50/80 px-5 py-2 border-t border-gray-100 text-xs text-gray-500">
                Next scheduled showcases
            </div>
        </div>

        <!-- Past Events -->
        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-r from-indigo-600 to-blue-700 rounded-xl flex items-center justify-center shadow">
                            <i class="fas fa-history text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Past Events</dt>
                            <dd id="stat-past" class="text-2xl font-bold text-indigo-600"><?php echo $stats['past']; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50/80 px-5 py-2 border-t border-gray-100 text-xs text-gray-500">
                Completed events & galleries
            </div>
        </div>

        <!-- Active Events -->
        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-pink-700 rounded-xl flex items-center justify-center shadow">
                            <i class="fas fa-eye text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active on Website</dt>
                            <dd id="stat-active" class="text-2xl font-bold text-purple-600"><?php echo $stats['active']; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50/80 px-5 py-2 border-t border-gray-100 text-xs text-gray-500">
                Visible to site visitors
            </div>
        </div>
    </div>

    <!-- Main Card & Toolbar -->
    <div class="bg-white/95 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 mb-8 overflow-hidden">
        <!-- Toolbar Header -->
        <div class="p-5 border-b border-gray-200/60 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="flex items-center space-x-2 bg-gray-100 p-1.5 rounded-xl border border-gray-200 self-start">
                <button onclick="setFilterType('all')" id="tab-all" class="filter-tab px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 bg-white text-gray-900 shadow-sm">
                    All Events <span id="badge-count-all" class="ml-1 px-1.5 py-0.5 rounded-full text-xs bg-gray-200 text-gray-800"><?php echo $stats['total']; ?></span>
                </button>
                <button onclick="setFilterType('upcoming')" id="tab-upcoming" class="filter-tab px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-clock mr-1 text-emerald-600"></i> Upcoming <span id="badge-count-upcoming" class="ml-1 px-1.5 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-800"><?php echo $stats['upcoming']; ?></span>
                </button>
                <button onclick="setFilterType('past')" id="tab-past" class="filter-tab px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-history mr-1 text-indigo-600"></i> Past Events <span id="badge-count-past" class="ml-1 px-1.5 py-0.5 rounded-full text-xs bg-indigo-100 text-indigo-800"><?php echo $stats['past']; ?></span>
                </button>
            </div>

            <!-- Search, Status filter, and Add button -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Search input -->
                <div class="relative flex-1 sm:w-64">
                    <input type="text" id="event-search" oninput="debounceSearch()" placeholder="Search events..." 
                           class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                </div>

                <!-- Status Filter -->
                <select id="event-status-filter" onchange="loadEvents()" 
                        class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                    <option value="all">All Status</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Hidden / Inactive</option>
                </select>

                <!-- Add Event Button -->
                <a href="event-edit.php" 
                   class="bg-gradient-to-r from-gray-900 to-black hover:from-gray-800 hover:to-gray-900 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg transition-all duration-300 flex items-center gap-2 transform hover:scale-[1.02]">
                    <i class="fas fa-plus"></i>
                    <span>Add New Event</span>
                </a>
            </div>
        </div>

        <!-- Events Grid Container -->
        <div class="p-6">
            <div id="events-loading" class="py-16 text-center">
                <i class="fas fa-circle-notch fa-spin text-3xl text-gray-400"></i>
                <p class="text-gray-500 mt-3 text-sm">Loading events...</p>
            </div>

            <div id="events-empty" class="hidden py-16 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <i class="fas fa-calendar-times text-2xl"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-800">No events found</h4>
                <p class="text-gray-500 text-sm mt-1 mb-4">No events match the current filter or search criteria.</p>
                <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-xl text-sm font-semibold hover:bg-gray-800">
                    <i class="fas fa-plus"></i> Create an Event
                </button>
            </div>

            <div id="events-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Dynamically populated -->
            </div>
        </div>
    </div>

    <!-- Event Add / Edit Modal -->
    <div id="event-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" onclick="closeEventModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <!-- Modal Dialog -->
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-gray-900 via-black to-gray-900 px-6 py-4 flex items-center justify-between text-white border-b border-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center">
                            <i id="modal-icon" class="fas fa-calendar-alt text-white"></i>
                        </div>
                        <div>
                            <h3 id="modal-title" class="text-lg font-bold font-orbitron">Add New Event</h3>
                            <p class="text-xs text-gray-400">Configure past or upcoming event details</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeEventModal()" class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Form -->
                <form id="event-form" onsubmit="handleEventSubmit(event)" enctype="multipart/form-data" class="p-6 space-y-5">
                    <input type="hidden" id="event-id" name="id" value="">
                    <input type="hidden" id="form-action" name="action" value="create">

                    <!-- Event Type Selector -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Event Classification *</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition hover:bg-gray-50 border-gray-300" id="label-type-upcoming">
                                <input type="radio" name="event_type" value="upcoming" id="type-upcoming" class="text-emerald-600 focus:ring-emerald-500" checked onchange="handleTypeChange('upcoming')">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900"><i class="fas fa-clock text-emerald-600 mr-1.5"></i>Upcoming Event</span>
                                    <span class="block text-xs text-gray-500">Scheduled in the future / Coming soon</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition hover:bg-gray-50 border-gray-300" id="label-type-past">
                                <input type="radio" name="event_type" value="past" id="type-past" class="text-indigo-600 focus:ring-indigo-500" onchange="handleTypeChange('past')">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900"><i class="fas fa-history text-indigo-600 mr-1.5"></i>Past Event</span>
                                    <span class="block text-xs text-gray-500">Completed showcase / View gallery</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Title & Slug -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="event-title-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Event Title *</label>
                            <input type="text" id="event-title-input" name="title" required placeholder="e.g. Avant Gardex 2026"
                                   oninput="handleTitleInput(this.value)"
                                   class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-700">
                        </div>

                        <div>
                            <label for="event-slug-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Slug / Identifier</label>
                            <input type="text" id="event-slug-input" name="slug" placeholder="e.g. avant-gardex-2026"
                                   class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-700">
                            <span class="text-[11px] text-gray-400">Used for gallery modal IDs and anchor tags</span>
                        </div>
                    </div>

                    <!-- Date text, Date picker, and Badge text -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="event-date-text-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Date Display Text *</label>
                            <input type="text" id="event-date-text-input" name="event_date_text" required placeholder="e.g. March 2026 or Nov 25, 2026"
                                   class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-700">
                        </div>

                        <div>
                            <label for="event-date-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Calendar Date (for sorting)</label>
                            <input type="date" id="event-date-input" name="event_date"
                                   class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-700">
                        </div>

                        <div>
                            <label for="event-badge-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Badge Text</label>
                            <input type="text" id="event-badge-input" name="badge_text" placeholder="Coming Soon / Past Event / Sold Out"
                                   class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-700">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="event-desc-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Description *</label>
                        <textarea id="event-desc-input" name="description" rows="3" required placeholder="Describe the event highlight, experience, or activities..."
                                  class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-700"></textarea>
                    </div>

                    <!-- Visual Header: Image Upload or Theme Gradient -->
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200 space-y-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Header Banner & Visual Style</label>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Image Upload / Presets -->
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Upload Banner Image (Optional)</label>
                                <input type="file" id="event-file-input" name="image_file" accept="image/*" onchange="previewImageUpload(this)"
                                       class="w-full text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-gray-700">
                                
                                <div class="mt-2">
                                    <label for="event-image-url-input" class="block text-xs text-gray-600 mb-1">Or Image Path / Preset</label>
                                    <input type="text" id="event-image-url-input" name="image_url" placeholder="pictures/1.png or https://..."
                                           oninput="handleImageUrlInput(this.value)"
                                           class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-gray-700">
                                    <div class="flex items-center gap-1.5 mt-1 text-[11px] text-gray-500">
                                        <span>Presets:</span>
                                        <button type="button" onclick="selectPresetImage('pictures/1.png')" class="underline hover:text-black">Pic 1</button>
                                        <button type="button" onclick="selectPresetImage('pictures/2.png')" class="underline hover:text-black">Pic 2</button>
                                        <button type="button" onclick="selectPresetImage('pictures/3.png')" class="underline hover:text-black">Pic 3</button>
                                        <button type="button" onclick="selectPresetImage('pictures/4.png')" class="underline hover:text-black">Pic 4</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Gradient Theme & Image Preview -->
                            <div>
                                <label for="event-gradient-select" class="block text-xs text-gray-600 mb-1">Signature Metallic Gradient</label>
                                <select id="event-gradient-select" name="gradient_theme" onchange="updatePreviewBanner()"
                                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-gray-700">
                                    <option value="from-white to-gray-300">White to Light Platinum (Signature)</option>
                                    <option value="from-gray-300 to-white">Light Platinum to White</option>
                                    <option value="from-gray-800 to-gray-950">Carbon Dark Slate</option>
                                    <option value="from-amber-600 to-yellow-500">Motorsport Gold Accent</option>
                                    <option value="from-red-700 to-rose-900">Racing Crimson Accent</option>
                                </select>

                                <!-- Live Banner Preview Box -->
                                <div class="mt-2.5">
                                    <span class="block text-[11px] text-gray-500 mb-1">Header Card Preview:</span>
                                    <div id="banner-preview-box" class="h-20 rounded-xl relative overflow-hidden bg-gradient-to-br from-white to-gray-300 flex items-end p-2 border border-gray-300">
                                        <img id="preview-image" src="" alt="Banner Preview" class="absolute inset-0 w-full h-full object-cover hidden">
                                        <div id="preview-overlay" class="absolute inset-0 bg-black/25 hidden"></div>
                                        <div class="relative z-10">
                                            <span id="preview-badge" class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-700 text-white">Coming Soon</span>
                                            <h5 id="preview-title" class="text-xs font-bold text-black truncate">Event Title</h5>
                                        </div>
                                        <button type="button" id="btn-remove-image" onclick="removeImageSelection()" class="hidden absolute top-1 right-1 bg-red-600/80 hover:bg-red-600 text-white text-[10px] px-1.5 py-0.5 rounded shadow z-20">
                                            <i class="fas fa-trash mr-1"></i>Clear Image
                                        </button>
                                    </div>
                                    <input type="hidden" id="remove-image-flag" name="remove_image" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Button Action & Link -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="event-button-text-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Action Button Text</label>
                            <input type="text" id="event-button-text-input" name="button_text" placeholder="e.g. View Gallery or Learn More"
                                   class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-700">
                        </div>

                        <div>
                            <label for="event-button-link-input" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Button Link / Action</label>
                            <input type="text" id="event-button-link-input" name="button_link" placeholder="Leave blank for gallery popup, or https://... / #register"
                                   class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-700">
                            <span class="text-[11px] text-gray-400">If blank, clicking will trigger the photo gallery popup</span>
                        </div>
                    </div>

                    <!-- Sort order & Active toggle -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2 border-t border-gray-200">
                        <div class="flex items-center gap-3">
                            <label for="event-sort-order" class="text-xs font-bold uppercase tracking-wider text-gray-700">Display Order:</label>
                            <input type="number" id="event-sort-order" name="sort_order" value="1" min="0" max="999"
                                   class="w-20 px-2 py-1 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-700">
                            <span class="text-xs text-gray-400">Lower numbers appear first</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="event-is-active" name="is_active" value="1" checked
                                   class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-700">
                            <label for="event-is-active" class="text-sm font-semibold text-gray-800">Publish immediately (Visible on website)</label>
                        </div>
                    </div>

                    <!-- Form Footer Buttons -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeEventModal()" 
                                class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-medium text-sm hover:bg-gray-100 transition">
                            Cancel
                        </button>
                        <button type="submit" id="btn-save-event"
                                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-gray-900 to-black hover:from-gray-800 hover:to-gray-900 text-white font-semibold text-sm shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            <span id="btn-save-text">Save Event</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 text-xl">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">Delete Event</h4>
                            <p class="text-xs text-gray-500 mt-1">Are you sure you want to delete <strong id="delete-event-title" class="text-gray-800">this event</strong>? This action cannot be undone.</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="button" id="confirm-delete-btn" onclick="executeDelete()" class="px-5 py-2 bg-red-600 text-white rounded-xl text-sm font-semibold hover:bg-red-700 shadow-md">
                            <i class="fas fa-trash-alt mr-1.5"></i> Yes, Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifications JS -->
    <script src="assets/toast-notifications.js"></script>
    
    <!-- Sidebar JavaScript -->
    <script src="includes/sidebar.js"></script>

    <!-- Page JavaScript Logic -->
    <script>
        let currentFilterType = 'all';
        let currentSearchQuery = '';
        let searchDebounceTimeout = null;
        let allEvents = [];
        let eventPendingDeleteId = null;

        // Initialize on DOM load
        document.addEventListener('DOMContentLoaded', () => {
            loadEvents();
        });

        // Debounced search
        function debounceSearch() {
            clearTimeout(searchDebounceTimeout);
            searchDebounceTimeout = setTimeout(() => {
                currentSearchQuery = document.getElementById('event-search').value.trim();
                loadEvents();
            }, 300);
        }

        // Set filter tab
        function setFilterType(type) {
            currentFilterType = type;
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.className = 'filter-tab px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 text-gray-600 hover:text-gray-900';
            });
            const activeTab = document.getElementById(`tab-${type}`);
            if (activeTab) {
                activeTab.className = 'filter-tab px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 bg-white text-gray-900 shadow-sm';
            }
            loadEvents();
        }

        // Fetch events from API
        function loadEvents() {
            const statusFilter = document.getElementById('event-status-filter').value;
            const params = new URLSearchParams({
                type: currentFilterType,
                status: statusFilter,
                search: currentSearchQuery
            });

            document.getElementById('events-loading').classList.remove('hidden');
            document.getElementById('events-empty').classList.add('hidden');
            document.getElementById('events-grid').classList.add('hidden');

            fetch(`api/events.php?${params}`)
                .then(res => {
                    if (!res.ok) throw new Error('Network error: ' + res.status);
                    return res.json();
                })
                .then(data => {
                    document.getElementById('events-loading').classList.add('hidden');
                    if (data.status === 'success') {
                        allEvents = data.data;
                        updateStatsDisplay(data.stats);
                        renderEvents(data.data);
                    } else {
                        showToast(data.message || 'Failed to load events', 'error');
                    }
                })
                .catch(err => {
                    document.getElementById('events-loading').classList.add('hidden');
                    console.error('Error fetching events:', err);
                    showToast('Failed to load events from server', 'error');
                });
        }

        // Update stats
        function updateStatsDisplay(stats) {
            if (!stats) return;
            document.getElementById('stat-total').innerText = stats.total;
            document.getElementById('stat-upcoming').innerText = stats.upcoming_count;
            document.getElementById('stat-past').innerText = stats.past_count;
            document.getElementById('stat-active').innerText = stats.active_count;

            document.getElementById('badge-count-all').innerText = stats.total;
            document.getElementById('badge-count-upcoming').innerText = stats.upcoming_count;
            document.getElementById('badge-count-past').innerText = stats.past_count;
        }

        // Render event cards
        function renderEvents(events) {
            const grid = document.getElementById('events-grid');
            const emptyState = document.getElementById('events-empty');

            if (!events || events.length === 0) {
                grid.innerHTML = '';
                grid.classList.add('hidden');
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            grid.classList.remove('hidden');

            grid.innerHTML = events.map(ev => {
                const isUpcoming = ev.event_type === 'upcoming';
                const typeBadgeClass = isUpcoming 
                    ? 'bg-emerald-600 text-white' 
                    : 'bg-indigo-600 text-white';
                const typeIcon = isUpcoming ? 'fa-clock' : 'fa-history';
                const typeLabel = isUpcoming ? 'Upcoming' : 'Past Event';

                const statusBadge = ev.is_active == 1
                    ? '<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-800 border border-green-200"><i class="fas fa-check-circle mr-1"></i>Active</span>'
                    : '<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-200 text-gray-700 border border-gray-300"><i class="fas fa-eye-slash mr-1"></i>Hidden</span>';

                const toggleTypeActionLabel = isUpcoming ? 'Move to Past' : 'Move to Upcoming';
                const toggleTypeIcon = isUpcoming ? 'fa-history' : 'fa-arrow-up';

                // Header visual: Image or metallic gradient
                let headerStyle = '';
                let headerContent = '';

                if (ev.image_url && ev.image_url.trim() !== '') {
                    headerContent = `
                        <img src="../${escapeHtml(ev.image_url)}" alt="${escapeHtml(ev.title)}" class="absolute inset-0 w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20"></div>
                        <div class="absolute top-3 left-3 z-10 flex items-center gap-1.5">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold shadow-md ${typeBadgeClass}">
                                <i class="fas ${typeIcon} mr-1"></i>${typeLabel}
                            </span>
                        </div>
                        <div class="absolute top-3 right-3 z-10">
                            ${statusBadge}
                        </div>
                        <div class="absolute bottom-3 left-3 right-3 z-10 text-white">
                            <span class="text-xs font-semibold text-gray-300">${escapeHtml(ev.badge_text || '')}</span>
                            <h4 class="text-lg font-bold truncate drop-shadow">${escapeHtml(ev.title)}</h4>
                            <p class="text-xs text-gray-300 flex items-center gap-1 mt-0.5">
                                <i class="fas fa-calendar-day text-[11px]"></i> ${escapeHtml(ev.event_date_text)}
                            </p>
                        </div>
                    `;
                } else {
                    const gradientClass = ev.gradient_theme || 'from-white to-gray-300';
                    const textColor = gradientClass.includes('from-gray-800') || gradientClass.includes('from-red-700') ? 'text-white' : 'text-gray-900';
                    headerContent = `
                        <div class="absolute inset-0 bg-gradient-to-br ${escapeHtml(gradientClass)}"></div>
                        <div class="absolute top-3 left-3 z-10 flex items-center gap-1.5">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold shadow-md ${typeBadgeClass}">
                                <i class="fas ${typeIcon} mr-1"></i>${typeLabel}
                            </span>
                        </div>
                        <div class="absolute top-3 right-3 z-10">
                            ${statusBadge}
                        </div>
                        <div class="absolute bottom-3 left-3 right-3 z-10 ${textColor}">
                            <span class="text-xs font-semibold uppercase tracking-wider opacity-80">${escapeHtml(ev.badge_text || '')}</span>
                            <h4 class="text-lg font-bold truncate">${escapeHtml(ev.title)}</h4>
                            <p class="text-xs opacity-80 flex items-center gap-1 mt-0.5">
                                <i class="fas fa-calendar-day text-[11px]"></i> ${escapeHtml(ev.event_date_text)}
                            </p>
                        </div>
                    `;
                }

                return `
                    <div class="event-card bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-200/80 flex flex-col justify-between">
                        <!-- Card Header -->
                        <div class="relative h-44 overflow-hidden">
                            ${headerContent}
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <p class="text-gray-600 text-xs md:text-sm leading-relaxed mb-4 line-clamp-3">
                                    ${escapeHtml(ev.description)}
                                </p>

                                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 mb-4 pt-3 border-t border-gray-100">
                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-[11px] font-medium text-gray-700">
                                        <i class="fas fa-sort mr-1"></i>Order: ${ev.sort_order}
                                    </span>
                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-[11px] font-medium text-gray-700 truncate max-w-[150px]">
                                        <i class="fas fa-mouse-pointer mr-1"></i>${escapeHtml(ev.button_text || 'View Gallery')}
                                    </span>
                                    ${ev.button_link ? `<span class="bg-gray-100 px-2 py-0.5 rounded text-[11px] font-medium text-gray-700 truncate max-w-[140px]"><i class="fas fa-link mr-1"></i>${escapeHtml(ev.button_link)}</span>` : ''}
                                </div>
                            </div>

                            <!-- Card Actions -->
                            <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5">
                                    <!-- Edit Button -->
                                    <a href="event-edit.php?id=${ev.id}" class="px-3 py-1.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-semibold rounded-lg transition shadow-sm flex items-center gap-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <!-- Quick Switch Type (Past <-> Upcoming) -->
                                    <button onclick="toggleEventType(${ev.id})" title="${toggleTypeActionLabel}" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-medium rounded-lg transition flex items-center gap-1 border border-gray-200">
                                        <i class="fas ${toggleTypeIcon}"></i> <span class="hidden sm:inline">${isUpcoming ? 'Make Past' : 'Make Upcoming'}</span>
                                    </button>
                                </div>

                                <div class="flex items-center gap-1">
                                    <!-- Visibility Toggle -->
                                    <button onclick="toggleEventStatus(${ev.id})" title="${ev.is_active == 1 ? 'Hide on website' : 'Show on website'}" class="p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition text-xs">
                                        <i class="fas ${ev.is_active == 1 ? 'fa-eye' : 'fa-eye-slash'}"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button onclick="openDeleteModal(${ev.id}, '${escapeJs(ev.title)}')" title="Delete event" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition text-xs">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Open modal for adding
        function openCreateModal() {
            document.getElementById('event-form').reset();
            document.getElementById('event-id').value = '';
            document.getElementById('form-action').value = 'create';
            document.getElementById('modal-title').innerText = 'Add New Event';
            document.getElementById('modal-icon').className = 'fas fa-calendar-plus text-white';
            document.getElementById('btn-save-text').innerText = 'Create Event';
            document.getElementById('event-is-active').checked = true;
            document.getElementById('remove-image-flag').value = '0';

            // Default to upcoming
            document.getElementById('type-upcoming').checked = true;
            handleTypeChange('upcoming');

            document.getElementById('event-gradient-select').value = 'from-white to-gray-300';
            document.getElementById('event-sort-order').value = (allEvents.length + 1);

            removeImageSelection();
            updatePreviewBanner();

            document.getElementById('event-modal').classList.remove('hidden');
        }

        // Open modal for editing
        function openEditModal(eventId) {
            const ev = allEvents.find(e => e.id == eventId);
            if (!ev) {
                // Fetch individually
                fetch(`api/events.php?id=${eventId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            populateEditModal(data.data);
                        } else {
                            showToast(data.message || 'Error loading event', 'error');
                        }
                    });
                return;
            }
            populateEditModal(ev);
        }

        function populateEditModal(ev) {
            document.getElementById('event-form').reset();
            document.getElementById('event-id').value = ev.id;
            document.getElementById('form-action').value = 'update';
            document.getElementById('modal-title').innerText = 'Edit Event: ' + ev.title;
            document.getElementById('modal-icon').className = 'fas fa-edit text-white';
            document.getElementById('btn-save-text').innerText = 'Update Event';
            document.getElementById('remove-image-flag').value = '0';

            // Classification
            if (ev.event_type === 'past') {
                document.getElementById('type-past').checked = true;
                handleTypeChange('past', false);
            } else {
                document.getElementById('type-upcoming').checked = true;
                handleTypeChange('upcoming', false);
            }

            document.getElementById('event-title-input').value = ev.title || '';
            document.getElementById('event-slug-input').value = ev.slug || '';
            document.getElementById('event-date-text-input').value = ev.event_date_text || '';
            document.getElementById('event-date-input').value = ev.event_date || '';
            document.getElementById('event-badge-input').value = ev.badge_text || '';
            document.getElementById('event-desc-input').value = ev.description || '';
            document.getElementById('event-button-text-input').value = ev.button_text || '';
            document.getElementById('event-button-link-input').value = ev.button_link || '';
            document.getElementById('event-sort-order').value = ev.sort_order || 0;
            document.getElementById('event-is-active').checked = (ev.is_active == 1);

            document.getElementById('event-gradient-select').value = ev.gradient_theme || 'from-white to-gray-300';
            document.getElementById('event-image-url-input').value = ev.image_url || '';

            if (ev.image_url && ev.image_url.trim() !== '') {
                showPreviewImage('../' + ev.image_url);
            } else {
                removeImageSelection();
            }

            updatePreviewBanner();
            document.getElementById('event-modal').classList.remove('hidden');
        }

        function closeEventModal() {
            document.getElementById('event-modal').classList.add('hidden');
        }

        // Handle Type selection changes (upcoming vs past)
        function handleTypeChange(type, updateDefaults = true) {
            const upLabel = document.getElementById('label-type-upcoming');
            const pastLabel = document.getElementById('label-type-past');

            if (type === 'upcoming') {
                upLabel.classList.add('border-emerald-600', 'bg-emerald-50/50');
                upLabel.classList.remove('border-gray-300');
                pastLabel.classList.remove('border-indigo-600', 'bg-indigo-50/50');
                pastLabel.classList.add('border-gray-300');

                if (updateDefaults) {
                    if (!document.getElementById('event-badge-input').value || document.getElementById('event-badge-input').value === 'Past Event') {
                        document.getElementById('event-badge-input').value = 'Coming Soon';
                    }
                    if (!document.getElementById('event-button-text-input').value || document.getElementById('event-button-text-input').value === 'View Gallery') {
                        document.getElementById('event-button-text-input').value = 'Learn More';
                    }
                }
            } else {
                pastLabel.classList.add('border-indigo-600', 'bg-indigo-50/50');
                pastLabel.classList.remove('border-gray-300');
                upLabel.classList.remove('border-emerald-600', 'bg-emerald-50/50');
                upLabel.classList.add('border-gray-300');

                if (updateDefaults) {
                    if (!document.getElementById('event-badge-input').value || document.getElementById('event-badge-input').value === 'Coming Soon') {
                        document.getElementById('event-badge-input').value = 'Past Event';
                    }
                    if (!document.getElementById('event-button-text-input').value || document.getElementById('event-button-text-input').value === 'Learn More') {
                        document.getElementById('event-button-text-input').value = 'View Gallery';
                    }
                }
            }
            updatePreviewBanner();
        }

        // Live title input
        function handleTitleInput(val) {
            const slugInput = document.getElementById('event-slug-input');
            if (document.getElementById('form-action').value === 'create' || !slugInput.value) {
                slugInput.value = generateSlug(val);
            }
            updatePreviewBanner();
        }

        function generateSlug(text) {
            return text.toString().toLowerCase()
                .trim()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        }

        // Image upload preview
        function previewImageUpload(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    showPreviewImage(e.target.result);
                    document.getElementById('remove-image-flag').value = '0';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function handleImageUrlInput(val) {
            if (val && val.trim() !== '') {
                const url = val.startsWith('http') || val.startsWith('/') ? val : '../' + val;
                showPreviewImage(url);
                document.getElementById('remove-image-flag').value = '0';
            } else {
                removeImageSelection();
            }
        }

        function selectPresetImage(path) {
            document.getElementById('event-image-url-input').value = path;
            showPreviewImage('../' + path);
            document.getElementById('remove-image-flag').value = '0';
        }

        function showPreviewImage(src) {
            const img = document.getElementById('preview-image');
            img.src = src;
            img.classList.remove('hidden');
            document.getElementById('preview-overlay').classList.remove('hidden');
            document.getElementById('btn-remove-image').classList.remove('hidden');
            document.getElementById('preview-title').classList.remove('text-black');
            document.getElementById('preview-title').classList.add('text-white');
        }

        function removeImageSelection() {
            document.getElementById('event-file-input').value = '';
            document.getElementById('event-image-url-input').value = '';
            document.getElementById('preview-image').src = '';
            document.getElementById('preview-image').classList.add('hidden');
            document.getElementById('preview-overlay').classList.add('hidden');
            document.getElementById('btn-remove-image').classList.add('hidden');
            document.getElementById('remove-image-flag').value = '1';
            updatePreviewBanner();
        }

        function updatePreviewBanner() {
            const title = document.getElementById('event-title-input').value || 'Event Title';
            const badge = document.getElementById('event-badge-input').value || (document.getElementById('type-past').checked ? 'Past Event' : 'Coming Soon');
            const gradient = document.getElementById('event-gradient-select').value;
            const hasImage = !document.getElementById('preview-image').classList.contains('hidden');

            document.getElementById('preview-title').innerText = title;
            document.getElementById('preview-badge').innerText = badge;

            const box = document.getElementById('banner-preview-box');
            // Remove previous gradient classes
            box.className = `h-20 rounded-xl relative overflow-hidden bg-gradient-to-br ${gradient} flex items-end p-2 border border-gray-300`;

            if (hasImage) {
                document.getElementById('preview-title').className = 'text-xs font-bold text-white truncate drop-shadow';
            } else {
                const isDarkGrad = gradient.includes('from-gray-800') || gradient.includes('from-red-700');
                document.getElementById('preview-title').className = `text-xs font-bold ${isDarkGrad ? 'text-white' : 'text-black'} truncate`;
            }
        }

        // Form Submit
        function handleEventSubmit(e) {
            e.preventDefault();
            const form = document.getElementById('event-form');
            const formData = new FormData(form);
            const btnSave = document.getElementById('btn-save-event');
            const originalText = document.getElementById('btn-save-text').innerText;

            btnSave.disabled = true;
            document.getElementById('btn-save-text').innerText = 'Saving...';

            fetch('api/events.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btnSave.disabled = false;
                document.getElementById('btn-save-text').innerText = originalText;

                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    closeEventModal();
                    loadEvents();
                } else {
                    showToast(data.message || 'Operation failed', 'error');
                }
            })
            .catch(err => {
                btnSave.disabled = false;
                document.getElementById('btn-save-text').innerText = originalText;
                console.error('Submit error:', err);
                showToast('Failed to save event', 'error');
            });
        }

        // Toggle Status (Active / Inactive)
        function toggleEventStatus(id) {
            fetch('api/events.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'toggle_status', id: id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    loadEvents();
                } else {
                    showToast(data.message || 'Failed to toggle status', 'error');
                }
            })
            .catch(err => {
                showToast('Network error toggling status', 'error');
            });
        }

        // Quick Toggle Event Type (Upcoming <-> Past)
        function toggleEventType(id) {
            fetch('api/events.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'toggle_type', id: id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    loadEvents();
                } else {
                    showToast(data.message || 'Failed to move event', 'error');
                }
            })
            .catch(err => {
                showToast('Network error moving event', 'error');
            });
        }

        // Delete Modal
        function openDeleteModal(id, title) {
            eventPendingDeleteId = id;
            document.getElementById('delete-event-title').innerText = `"${title}"`;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            eventPendingDeleteId = null;
            document.getElementById('delete-modal').classList.add('hidden');
        }

        function executeDelete() {
            if (!eventPendingDeleteId) return;
            const btn = document.getElementById('confirm-delete-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-1"></i> Deleting...';

            fetch('api/events.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', id: eventPendingDeleteId })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash-alt mr-1.5"></i> Yes, Delete';
                closeDeleteModal();

                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    loadEvents();
                } else {
                    showToast(data.message || 'Failed to delete event', 'error');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash-alt mr-1.5"></i> Yes, Delete';
                closeDeleteModal();
                showToast('Network error deleting event', 'error');
            });
        }

        // Utilities
        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function escapeJs(str) {
            if (!str) return '';
            return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
        }
    </script>
</body>
</html>
