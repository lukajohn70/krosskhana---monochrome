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
    'active' => 0,
    'hidden' => 0,
    'slide_1' => 0,
    'slide_2' => 0
];

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN is_active = 1 THEN 1 END) as active,
            COUNT(CASE WHEN is_active = 0 THEN 1 END) as hidden,
            COUNT(CASE WHEN slide_number = 1 THEN 1 END) as slide_1,
            COUNT(CASE WHEN slide_number = 2 THEN 1 END) as slide_2
            FROM explore_items");
        $stats = $stmt->fetch() ?: $stats;
    } catch (PDOException $e) {
        error_log('Explore stats error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Section Management - Krosskhana Admin</title>
    <link rel="icon" type="image/png" href="../assets/logos/Krosskhana-05.png">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
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
                        orbitron: ['Orbitron', 'sans-serif']
                    },
                    colors: {
                        dark: {
                            50: '#f8fafc',
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-950 text-white font-poppins min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation -->
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <!-- Top Bar -->
            <header class="border-b border-gray-800 bg-dark-900/60 backdrop-blur-md sticky top-0 z-20 px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-compass text-gray-400"></i> Explore Section Management
                    </h1>
                    <p class="text-xs text-gray-400">Manage 'EXPLORE THE WORLD OF KROSSKHANA' cards and hover showcases</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="openCreateModal()" class="px-4 py-2 bg-gradient-to-r from-white to-gray-200 text-black font-semibold rounded-xl text-xs hover:from-gray-100 hover:to-gray-300 transition shadow-lg flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Explore Card
                    </button>
                </div>
            </header>

            <main class="p-6 space-y-6 max-w-7xl mx-auto w-full">
                <!-- Toast Notification -->
                <div id="toast" class="hidden fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 transition-all duration-300"></div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-dark-900/60 border border-gray-800 rounded-2xl p-4 backdrop-blur-xl flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Total Cards</span>
                            <div class="text-xl font-bold text-white mt-1" id="stat-total"><?php echo $stats['total']; ?></div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-gray-800/80 flex items-center justify-center text-gray-300 border border-gray-700/50">
                            <i class="fas fa-th-large"></i>
                        </div>
                    </div>
                    <div class="bg-dark-900/60 border border-gray-800 rounded-2xl p-4 backdrop-blur-xl flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-medium text-emerald-400 uppercase tracking-wider">Active</span>
                            <div class="text-xl font-bold text-emerald-400 mt-1" id="stat-active"><?php echo $stats['active']; ?></div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-950/40 flex items-center justify-center text-emerald-400 border border-emerald-800/50">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="bg-dark-900/60 border border-gray-800 rounded-2xl p-4 backdrop-blur-xl flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Slide 1 Cards</span>
                            <div class="text-xl font-bold text-white mt-1" id="stat-slide1"><?php echo $stats['slide_1']; ?></div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-gray-800/80 flex items-center justify-center text-gray-300 border border-gray-700/50">
                            <span class="text-xs font-bold font-orbitron">S1</span>
                        </div>
                    </div>
                    <div class="bg-dark-900/60 border border-gray-800 rounded-2xl p-4 backdrop-blur-xl flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Slide 2 Cards</span>
                            <div class="text-xl font-bold text-white mt-1" id="stat-slide2"><?php echo $stats['slide_2']; ?></div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-gray-800/80 flex items-center justify-center text-gray-300 border border-gray-700/50">
                            <span class="text-xs font-bold font-orbitron">S2</span>
                        </div>
                    </div>
                </div>

                <!-- Explore Items Grid -->
                <div class="bg-dark-900/60 border border-gray-800 rounded-2xl p-6 backdrop-blur-xl">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-layer-group text-gray-400"></i> Cards on Homepage
                        </h2>
                        <span class="text-xs text-gray-500">Each slide holds 3 cards on the desktop carousel</span>
                    </div>

                    <!-- Loading State -->
                    <div id="loading-spinner" class="py-16 text-center text-gray-400">
                        <i class="fas fa-circle-notch fa-spin text-3xl mb-3 text-white"></i>
                        <p class="text-sm">Loading explore items...</p>
                    </div>

                    <!-- Empty State -->
                    <div id="empty-state" class="hidden py-16 text-center text-gray-400">
                        <div class="w-16 h-16 rounded-full bg-gray-800 flex items-center justify-center mx-auto mb-4 text-gray-500 text-2xl">
                            <i class="fas fa-compass"></i>
                        </div>
                        <h3 class="text-base font-semibold text-white">No explore cards yet</h3>
                        <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1 mb-4">Click below to add your first explore card.</p>
                        <button onclick="openCreateModal()" class="px-4 py-2 bg-gradient-to-r from-white to-gray-200 text-black font-semibold rounded-xl text-xs hover:from-gray-100 hover:to-gray-300 transition">
                            <i class="fas fa-plus mr-1"></i> Add Explore Card
                        </button>
                    </div>

                    <!-- Cards Grid -->
                    <div id="explore-grid" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add / Edit Modal -->
    <div id="explore-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-900 border border-gray-800 rounded-2xl max-w-2xl w-full p-6 shadow-2xl relative my-8">
            <div class="flex items-center justify-between pb-4 border-b border-gray-800 mb-6">
                <h3 id="modal-title" class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fas fa-plus text-gray-400"></i> Add Explore Card
                </h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-white transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form id="explore-form" onsubmit="handleExploreSubmit(event)" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" id="form-action" value="create">
                <input type="hidden" name="id" id="explore-id" value="">
                <input type="hidden" name="remove_image" id="remove-image-flag" value="0">
                <input type="hidden" name="modal_image_url" id="inp-modal-image-url" value="">

                <!-- Title & Slide -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Card Heading <span class="text-red-400">*</span></label>
                        <input type="text" name="title" id="inp-title" required placeholder="e.g. Autocross & Drifting Competitions" class="w-full bg-dark-950 border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-gray-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Carousel Slide</label>
                        <select name="slide_number" id="inp-slide" class="w-full bg-dark-950 border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-gray-500">
                            <option value="1">Slide 1 (First 3 cards)</option>
                            <option value="2">Slide 2 (Next 3 cards)</option>
                        </select>
                    </div>
                </div>

                <!-- Main Card Description -->
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Card Text (Front Display) <span class="text-red-400">*</span></label>
                    <textarea name="description" id="inp-desc" rows="2" required placeholder="Experience the raw thrill and precision of authentic racing..." class="w-full bg-dark-950 border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-gray-500"></textarea>
                </div>

                <!-- Hover Overlay Showcase Section -->
                <div class="p-4 bg-dark-950/60 rounded-xl border border-gray-800 space-y-4">
                    <h4 class="text-xs font-semibold text-white uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-eye text-gray-400"></i> Hover Showcase Popup Details
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1">Hover Title</label>
                            <input type="text" name="modal_title" id="inp-modal-title" placeholder="e.g. Racing Excellence" class="w-full bg-dark-900 border border-gray-800 rounded-xl px-3 py-2 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1">Hover Subtitle / Tag</label>
                            <input type="text" name="subtitle" id="inp-subtitle" placeholder="e.g. Racing Action" class="w-full bg-dark-900 border border-gray-800 rounded-xl px-3 py-2 text-xs text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Hover Description</label>
                        <textarea name="modal_description" id="inp-modal-desc" rows="2" placeholder="Longer description shown on hover..." class="w-full bg-dark-900 border border-gray-800 rounded-xl px-3 py-2 text-xs text-white"></textarea>
                    </div>

                    <!-- Showcase Image -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Hover Image / Poster</label>
                        <div class="border border-dashed border-gray-800 rounded-xl p-3 text-center cursor-pointer hover:border-gray-600 transition bg-dark-900/60" onclick="document.getElementById('file-input').click()">
                            <input type="file" name="image_file" id="file-input" accept="image/*" class="hidden" onchange="previewImage(this)">
                            <div id="upload-placeholder" class="py-2">
                                <i class="fas fa-image text-gray-500 text-lg mb-1"></i>
                                <p class="text-[11px] text-gray-400">Click to upload showcase photo (e.g. pictures/1.png)</p>
                            </div>
                            <div id="upload-preview-box" class="hidden items-center justify-between gap-3 p-1">
                                <img id="preview-img" src="" class="h-16 w-24 object-cover rounded-lg border border-gray-700">
                                <span class="text-xs text-gray-300 truncate" id="preview-filename">Image selected</span>
                                <button type="button" onclick="clearImage(event)" class="text-xs text-red-400 hover:text-red-300"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Icon and Gradient Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Front Card Icon</label>
                        <select name="icon_class" id="inp-icon" class="w-full bg-dark-950 border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-gray-500">
                            <option value="fas fa-car-side">Race Car (fas fa-car-side)</option>
                            <option value="fas fa-microscope">Innovation / Tech (fas fa-microscope)</option>
                            <option value="fas fa-music">Music / Entertainment (fas fa-music)</option>
                            <option value="fas fa-tshirt">Fashion / Apparel (fas fa-tshirt)</option>
                            <option value="fas fa-heart">Heart / Loyalty (fas fa-heart)</option>
                            <option value="fas fa-crown">Crown / Premium (fas fa-crown)</option>
                            <option value="fas fa-flag-checkered">Checkered Flag (fas fa-flag-checkered)</option>
                            <option value="fas fa-trophy">Trophy (fas fa-trophy)</option>
                            <option value="fas fa-bolt">High Voltage / Drift (fas fa-bolt)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Card Gradient Styling</label>
                        <select name="gradient_theme" id="inp-gradient" class="w-full bg-dark-950 border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-gray-500">
                            <option value="from-white to-gray-300">White &rarr; Gray (Silver Accent)</option>
                            <option value="from-gray-300 to-white">Gray &rarr; White (Reverse Silver)</option>
                            <option value="from-gray-700 to-gray-900">Carbon Gray</option>
                        </select>
                    </div>
                </div>

                <!-- Order and Active -->
                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Display Priority Order</label>
                        <input type="number" name="sort_order" id="inp-sort" value="1" class="w-full bg-dark-950 border border-gray-800 rounded-xl px-3 py-2 text-xs text-white">
                        <span class="text-[10px] text-gray-500">1 to 6</span>
                    </div>
                    <div class="flex items-center justify-between pt-5">
                        <span class="text-xs font-semibold text-white">Visible on Website</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" id="inp-active" value="1" checked class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-800 mt-6">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-700 hover:border-gray-600 text-gray-300 rounded-xl text-xs font-medium hover:bg-gray-800/40 transition">
                        Cancel
                    </button>
                    <button type="submit" id="btn-submit" class="px-5 py-2 bg-gradient-to-r from-white to-gray-200 text-black font-semibold rounded-xl text-xs hover:from-gray-100 hover:to-gray-300 transition shadow">
                        <span id="btn-submit-text">Save Explore Card</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-900 border border-gray-800 rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center">
            <div class="w-12 h-12 rounded-full bg-red-950/50 text-red-400 flex items-center justify-center mx-auto mb-3 border border-red-800/40 text-xl">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h4 class="text-base font-bold text-white mb-1">Delete Explore Card</h4>
            <p class="text-xs text-gray-400 mb-6">Are you sure you want to remove <strong id="delete-name" class="text-white">this card</strong>?</p>
            <div class="flex items-center justify-center gap-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-700 text-gray-300 rounded-xl text-xs">Cancel</button>
                <button type="button" id="confirm-delete-btn" onclick="executeDelete()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl text-xs shadow">Delete</button>
            </div>
        </div>
    </div>

    <script>
        let deleteItemId = null;

        function loadExplore() {
            document.getElementById('loading-spinner').classList.remove('hidden');
            document.getElementById('explore-grid').classList.add('hidden');
            document.getElementById('empty-state').classList.add('hidden');

            fetch('api/explore.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('loading-spinner').classList.add('hidden');
                    if (data.status === 'success') {
                        if (data.stats) {
                            document.getElementById('stat-total').innerText = data.stats.total;
                            document.getElementById('stat-active').innerText = data.stats.active_count;
                            document.getElementById('stat-slide1').innerText = data.stats.slide_1_count;
                            document.getElementById('stat-slide2').innerText = data.stats.slide_2_count;
                        }

                        if (!data.data || data.data.length === 0) {
                            document.getElementById('empty-state').classList.remove('hidden');
                        } else {
                            renderExploreGrid(data.data);
                        }
                    } else {
                        showToast(data.message || 'Error loading explore items', 'error');
                    }
                })
                .catch(err => {
                    document.getElementById('loading-spinner').classList.add('hidden');
                    console.error('Fetch error:', err);
                    showToast('Failed to load explore data', 'error');
                });
        }

        function renderExploreGrid(items) {
            const grid = document.getElementById('explore-grid');
            grid.innerHTML = items.map(item => {
                const statusBadge = item.is_active == 1 
                    ? `<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/40">Active</span>`
                    : `<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-800 text-gray-400 border border-gray-700">Hidden</span>`;

                const slideBadge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-800 text-gray-300 border border-gray-700">Slide ${item.slide_number}</span>`;

                return `
                    <div class="bg-dark-950/70 border border-gray-800 rounded-2xl p-6 relative flex flex-col hover:border-gray-700 transition">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r ${escapeHtml(item.gradient_theme || 'from-white to-gray-300')} rounded-xl flex items-center justify-center text-black text-xl shadow">
                                <i class="${escapeHtml(item.icon_class || 'fas fa-car-side')}"></i>
                            </div>
                            <div class="flex items-center gap-1.5">${slideBadge} ${statusBadge}</div>
                        </div>

                        <h4 class="text-base font-bold text-white mb-2">${escapeHtml(item.title)}</h4>
                        <p class="text-xs text-gray-400 line-clamp-2 leading-relaxed mb-4">${escapeHtml(item.description)}</p>

                        ${item.modal_title || item.modal_image_url ? `
                        <div class="mb-4 p-3 bg-dark-900/80 rounded-xl border border-gray-800/80 flex items-center gap-3">
                            ${item.modal_image_url ? `<img src="../${escapeHtml(item.modal_image_url)}" class="w-12 h-12 object-cover rounded-lg border border-gray-700">` : ''}
                            <div class="overflow-hidden">
                                <span class="block text-[11px] font-semibold text-gray-300 truncate">Hover: ${escapeHtml(item.modal_title || 'Showcase')}</span>
                                <span class="block text-[10px] text-gray-500 truncate">${escapeHtml(item.modal_description || 'View popup preview')}</span>
                            </div>
                        </div>` : ''}

                        <div class="mt-auto pt-4 border-t border-gray-800/80 w-full flex items-center justify-between">
                            <span class="text-[11px] text-gray-500">Order: ${item.sort_order}</span>
                            <div class="flex items-center gap-1.5">
                                <button onclick="toggleStatus(${item.id})" title="${item.is_active == 1 ? 'Hide on website' : 'Show on website'}" class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-800/60 text-xs">
                                    <i class="fas ${item.is_active == 1 ? 'fa-eye' : 'fa-eye-slash'}"></i>
                                </button>
                                <button onclick="openEditModal(${item.id})" title="Edit card" class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-800/60 text-xs">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteModal(${item.id}, '${escapeJs(item.title)}')" title="Delete card" class="p-2 text-red-400 hover:text-red-300 rounded-lg hover:bg-red-950/30 text-xs">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            grid.classList.remove('hidden');
        }

        function openCreateModal() {
            document.getElementById('explore-form').reset();
            document.getElementById('form-action').value = 'create';
            document.getElementById('explore-id').value = '';
            document.getElementById('modal-title').innerHTML = '<i class="fas fa-plus text-gray-400"></i> Add Explore Card';
            document.getElementById('btn-submit-text').innerText = 'Add Card';
            document.getElementById('inp-active').checked = true;
            document.getElementById('remove-image-flag').value = '0';
            document.getElementById('upload-placeholder').classList.remove('hidden');
            document.getElementById('upload-preview-box').classList.add('hidden');
            document.getElementById('explore-modal').classList.remove('hidden');
        }

        function openEditModal(id) {
            fetch(`api/explore.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const it = data.data;
                        document.getElementById('form-action').value = 'update';
                        document.getElementById('explore-id').value = it.id;
                        document.getElementById('inp-title').value = it.title || '';
                        document.getElementById('inp-subtitle').value = it.subtitle || '';
                        document.getElementById('inp-desc').value = it.description || '';
                        document.getElementById('inp-modal-title').value = it.modal_title || '';
                        document.getElementById('inp-modal-desc').value = it.modal_description || '';
                        document.getElementById('inp-icon').value = it.icon_class || 'fas fa-car-side';
                        document.getElementById('inp-gradient').value = it.gradient_theme || 'from-white to-gray-300';
                        document.getElementById('inp-slide').value = it.slide_number || 1;
                        document.getElementById('inp-sort').value = it.sort_order || 1;
                        document.getElementById('inp-active').checked = (it.is_active == 1);
                        document.getElementById('inp-modal-image-url').value = it.modal_image_url || '';
                        document.getElementById('remove-image-flag').value = '0';

                        if (it.modal_image_url && it.modal_image_url.trim() !== '') {
                            document.getElementById('preview-img').src = '../' + it.modal_image_url;
                            document.getElementById('upload-placeholder').classList.add('hidden');
                            document.getElementById('upload-preview-box').classList.remove('hidden');
                        } else {
                            document.getElementById('upload-placeholder').classList.remove('hidden');
                            document.getElementById('upload-preview-box').classList.add('hidden');
                        }

                        document.getElementById('modal-title').innerHTML = '<i class="fas fa-edit text-gray-400"></i> Edit Explore Card';
                        document.getElementById('btn-submit-text').innerText = 'Save Changes';
                        document.getElementById('explore-modal').classList.remove('hidden');
                    } else {
                        showToast(data.message || 'Could not load item details', 'error');
                    }
                });
        }

        function closeModal() {
            document.getElementById('explore-modal').classList.add('hidden');
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('upload-placeholder').classList.add('hidden');
                    document.getElementById('upload-preview-box').classList.remove('hidden');
                    document.getElementById('remove-image-flag').value = '0';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function clearImage(e) {
            e.stopPropagation();
            document.getElementById('file-input').value = '';
            document.getElementById('inp-modal-image-url').value = '';
            document.getElementById('remove-image-flag').value = '1';
            document.getElementById('upload-preview-box').classList.add('hidden');
            document.getElementById('upload-placeholder').classList.remove('hidden');
        }

        function handleExploreSubmit(e) {
            e.preventDefault();
            const form = document.getElementById('explore-form');
            const formData = new FormData(form);
            const btn = document.getElementById('btn-submit');
            const originalText = document.getElementById('btn-submit-text').innerText;

            btn.disabled = true;
            document.getElementById('btn-submit-text').innerText = 'Saving...';

            fetch('api/explore.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                document.getElementById('btn-submit-text').innerText = originalText;
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    closeModal();
                    loadExplore();
                } else {
                    showToast(data.message || 'Failed to save', 'error');
                }
            })
            .catch(err => {
                btn.disabled = false;
                document.getElementById('btn-submit-text').innerText = originalText;
                console.error(err);
                showToast('Network error saving explore card', 'error');
            });
        }

        function toggleStatus(id) {
            fetch('api/explore.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'toggle_status', id: id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    loadExplore();
                } else {
                    showToast(data.message || 'Failed to toggle status', 'error');
                }
            });
        }

        function openDeleteModal(id, name) {
            deleteItemId = id;
            document.getElementById('delete-name').innerText = `"${name}"`;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            deleteItemId = null;
            document.getElementById('delete-modal').classList.add('hidden');
        }

        function executeDelete() {
            if (!deleteItemId) return;
            fetch('api/explore.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', id: deleteItemId })
            })
            .then(res => res.json())
            .then(data => {
                closeDeleteModal();
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    loadExplore();
                } else {
                    showToast(data.message || 'Failed to delete item', 'error');
                }
            });
        }

        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerText = msg;
            toast.className = `fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 transition-all duration-300 ${type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'}`;
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.add('hidden'); }, 3500);
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>"']/g, s => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[s]));
        }

        function escapeJs(str) {
            if (!str) return '';
            return String(str).replace(/'/g, "\\'");
        }

        window.addEventListener('DOMContentLoaded', loadExplore);
    </script>
</body>
</html>
