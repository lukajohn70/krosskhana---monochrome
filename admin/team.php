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
    'hidden' => 0
];

if ($pdo) {
    try {
        if (function_exists('ensureCoreTables')) {
            ensureCoreTables($pdo);
        }
        $stmt = $pdo->query("SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN is_active = 1 THEN 1 END) as active,
            COUNT(CASE WHEN is_active = 0 THEN 1 END) as hidden
            FROM team_members");
        $stats = $stmt->fetch() ?: $stats;
    } catch (PDOException $e) {
        error_log('Team stats error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management - Krosskhana Admin</title>
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
    <?php $pageTitle = 'Team Management'; ?>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <!-- Toast Notification -->
    <div id="toast" class="hidden fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 transition-all duration-300"></div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-sm hover:shadow-md rounded-2xl border border-gray-200/80 transition-all p-5 flex items-center justify-between">
            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Members</dt>
                <dd id="stat-total" class="text-3xl font-extrabold text-gray-900 mt-1"><?php echo $stats['total']; ?></dd>
            </div>
            <div class="w-12 h-12 bg-gradient-to-tr from-gray-800 to-gray-700 text-white rounded-xl flex items-center justify-center text-xl shadow">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-sm hover:shadow-md rounded-2xl border border-gray-200/80 transition-all p-5 flex items-center justify-between">
            <div>
                <dt class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Active on Website</dt>
                <dd id="stat-active" class="text-3xl font-extrabold text-emerald-600 mt-1"><?php echo $stats['active']; ?></dd>
            </div>
            <div class="w-12 h-12 bg-gradient-to-tr from-emerald-600 to-teal-500 text-white rounded-xl flex items-center justify-center text-xl shadow">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-sm hover:shadow-md rounded-2xl border border-gray-200/80 transition-all p-5 flex items-center justify-between">
            <div>
                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Hidden / Draft</dt>
                <dd id="stat-hidden" class="text-3xl font-extrabold text-gray-600 mt-1"><?php echo $stats['hidden']; ?></dd>
            </div>
            <div class="w-12 h-12 bg-gradient-to-tr from-gray-400 to-gray-500 text-white rounded-xl flex items-center justify-center text-xl shadow">
                <i class="fas fa-eye-slash"></i>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white shadow-sm rounded-2xl border border-gray-200/80 overflow-hidden mb-8">
        <!-- Toolbar Header -->
        <div class="p-5 sm:p-6 border-b border-gray-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-id-badge text-gray-700"></i> Leadership & Team Members
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Manage leadership profiles displayed in the "Meet the Team" section of the landing page.</p>
            </div>
            <div>
                <button onclick="openCreateModal()" class="bg-gradient-to-r from-gray-900 to-black hover:from-gray-800 hover:to-gray-900 text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow hover:shadow-md transition-all flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Team Member
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-6">
            <!-- Loading Spinner -->
            <div id="loading-spinner" class="py-16 text-center text-gray-500">
                <i class="fas fa-circle-notch fa-spin text-3xl mb-3 text-gray-700"></i>
                <p class="text-sm font-medium">Loading team members...</p>
            </div>

            <!-- Empty State -->
            <div id="empty-state" class="hidden py-16 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4 text-gray-400 text-2xl">
                    <i class="fas fa-user-slash"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900">No team members found</h3>
                <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1 mb-5">Click below to add leadership profiles to your website.</p>
                <button onclick="openCreateModal()" class="px-4 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-xl shadow transition">
                    <i class="fas fa-plus mr-1"></i> Add First Member
                </button>
            </div>

            <!-- Team Grid -->
            <div id="team-grid" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
        </div>
    </div>

    <!-- Add / Edit Modal -->
    <div id="member-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white border border-gray-200 rounded-2xl max-w-xl w-full p-6 shadow-2xl relative my-8">
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 mb-5">
                <h3 id="modal-title" class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-user-plus text-gray-700"></i> Add Team Member
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-700 p-1">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form id="member-form" onsubmit="handleFormSubmit(event)" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" id="form-action" value="create">
                <input type="hidden" name="id" id="form-id" value="">
                <input type="hidden" name="image_url" id="inp-image-url" value="">
                <input type="hidden" name="remove_image" id="inp-remove-image" value="0">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="inp-name" required placeholder="e.g. Stevyn" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Role / Job Title <span class="text-red-500">*</span></label>
                        <input type="text" name="role" id="inp-role" required placeholder="e.g. Team Lead" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                    </div>
                </div>

                <!-- Bio -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Bio / Responsibilities</label>
                    <textarea name="bio" id="inp-bio" rows="2" placeholder="Brief description of their responsibilities..." class="w-full bg-gray-50 border border-gray-300 rounded-xl p-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition"></textarea>
                </div>

                <!-- Photo Upload Box -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Member Photo / Portrait</label>
                    <div id="upload-box" class="border-2 border-dashed border-gray-300 hover:border-gray-500 rounded-xl p-4 text-center cursor-pointer transition bg-gray-50 relative" onclick="document.getElementById('file-input').click()">
                        <input type="file" name="image_file" id="file-input" accept="image/*" class="hidden" onchange="previewUpload(this)">
                        
                        <div id="upload-placeholder" class="py-2">
                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-1"></i>
                            <p class="text-xs text-gray-600 font-medium">Click to upload photo</p>
                            <p class="text-[11px] text-gray-400">JPG, PNG, WEBP up to 5MB</p>
                        </div>

                        <div id="upload-preview-box" class="hidden flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img id="preview-img" src="" alt="Preview" class="w-12 h-12 rounded-full object-cover border border-gray-300">
                                <span id="preview-filename" class="text-xs font-medium text-gray-700">Photo selected</span>
                            </div>
                            <button type="button" onclick="clearPhoto(event)" class="text-xs text-red-600 hover:text-red-800 font-semibold px-2 py-1 bg-red-50 rounded-lg">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Icon Class -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Fallback Icon (FontAwesome)</label>
                        <select name="icon_class" id="inp-icon" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                            <option value="fas fa-user-tie">👔 User Tie (Leadership)</option>
                            <option value="fas fa-tasks">📋 Tasks / PM</option>
                            <option value="fas fa-cogs">⚙️ Technical / Cogs</option>
                            <option value="fas fa-car">🏎️ Motorsports / Car</option>
                            <option value="fas fa-bullhorn">📢 Marketing / PR</option>
                            <option value="fas fa-shield-alt">🛡️ Safety / Security</option>
                            <option value="fas fa-camera">📷 Media / Photo</option>
                        </select>
                    </div>

                    <!-- Gradient Theme -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Card Accent Gradient</label>
                        <select name="gradient_theme" id="inp-gradient" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                            <option value="from-gray-500 to-gray-700">Carbon Gray (Default)</option>
                            <option value="from-gray-700 to-gray-500">Inverted Gray</option>
                            <option value="from-gray-900 to-black">Deep Black</option>
                            <option value="from-red-600 to-gray-900">Racing Crimson</option>
                            <option value="from-emerald-600 to-gray-900">Track Green</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <!-- Sort Order -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Display Priority (Order)</label>
                        <input type="number" name="sort_order" id="inp-sort" value="0" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3.5 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-700 transition">
                        <span class="text-[11px] text-gray-400">Lower numbers appear first</span>
                    </div>

                    <!-- Active Toggle -->
                    <div class="flex items-center gap-3 pt-4">
                        <input type="checkbox" name="is_active" id="inp-active" value="1" checked class="w-4 h-4 rounded text-gray-900 focus:ring-gray-700 border-gray-300">
                        <label for="inp-active" class="text-xs font-semibold text-gray-800">
                            Visible on Website
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-5 border-t border-gray-200">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 hover:bg-gray-100 rounded-xl text-xs font-semibold text-gray-700 transition">
                        Cancel
                    </button>
                    <button type="submit" id="btn-submit" class="px-5 py-2 bg-gradient-to-r from-gray-900 to-black text-white hover:from-gray-800 hover:to-gray-900 rounded-xl text-xs font-semibold shadow transition flex items-center gap-2">
                        <span id="btn-submit-text">Save Member</span>
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
            <h3 class="text-base font-bold text-gray-900 mb-1">Delete Team Member?</h3>
            <p class="text-xs text-gray-500 mb-5">This action will remove this member from the public website.</p>
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
        let deleteMemberId = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadTeam();
        });

        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerText = msg;
            toast.className = `fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 text-xs font-semibold transition-all duration-300 ${type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'}`;
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.add('hidden'); }, 3500);
        }

        function loadTeam() {
            document.getElementById('loading-spinner').classList.remove('hidden');
            document.getElementById('team-grid').classList.add('hidden');
            document.getElementById('empty-state').classList.add('hidden');

            fetch('api/team.php')
                .then(async res => {
                    const text = await res.text();
                    try { return JSON.parse(text); } catch (e) { throw new Error(text.replace(/<[^>]+>/g, '').trim() || `Error ${res.status}`); }
                })
                .then(data => {
                    document.getElementById('loading-spinner').classList.add('hidden');
                    if (data.status === 'success') {
                        if (data.stats) {
                            document.getElementById('stat-total').innerText = data.stats.total || 0;
                            document.getElementById('stat-active').innerText = data.stats.active || 0;
                            document.getElementById('stat-hidden').innerText = data.stats.hidden || 0;
                        }
                        renderCards(data.data || []);
                    } else {
                        showToast(data.message || 'Error loading team data', 'error');
                    }
                })
                .catch(err => {
                    document.getElementById('loading-spinner').classList.add('hidden');
                    console.error('Fetch team error:', err);
                    showToast('Failed to load team data', 'error');
                });
        }

        function renderCards(members) {
            const container = document.getElementById('team-grid');
            const emptyState = document.getElementById('empty-state');
            container.innerHTML = '';

            if (!members || members.length === 0) {
                emptyState.classList.remove('hidden');
                return;
            }

            container.classList.remove('hidden');
            members.forEach(m => {
                const card = document.createElement('div');
                card.className = "bg-white border border-gray-200/80 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between";

                const iconOrImg = m.image_url 
                    ? `<img src="../${m.image_url}" alt="${m.name}" class="w-16 h-16 rounded-full object-cover border-2 border-gray-200 shadow-sm">`
                    : `<div class="w-16 h-16 rounded-full bg-gradient-to-r ${m.gradient_theme || 'from-gray-500 to-gray-700'} flex items-center justify-center text-white text-2xl shadow-sm"><i class="${m.icon_class || 'fas fa-user-tie'}"></i></div>`;

                const statusBadge = m.is_active == 1 
                    ? `<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">Active</span>`
                    : `<span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600">Hidden</span>`;

                card.innerHTML = `
                    <div>
                        <div class="flex items-start justify-between mb-4">
                            ${iconOrImg}
                            <div class="flex items-center gap-2">
                                ${statusBadge}
                                <span class="text-[11px] font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">#${m.sort_order}</span>
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">${m.name}</h3>
                        <p class="text-xs font-semibold text-gray-500 mt-0.5">${m.role}</p>
                        ${m.bio ? `<p class="text-xs text-gray-600 mt-3 leading-relaxed border-t border-gray-100 pt-2 line-clamp-3">${m.bio}</p>` : ''}
                    </div>

                    <div class="pt-4 mt-4 border-t border-gray-100 flex items-center justify-between">
                        <button onclick="toggleStatus(${m.id})" class="text-xs font-medium text-gray-600 hover:text-gray-900 flex items-center gap-1">
                            <i class="fas fa-power-off text-xs ${m.is_active == 1 ? 'text-emerald-500' : 'text-gray-400'}"></i>
                            <span>${m.is_active == 1 ? 'Hide' : 'Publish'}</span>
                        </button>
                        <div class="flex items-center gap-2">
                            <button onclick="openEditModal(${m.id})" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-medium rounded-lg transition">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                            <button onclick="openDeleteModal(${m.id})" class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg transition">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        function openCreateModal() {
            document.getElementById('modal-title').innerHTML = '<i class="fas fa-user-plus text-gray-700"></i> Add Team Member';
            document.getElementById('form-action').value = 'create';
            document.getElementById('form-id').value = '';
            document.getElementById('member-form').reset();
            document.getElementById('inp-image-url').value = '';
            document.getElementById('inp-remove-image').value = '0';
            document.getElementById('upload-placeholder').classList.remove('hidden');
            document.getElementById('upload-preview-box').classList.add('hidden');
            document.getElementById('member-modal').classList.remove('hidden');
        }

        function openEditModal(id) {
            fetch(`api/team.php?id=${id}`)
                .then(async res => {
                    const text = await res.text();
                    try { return JSON.parse(text); } catch (e) { throw new Error(text.replace(/<[^>]+>/g, '').trim() || `Error ${res.status}`); }
                })
                .then(data => {
                    if (data.status === 'success') {
                        const m = data.data;
                        document.getElementById('modal-title').innerHTML = '<i class="fas fa-user-edit text-gray-700"></i> Edit Team Member';
                        document.getElementById('form-action').value = 'update';
                        document.getElementById('form-id').value = m.id;
                        document.getElementById('inp-name').value = m.name || '';
                        document.getElementById('inp-role').value = m.role || '';
                        document.getElementById('inp-bio').value = m.bio || '';
                        document.getElementById('inp-icon').value = m.icon_class || 'fas fa-user-tie';
                        document.getElementById('inp-gradient').value = m.gradient_theme || 'from-gray-500 to-gray-700';
                        document.getElementById('inp-sort').value = m.sort_order || 0;
                        document.getElementById('inp-active').checked = m.is_active == 1;
                        document.getElementById('inp-image-url').value = m.image_url || '';
                        document.getElementById('inp-remove-image').value = '0';

                        if (m.image_url) {
                            document.getElementById('preview-img').src = '../' + m.image_url;
                            document.getElementById('preview-filename').innerText = m.name + ' photo';
                            document.getElementById('upload-placeholder').classList.add('hidden');
                            document.getElementById('upload-preview-box').classList.remove('hidden');
                        } else {
                            document.getElementById('upload-placeholder').classList.remove('hidden');
                            document.getElementById('upload-preview-box').classList.add('hidden');
                        }

                        document.getElementById('member-modal').classList.remove('hidden');
                    } else {
                        showToast(data.message || 'Error fetching member data', 'error');
                    }
                })
                .catch(err => {
                    console.error('Fetch member error:', err);
                    showToast('Failed to fetch member details', 'error');
                });
        }

        function closeModal() {
            document.getElementById('member-modal').classList.add('hidden');
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
            document.getElementById('inp-image-url').value = '';
            document.getElementById('inp-remove-image').value = '1';
            document.getElementById('upload-placeholder').classList.remove('hidden');
            document.getElementById('upload-preview-box').classList.add('hidden');
        }

        function handleFormSubmit(e) {
            e.preventDefault();
            const form = document.getElementById('member-form');
            const formData = new FormData(form);
            const btn = document.getElementById('btn-submit');
            const originalText = document.getElementById('btn-submit-text').innerText;

            btn.disabled = true;
            document.getElementById('btn-submit-text').innerText = 'Saving...';

            fetch('api/team.php', {
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
                    showToast(data.message || 'Member saved successfully!', 'success');
                    closeModal();
                    loadTeam();
                } else {
                    showToast(data.message || 'Failed to save team member', 'error');
                }
            })
            .catch(err => {
                btn.disabled = false;
                document.getElementById('btn-submit-text').innerText = originalText;
                console.error('Save member error:', err);
                showToast(err.message || 'Network error saving team member', 'error');
            });
        }

        function toggleStatus(id) {
            fetch('api/team.php', {
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
                    loadTeam();
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
            deleteMemberId = id;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            deleteMemberId = null;
            document.getElementById('delete-modal').classList.add('hidden');
        }

        function executeDelete() {
            if (!deleteMemberId) return;
            fetch('api/team.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', id: deleteMemberId })
            })
            .then(async res => {
                const text = await res.text();
                try { return JSON.parse(text); } catch (e) { throw new Error(text || `Error ${res.status}`); }
            })
            .then(data => {
                closeDeleteModal();
                if (data.status === 'success') {
                    showToast(data.message || 'Member deleted', 'success');
                    loadTeam();
                } else {
                    showToast(data.message || 'Failed to delete member', 'error');
                }
            })
            .catch(err => {
                closeDeleteModal();
                console.error('Delete error:', err);
                showToast('Network error deleting member', 'error');
            });
        }
    </script>
</body>
</html>
