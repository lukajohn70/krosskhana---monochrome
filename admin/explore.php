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
        if (function_exists('ensureCoreTables')) {
            ensureCoreTables($pdo);
        }
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
    <title>Explore Section - Krosskhana Admin</title>
    <link rel="icon" type="image/png" href="../assets/logos/Krosskhana-05.png">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { poppins: ['Poppins', 'sans-serif'], orbitron: ['Orbitron', 'monospace'] }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen font-poppins">
    <?php $pageTitle = 'Explore Showcase'; ?>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <!-- Toast Notification -->
    <div id="toast" class="hidden fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 transition-all duration-300"></div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-sm hover:shadow-md rounded-2xl border border-gray-200/80 transition-all p-5 flex items-center justify-between">
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Cards</dt>
                <dd id="stat-total" class="text-3xl font-extrabold text-gray-900 mt-1"><?php echo $stats['total']; ?></dd>
            </div>
            <div class="w-12 h-12 bg-gradient-to-tr from-gray-800 to-gray-700 text-white rounded-xl flex items-center justify-center text-xl shadow">
                <i class="fas fa-compass"></i>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-sm hover:shadow-md rounded-2xl border border-gray-200/80 transition-all p-5 flex items-center justify-between">
            <div>
                <dt class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Slide 1 Cards</dt>
                <dd id="stat-s1" class="text-3xl font-extrabold text-indigo-600 mt-1"><?php echo $stats['slide_1']; ?></dd>
            </div>
            <div class="w-12 h-12 bg-gradient-to-tr from-indigo-600 to-blue-500 text-white rounded-xl flex items-center justify-center text-xl shadow">
                <i class="fas fa-layer-group"></i>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-sm hover:shadow-md rounded-2xl border border-gray-200/80 transition-all p-5 flex items-center justify-between">
            <div>
                <dt class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Slide 2 Cards</dt>
                <dd id="stat-s2" class="text-3xl font-extrabold text-purple-600 mt-1"><?php echo $stats['slide_2']; ?></dd>
            </div>
            <div class="w-12 h-12 bg-gradient-to-tr from-purple-600 to-pink-500 text-white rounded-xl flex items-center justify-center text-xl shadow">
                <i class="fas fa-clone"></i>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-sm hover:shadow-md rounded-2xl border border-gray-200/80 transition-all p-5 flex items-center justify-between">
            <div>
                <dt class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Active Status</dt>
                <dd id="stat-active" class="text-3xl font-extrabold text-emerald-600 mt-1"><?php echo $stats['active']; ?></dd>
            </div>
            <div class="w-12 h-12 bg-gradient-to-tr from-emerald-600 to-teal-500 text-white rounded-xl flex items-center justify-center text-xl shadow">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white shadow-sm rounded-2xl border border-gray-200/80 overflow-hidden mb-8">
        <!-- Toolbar Header -->
        <div class="p-5 sm:p-6 border-b border-gray-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-compass text-gray-700"></i> Explore the World of Krosskhana
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Manage the cards, icons, descriptions, and hover popups displayed in the homepage carousel.</p>
            </div>
            <div>
                <button onclick="openCreateModal()" class="bg-gradient-to-r from-gray-900 to-black hover:from-gray-800 hover:to-gray-900 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow hover:shadow-md transition-all flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Explore Card
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-6">
            <!-- Loading Spinner -->
            <div id="loading-spinner" class="py-16 text-center text-gray-500">
                <i class="fas fa-circle-notch fa-spin text-3xl mb-3 text-gray-700"></i>
                <p class="text-sm font-medium">Loading explore items...</p>
            </div>

            <!-- Empty State -->
            <div id="empty-state" class="hidden py-16 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4 text-gray-400 text-2xl">
                    <i class="fas fa-compass"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900">No explore cards found</h3>
                <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1 mb-5">Click below to add cards to the Explore section.</p>
                <button onclick="openCreateModal()" class="px-4 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-xl shadow transition">
                    <i class="fas fa-plus mr-1"></i> Add First Card
                </button>
            </div>

            <!-- Explore Grid -->
            <div id="explore-grid" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
        </div>
    </div>

    <!-- Add / Edit Modal -->
    <div id="explore-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white border border-gray-200 rounded-2xl max-w-2xl w-full p-6 shadow-2xl relative my-8">
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-5">
                <h3 id="modal-title" class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-gray-700"></i> Add Explore Card
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-700 p-1">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form id="explore-form" onsubmit="handleFormSubmit(event)" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" id="form-action" value="create">
                <input type="hidden" name="id" id="form-id" value="">
                <input type="hidden" name="modal_image_url" id="inp-modal-image-url" value="">
                <input type="hidden" name="remove_image" id="inp-remove-image" value="0">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Card Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="inp-title" required placeholder="e.g. Autocross & Drifting" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                    </div>

                    <!-- Slide Assignment -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Carousel Slide</label>
                        <select name="slide_number" id="inp-slide" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                            <option value="1">Slide 1 (First Set)</option>
                            <option value="2">Slide 2 (Second Set)</option>
                        </select>
                    </div>
                </div>

                <!-- Card Description -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Card Brief Description <span class="text-red-500">*</span></label>
                    <textarea name="description" id="inp-desc" rows="2" required placeholder="Brief tagline shown on the card face..." class="w-full bg-gray-50 border border-gray-300 rounded-xl p-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition"></textarea>
                </div>

                <!-- Hover Modal Section -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider block">Hover Preview Modal Details</span>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Hover Popup Title</label>
                            <input type="text" name="modal_title" id="inp-modal-title" placeholder="e.g. Racing Excellence" class="w-full bg-white border border-gray-300 rounded-xl px-3 py-2 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Hover Popup Description</label>
                            <input type="text" name="modal_description" id="inp-modal-desc" placeholder="Expanded explanation on hover..." class="w-full bg-white border border-gray-300 rounded-xl px-3 py-2 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Showcase Photo</label>
                        <div id="upload-box" class="border-2 border-dashed border-gray-300 hover:border-gray-500 rounded-xl p-3 text-center cursor-pointer transition bg-white relative" onclick="document.getElementById('file-input').click()">
                            <input type="file" name="image_file" id="file-input" accept="image/*" class="hidden" onchange="previewUpload(this)">
                            
                            <div id="upload-placeholder" class="py-1">
                                <i class="fas fa-image text-xl text-gray-400 mb-1"></i>
                                <p class="text-xs text-gray-600 font-medium">Click to upload showcase photo</p>
                            </div>

                            <div id="upload-preview-box" class="hidden flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img id="preview-img" src="" alt="Preview" class="w-12 h-12 rounded-lg object-cover border border-gray-300">
                                    <span id="preview-filename" class="text-xs font-medium text-gray-700">Photo selected</span>
                                </div>
                                <button type="button" onclick="clearPhoto(event)" class="text-xs text-red-600 hover:text-red-800 font-semibold px-2 py-1 bg-red-50 rounded-lg">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Icon -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Icon</label>
                        <select name="icon_class" id="inp-icon" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                            <option value="fas fa-car-side">🏎️ Car Side (Racing)</option>
                            <option value="fas fa-microscope">🔬 Microscope (R&D)</option>
                            <option value="fas fa-music">🎵 Music (Lifestyle)</option>
                            <option value="fas fa-tshirt">👕 T-Shirt (Fashion)</option>
                            <option value="fas fa-heart">❤️ Heart (Community)</option>
                            <option value="fas fa-crown">👑 Crown (High-End)</option>
                            <option value="fas fa-flag-checkered">🏁 Checkered Flag</option>
                            <option value="fas fa-trophy">🏆 Trophy</option>
                        </select>
                    </div>

                    <!-- Gradient Theme -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Card Accent</label>
                        <select name="gradient_theme" id="inp-gradient" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                            <option value="from-white to-gray-300">Monochrome Silver</option>
                            <option value="from-gray-300 to-white">Inverted Silver</option>
                            <option value="from-gray-900 to-black">Carbon Black</option>
                            <option value="from-red-600 to-gray-900">Crimson Drift</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" id="inp-sort" value="0" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                    </div>
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="is_active" id="inp-active" value="1" checked class="w-4 h-4 rounded text-gray-900 focus:ring-gray-700 border-gray-300">
                    <label for="inp-active" class="text-xs font-semibold text-gray-800">
                        Visible in public carousel
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 hover:bg-gray-100 rounded-xl text-xs font-semibold text-gray-700 transition">
                        Cancel
                    </button>
                    <button type="submit" id="btn-submit" class="px-5 py-2 bg-gradient-to-r from-gray-900 to-black text-white hover:from-gray-800 hover:to-gray-900 rounded-xl text-xs font-semibold shadow transition flex items-center gap-2">
                        <span id="btn-submit-text">Save Card</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white border border-gray-200 rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center">
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4 text-xl">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-1">Delete Explore Card?</h3>
            <p class="text-xs text-gray-500 mb-5">This action will remove this card from the public homepage.</p>
            <div class="flex justify-center gap-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 hover:bg-gray-100 rounded-xl text-xs font-semibold text-gray-700 transition">
                    Cancel
                </button>
                <button onclick="executeDelete()" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-semibold transition">
                    Confirm Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        let deleteItemId = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadExplore();
        });

        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerText = msg;
            toast.className = `fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 text-xs font-semibold transition-all duration-300 ${type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'}`;
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.add('hidden'); }, 3500);
        }

        function loadExplore() {
            document.getElementById('loading-spinner').classList.remove('hidden');
            document.getElementById('explore-grid').classList.add('hidden');
            document.getElementById('empty-state').classList.add('hidden');

            fetch('api/explore.php')
                .then(async res => {
                    const text = await res.text();
                    try { return JSON.parse(text); } catch (e) { throw new Error(text.replace(/<[^>]+>/g, '').trim() || `Error ${res.status}`); }
                })
                .then(data => {
                    document.getElementById('loading-spinner').classList.add('hidden');
                    if (data.status === 'success') {
                        if (data.stats) {
                            document.getElementById('stat-total').innerText = data.stats.total || 0;
                            document.getElementById('stat-s1').innerText = data.stats.slide_1 || 0;
                            document.getElementById('stat-s2').innerText = data.stats.slide_2 || 0;
                            document.getElementById('stat-active').innerText = data.stats.active || 0;
                        }
                        renderCards(data.data || []);
                    } else {
                        showToast(data.message || 'Error loading explore data', 'error');
                    }
                })
                .catch(err => {
                    document.getElementById('loading-spinner').classList.add('hidden');
                    console.error('Fetch explore error:', err);
                    showToast('Failed to load explore data', 'error');
                });
        }

        function renderCards(items) {
            const container = document.getElementById('explore-grid');
            const emptyState = document.getElementById('empty-state');
            container.innerHTML = '';

            if (!items || items.length === 0) {
                emptyState.classList.remove('hidden');
                return;
            }

            container.classList.remove('hidden');
            items.forEach(it => {
                const card = document.createElement('div');
                card.className = "bg-white border border-gray-200/80 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between";

                const statusBadge = it.is_active == 1 
                    ? `<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">Active</span>`
                    : `<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600">Hidden</span>`;

                const slideBadge = `<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700">Slide ${it.slide_number}</span>`;

                card.innerHTML = `
                    <div>
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gray-900 text-white flex items-center justify-center text-xl shadow-sm">
                                <i class="${it.icon_class || 'fas fa-car-side'}"></i>
                            </div>
                            <div class="flex items-center gap-1.5">
                                ${slideBadge}
                                ${statusBadge}
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">${it.title}</h3>
                        <p class="text-xs text-gray-600 mt-2 leading-relaxed line-clamp-3">${it.description}</p>
                        
                        ${it.modal_title ? `
                            <div class="mt-3 p-2.5 bg-gray-50 rounded-xl border border-gray-100 text-[11px] text-gray-600">
                                <span class="font-bold text-gray-800 block">Hover Popup: ${it.modal_title}</span>
                                <span class="text-gray-500 line-clamp-2">${it.modal_description || ''}</span>
                            </div>
                        ` : ''}
                    </div>

                    <div class="pt-4 mt-4 border-t border-gray-100 flex items-center justify-between">
                        <button onclick="toggleStatus(${it.id})" class="text-xs font-medium text-gray-600 hover:text-gray-900 flex items-center gap-1">
                            <i class="fas fa-power-off text-xs ${it.is_active == 1 ? 'text-emerald-500' : 'text-gray-400'}"></i>
                            <span>${it.is_active == 1 ? 'Hide' : 'Publish'}</span>
                        </button>
                        <div class="flex items-center gap-2">
                            <button onclick="openEditModal(${it.id})" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-medium rounded-lg transition">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                            <button onclick="openDeleteModal(${it.id})" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg transition">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        function openCreateModal() {
            document.getElementById('modal-title').innerHTML = '<i class="fas fa-plus-circle text-gray-700"></i> Add Explore Card';
            document.getElementById('form-action').value = 'create';
            document.getElementById('form-id').value = '';
            document.getElementById('explore-form').reset();
            document.getElementById('inp-modal-image-url').value = '';
            document.getElementById('inp-remove-image').value = '0';
            document.getElementById('upload-placeholder').classList.remove('hidden');
            document.getElementById('upload-preview-box').classList.add('hidden');
            document.getElementById('explore-modal').classList.remove('hidden');
        }

        function openEditModal(id) {
            fetch(`api/explore.php?id=${id}`)
                .then(async res => {
                    const text = await res.text();
                    try { return JSON.parse(text); } catch (e) { throw new Error(text.replace(/<[^>]+>/g, '').trim() || `Error ${res.status}`); }
                })
                .then(data => {
                    if (data.status === 'success') {
                        const it = data.data;
                        document.getElementById('modal-title').innerHTML = '<i class="fas fa-edit text-gray-700"></i> Edit Explore Card';
                        document.getElementById('form-action').value = 'update';
                        document.getElementById('form-id').value = it.id;
                        document.getElementById('inp-title').value = it.title || '';
                        document.getElementById('inp-desc').value = it.description || '';
                        document.getElementById('inp-modal-title').value = it.modal_title || '';
                        document.getElementById('inp-modal-desc').value = it.modal_description || '';
                        document.getElementById('inp-icon').value = it.icon_class || 'fas fa-car-side';
                        document.getElementById('inp-gradient').value = it.gradient_theme || 'from-white to-gray-300';
                        document.getElementById('inp-slide').value = it.slide_number || 1;
                        document.getElementById('inp-sort').value = it.sort_order || 0;
                        document.getElementById('inp-active').checked = it.is_active == 1;
                        document.getElementById('inp-modal-image-url').value = it.modal_image_url || '';
                        document.getElementById('inp-remove-image').value = '0';

                        if (it.modal_image_url) {
                            document.getElementById('preview-img').src = '../' + it.modal_image_url;
                            document.getElementById('preview-filename').innerText = 'Current photo attached';
                            document.getElementById('upload-placeholder').classList.add('hidden');
                            document.getElementById('upload-preview-box').classList.remove('hidden');
                        } else {
                            document.getElementById('upload-placeholder').classList.remove('hidden');
                            document.getElementById('upload-preview-box').classList.add('hidden');
                        }

                        document.getElementById('explore-modal').classList.remove('hidden');
                    } else {
                        showToast(data.message || 'Error fetching card data', 'error');
                    }
                })
                .catch(err => {
                    console.error('Fetch card error:', err);
                    showToast('Failed to fetch card details', 'error');
                });
        }

        function closeModal() {
            document.getElementById('explore-modal').classList.add('hidden');
        }

        function previewUpload(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('preview-filename').innerText = file.name;
                    document.getElementById('upload-placeholder').classList.add('hidden');
                    document.getElementById('upload-preview-box').classList.remove('hidden');
                    document.getElementById('inp-remove-image').value = '0';
                }
                reader.readAsDataURL(file);
            }
        }

        function clearPhoto(e) {
            e.stopPropagation();
            document.getElementById('file-input').value = '';
            document.getElementById('inp-modal-image-url').value = '';
            document.getElementById('inp-remove-image').value = '1';
            document.getElementById('upload-placeholder').classList.remove('hidden');
            document.getElementById('upload-preview-box').classList.add('hidden');
        }

        function handleFormSubmit(e) {
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
            .then(async (res) => {
                const text = await res.text();
                try {
                    return JSON.parse(text);
                } catch(e) {
                    const clean = text.replace(/<[^>]+>/g, '').trim();
                    throw new Error(clean || `Server returned status ${res.status}`);
                }
            })
            .then(data => {
                btn.disabled = false;
                document.getElementById('btn-submit-text').innerText = originalText;
                if (data.status === 'success') {
                    showToast(data.message || 'Card saved successfully!', 'success');
                    closeModal();
                    loadExplore();
                } else {
                    showToast(data.message || 'Failed to save card', 'error');
                }
            })
            .catch(err => {
                btn.disabled = false;
                document.getElementById('btn-submit-text').innerText = originalText;
                console.error('Save card error:', err);
                showToast(err.message || 'Network error saving explore card', 'error');
            });
        }

        function toggleStatus(id) {
            fetch('api/explore.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'toggle_status', id: id })
            })
            .then(async res => {
                const text = await res.text();
                try { return JSON.parse(text); } catch (e) { throw new Error(text || `Error ${res.status}`); }
            })
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    loadExplore();
                } else {
                    showToast(data.message || 'Failed to toggle status', 'error');
                }
            })
            .catch(err => {
                console.error('Toggle error:', err);
                showToast('Network error updating status', 'error');
            });
        }

        function openDeleteModal(id) {
            deleteItemId = id;
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
            .then(async res => {
                const text = await res.text();
                try { return JSON.parse(text); } catch (e) { throw new Error(text || `Error ${res.status}`); }
            })
            .then(data => {
                closeDeleteModal();
                if (data.status === 'success') {
                    showToast(data.message || 'Card deleted', 'success');
                    loadExplore();
                } else {
                    showToast(data.message || 'Failed to delete card', 'error');
                }
            })
            .catch(err => {
                closeDeleteModal();
                console.error('Delete error:', err);
                showToast('Network error deleting card', 'error');
            });
        }
    </script>
</body>
</html>
