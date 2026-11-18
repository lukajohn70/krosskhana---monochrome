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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { poppins: ['Poppins', 'sans-serif'] },
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
                        <i class="fas fa-users text-gray-400"></i> Team Management
                    </h1>
                    <p class="text-xs text-gray-400">Manage leadership & team members displayed on the public website</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="openCreateModal()" class="px-4 py-2 bg-gradient-to-r from-white to-gray-200 text-black font-semibold rounded-xl text-xs hover:from-gray-100 hover:to-gray-300 transition shadow-lg flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Team Member
                    </button>
                </div>
            </header>

            <main class="p-6 space-y-6 max-w-7xl mx-auto w-full">
                <!-- Toast Notification -->
                <div id="toast" class="hidden fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 transition-all duration-300"></div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="bg-dark-900/60 border border-gray-800 rounded-2xl p-5 backdrop-blur-xl flex items-center justify-between">
                        <div>
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Members</span>
                            <div class="text-2xl font-bold text-white mt-1" id="stat-total"><?php echo $stats['total']; ?></div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gray-800/80 flex items-center justify-center text-gray-300 text-xl border border-gray-700/50">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="bg-dark-900/60 border border-gray-800 rounded-2xl p-5 backdrop-blur-xl flex items-center justify-between">
                        <div>
                            <span class="text-xs font-medium text-emerald-400 uppercase tracking-wider">Active on Website</span>
                            <div class="text-2xl font-bold text-emerald-400 mt-1" id="stat-active"><?php echo $stats['active']; ?></div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-950/40 flex items-center justify-center text-emerald-400 text-xl border border-emerald-800/50">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="bg-dark-900/60 border border-gray-800 rounded-2xl p-5 backdrop-blur-xl flex items-center justify-between">
                        <div>
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Hidden / Draft</span>
                            <div class="text-2xl font-bold text-gray-400 mt-1" id="stat-hidden"><?php echo $stats['hidden']; ?></div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gray-800/80 flex items-center justify-center text-gray-400 text-xl border border-gray-700/50">
                            <i class="fas fa-eye-slash"></i>
                        </div>
                    </div>
                </div>

                <!-- Team Members Grid -->
                <div class="bg-dark-900/60 border border-gray-800 rounded-2xl p-6 backdrop-blur-xl">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-id-badge text-gray-400"></i> Current Team Members
                        </h2>
                        <span class="text-xs text-gray-500">Ordered by display priority</span>
                    </div>

                    <!-- Loading State -->
                    <div id="loading-spinner" class="py-16 text-center text-gray-400">
                        <i class="fas fa-circle-notch fa-spin text-3xl mb-3 text-white"></i>
                        <p class="text-sm">Loading team members...</p>
                    </div>

                    <!-- Empty State -->
                    <div id="empty-state" class="hidden py-16 text-center text-gray-400">
                        <div class="w-16 h-16 rounded-full bg-gray-800 flex items-center justify-center mx-auto mb-4 text-gray-500 text-2xl">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <h3 class="text-base font-semibold text-white">No team members yet</h3>
                        <p class="text-xs text-gray-500 max-w-sm mx-auto mt-1 mb-4">Click below to add your first team member or leadership profile.</p>
                        <button onclick="openCreateModal()" class="px-4 py-2 bg-gradient-to-r from-white to-gray-200 text-black font-semibold rounded-xl text-xs hover:from-gray-100 hover:to-gray-300 transition">
                            <i class="fas fa-plus mr-1"></i> Add Team Member
                        </button>
                    </div>

                    <!-- Cards Grid -->
                    <div id="team-grid" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add / Edit Modal -->
    <div id="member-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-900 border border-gray-800 rounded-2xl max-w-2xl w-full p-6 shadow-2xl relative my-8">
            <div class="flex items-center justify-between pb-4 border-b border-gray-800 mb-6">
                <h3 id="modal-title" class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fas fa-user-plus text-gray-400"></i> Add Team Member
                </h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-white transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form id="member-form" onsubmit="handleMemberSubmit(event)" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" id="form-action" value="create">
                <input type="hidden" name="id" id="member-id" value="">
                <input type="hidden" name="remove_image" id="remove-image-flag" value="0">
                <input type="hidden" name="image_url" id="inp-image-url" value="">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Full Name <span class="text-red-400">*</span></label>
                        <input type="text" name="name" id="inp-name" required placeholder="e.g. Stevyn Johnson" class="w-full bg-dark-950 border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-gray-500">
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Role / Position <span class="text-red-400">*</span></label>
                        <input type="text" name="role" id="inp-role" required placeholder="e.g. Team Lead, Technical Lead" class="w-full bg-dark-950 border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-gray-500">
                    </div>
                </div>

                <!-- Bio -->
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Short Bio (Optional)</label>
                    <textarea name="bio" id="inp-bio" rows="2" placeholder="Brief background or responsibilities..." class="w-full bg-dark-950 border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-gray-500"></textarea>
                </div>

                <!-- Profile Photo / Icon -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Profile Photo</label>
                        <div class="border border-dashed border-gray-800 rounded-xl p-3 text-center cursor-pointer hover:border-gray-600 transition bg-dark-950/60" onclick="document.getElementById('file-input').click()">
                            <input type="file" name="image_file" id="file-input" accept="image/*" class="hidden" onchange="previewImage(this)">
                            <div id="upload-placeholder" class="py-2">
                                <i class="fas fa-camera text-gray-500 text-lg mb-1"></i>
                                <p class="text-[11px] text-gray-400">Click to upload photo</p>
                            </div>
                            <div id="upload-preview-box" class="hidden items-center justify-between gap-2 p-1">
                                <img id="preview-img" src="" class="w-12 h-12 rounded-full object-cover border border-gray-700">
                                <button type="button" onclick="clearImage(event)" class="text-xs text-red-400 hover:text-red-300"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Icon & Theme if no photo -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Default Avatar Icon</label>
                        <select name="icon_class" id="inp-icon" class="w-full bg-dark-950 border border-gray-800 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-gray-500">
                            <option value="fas fa-user-tie">User Tie (fas fa-user-tie)</option>
                            <option value="fas fa-tasks">Tasks / Manager (fas fa-tasks)</option>
                            <option value="fas fa-cogs">Cogs / Technical (fas fa-cogs)</option>
                            <option value="fas fa-car">Racing / Car (fas fa-car)</option>
                            <option value="fas fa-wrench">Engineering (fas fa-wrench)</option>
                            <option value="fas fa-shield-alt">Safety / Ops (fas fa-shield-alt)</option>
                            <option value="fas fa-crown">Executive (fas fa-crown)</option>
                        </select>
                        <span class="text-[10px] text-gray-500 mt-1 block">Shown if no profile photo uploaded</span>
                    </div>
                </div>

                <!-- Gradient Color Scheme -->
                <div>
                    <label class="block text-xs font-semibold text-gray-300 mb-1.5">Avatar Gradient Background</label>
                    <div class="grid grid-cols-4 gap-2">
                        <label class="border border-gray-800 rounded-lg p-2 flex items-center justify-center cursor-pointer hover:border-gray-600 bg-dark-950">
                            <input type="radio" name="gradient_theme" value="from-gray-500 to-gray-700" checked class="sr-only">
                            <div class="w-full h-6 rounded bg-gradient-to-r from-gray-500 to-gray-700 border border-gray-600"></div>
                        </label>
                        <label class="border border-gray-800 rounded-lg p-2 flex items-center justify-center cursor-pointer hover:border-gray-600 bg-dark-950">
                            <input type="radio" name="gradient_theme" value="from-gray-700 to-gray-500" class="sr-only">
                            <div class="w-full h-6 rounded bg-gradient-to-r from-gray-700 to-gray-500 border border-gray-600"></div>
                        </label>
                        <label class="border border-gray-800 rounded-lg p-2 flex items-center justify-center cursor-pointer hover:border-gray-600 bg-dark-950">
                            <input type="radio" name="gradient_theme" value="from-gray-900 to-black" class="sr-only">
                            <div class="w-full h-6 rounded bg-gradient-to-r from-gray-900 to-black border border-gray-600"></div>
                        </label>
                        <label class="border border-gray-800 rounded-lg p-2 flex items-center justify-center cursor-pointer hover:border-gray-600 bg-dark-950">
                            <input type="radio" name="gradient_theme" value="from-white to-gray-400" class="sr-only">
                            <div class="w-full h-6 rounded bg-gradient-to-r from-white to-gray-400 border border-gray-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Socials & Order -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">LinkedIn Profile (Optional)</label>
                        <input type="url" name="linkedin_url" id="inp-linkedin" placeholder="https://linkedin.com/in/..." class="w-full bg-dark-950 border border-gray-800 rounded-xl px-3 py-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Email / Contact (Optional)</label>
                        <input type="email" name="email" id="inp-email" placeholder="name@krosskhana.org" class="w-full bg-dark-950 border border-gray-800 rounded-xl px-3 py-2 text-xs text-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Display Sort Order</label>
                        <input type="number" name="sort_order" id="inp-sort" value="1" class="w-full bg-dark-950 border border-gray-800 rounded-xl px-3 py-2 text-xs text-white">
                        <span class="text-[10px] text-gray-500">1 shows first</span>
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
                        <span id="btn-submit-text">Save Member</span>
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
            <h4 class="text-base font-bold text-white mb-1">Delete Team Member</h4>
            <p class="text-xs text-gray-400 mb-6">Are you sure you want to remove <strong id="delete-name" class="text-white">this member</strong>? This action cannot be undone.</p>
            <div class="flex items-center justify-center gap-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-700 text-gray-300 rounded-xl text-xs">Cancel</button>
                <button type="button" id="confirm-delete-btn" onclick="executeDelete()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl text-xs shadow">Delete</button>
            </div>
        </div>
    </div>

    <script>
        let deleteMemberId = null;

        function loadTeam() {
            document.getElementById('loading-spinner').classList.remove('hidden');
            document.getElementById('team-grid').classList.add('hidden');
            document.getElementById('empty-state').classList.add('hidden');

            fetch('api/team.php')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('loading-spinner').classList.add('hidden');
                    if (data.status === 'success') {
                        // Update stats
                        if (data.stats) {
                            document.getElementById('stat-total').innerText = data.stats.total;
                            document.getElementById('stat-active').innerText = data.stats.active_count;
                            document.getElementById('stat-hidden').innerText = data.stats.hidden_count;
                        }

                        if (!data.data || data.data.length === 0) {
                            document.getElementById('empty-state').classList.remove('hidden');
                        } else {
                            renderTeamGrid(data.data);
                        }
                    } else {
                        showToast(data.message || 'Error loading team members', 'error');
                    }
                })
                .catch(err => {
                    document.getElementById('loading-spinner').classList.add('hidden');
                    console.error('Fetch error:', err);
                    showToast('Failed to load team data', 'error');
                });
        }

        function renderTeamGrid(members) {
            const grid = document.getElementById('team-grid');
            grid.innerHTML = members.map(m => {
                const isPhoto = m.image_url && m.image_url.trim() !== '';
                const photoHtml = isPhoto 
                    ? `<img src="../${escapeHtml(m.image_url)}" class="w-20 h-20 rounded-full object-cover shadow-lg border-2 border-gray-700">`
                    : `<div class="w-20 h-20 bg-gradient-to-r ${escapeHtml(m.gradient_theme || 'from-gray-500 to-gray-700')} rounded-full flex items-center justify-center shadow-lg"><i class="${escapeHtml(m.icon_class || 'fas fa-user-tie')} text-white text-3xl"></i></div>`;

                const statusBadge = m.is_active == 1 
                    ? `<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-950/60 text-emerald-400 border border-emerald-800/40">Active</span>`
                    : `<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-800 text-gray-400 border border-gray-700">Hidden</span>`;

                return `
                    <div class="bg-dark-950/70 border border-gray-800 rounded-2xl p-6 relative flex flex-col items-center text-center hover:border-gray-700 transition">
                        <div class="absolute top-4 right-4">${statusBadge}</div>
                        <div class="mb-4">${photoHtml}</div>
                        <h4 class="text-lg font-bold text-white">${escapeHtml(m.name)}</h4>
                        <p class="text-xs text-gray-400 font-medium mb-3">${escapeHtml(m.role)}</p>
                        ${m.bio ? `<p class="text-xs text-gray-400 line-clamp-2 mb-4 px-2">${escapeHtml(m.bio)}</p>` : ''}
                        
                        <div class="mt-auto pt-4 border-t border-gray-800/80 w-full flex items-center justify-between">
                            <span class="text-[11px] text-gray-500">Order: ${m.sort_order}</span>
                            <div class="flex items-center gap-1.5">
                                <button onclick="toggleStatus(${m.id})" title="${m.is_active == 1 ? 'Hide on website' : 'Show on website'}" class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-800/60 text-xs">
                                    <i class="fas ${m.is_active == 1 ? 'fa-eye' : 'fa-eye-slash'}"></i>
                                </button>
                                <button onclick="openEditModal(${m.id})" title="Edit member" class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-800/60 text-xs">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteModal(${m.id}, '${escapeJs(m.name)}')" title="Delete member" class="p-2 text-red-400 hover:text-red-300 rounded-lg hover:bg-red-950/30 text-xs">
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
            document.getElementById('member-form').reset();
            document.getElementById('form-action').value = 'create';
            document.getElementById('member-id').value = '';
            document.getElementById('modal-title').innerHTML = '<i class="fas fa-user-plus text-gray-400"></i> Add Team Member';
            document.getElementById('btn-submit-text').innerText = 'Add Member';
            document.getElementById('inp-active').checked = true;
            document.getElementById('remove-image-flag').value = '0';
            document.getElementById('upload-placeholder').classList.remove('hidden');
            document.getElementById('upload-preview-box').classList.add('hidden');
            document.getElementById('member-modal').classList.remove('hidden');
        }

        function openEditModal(id) {
            fetch(`api/team.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const m = data.data;
                        document.getElementById('form-action').value = 'update';
                        document.getElementById('member-id').value = m.id;
                        document.getElementById('inp-name').value = m.name || '';
                        document.getElementById('inp-role').value = m.role || '';
                        document.getElementById('inp-bio').value = m.bio || '';
                        document.getElementById('inp-icon').value = m.icon_class || 'fas fa-user-tie';
                        document.getElementById('inp-linkedin').value = m.linkedin_url || '';
                        document.getElementById('inp-email').value = m.email || '';
                        document.getElementById('inp-sort').value = m.sort_order || 1;
                        document.getElementById('inp-active').checked = (m.is_active == 1);
                        document.getElementById('inp-image-url').value = m.image_url || '';
                        document.getElementById('remove-image-flag').value = '0';

                        if (m.image_url && m.image_url.trim() !== '') {
                            document.getElementById('preview-img').src = '../' + m.image_url;
                            document.getElementById('upload-placeholder').classList.add('hidden');
                            document.getElementById('upload-preview-box').classList.remove('hidden');
                        } else {
                            document.getElementById('upload-placeholder').classList.remove('hidden');
                            document.getElementById('upload-preview-box').classList.add('hidden');
                        }

                        document.getElementById('modal-title').innerHTML = '<i class="fas fa-user-edit text-gray-400"></i> Edit Team Member';
                        document.getElementById('btn-submit-text').innerText = 'Save Changes';
                        document.getElementById('member-modal').classList.remove('hidden');
                    } else {
                        showToast(data.message || 'Could not load member details', 'error');
                    }
                });
        }

        function closeModal() {
            document.getElementById('member-modal').classList.add('hidden');
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
            document.getElementById('inp-image-url').value = '';
            document.getElementById('remove-image-flag').value = '1';
            document.getElementById('upload-preview-box').classList.add('hidden');
            document.getElementById('upload-placeholder').classList.remove('hidden');
        }

        function handleMemberSubmit(e) {
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
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                document.getElementById('btn-submit-text').innerText = originalText;
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    closeModal();
                    loadTeam();
                } else {
                    showToast(data.message || 'Failed to save', 'error');
                }
            })
            .catch(err => {
                btn.disabled = false;
                document.getElementById('btn-submit-text').innerText = originalText;
                console.error(err);
                showToast('Network error saving team member', 'error');
            });
        }

        function toggleStatus(id) {
            fetch('api/team.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'toggle_status', id: id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    loadTeam();
                } else {
                    showToast(data.message || 'Failed to toggle status', 'error');
                }
            });
        }

        function openDeleteModal(id, name) {
            deleteMemberId = id;
            document.getElementById('delete-name').innerText = `"${name}"`;
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
            .then(res => res.json())
            .then(data => {
                closeDeleteModal();
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    loadTeam();
                } else {
                    showToast(data.message || 'Failed to delete member', 'error');
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

        window.addEventListener('DOMContentLoaded', loadTeam);
    </script>
</body>
</html>
