<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';

requireAdminAuth();

$pageTitle = 'Media & Video Manager';
include 'includes/sidebar.php';
?>

<div class="space-y-6">
    <!-- Top Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-orbitron">Media & Video Manager</h1>
            <p class="text-sm text-gray-500">Manage hero/splash videos, upload new clips, and organize the motorsports video gallery.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openUploadModal()" class="inline-flex items-center px-4 py-2 bg-black hover:bg-gray-800 text-white rounded-lg text-sm font-semibold transition-all duration-300 shadow-md">
                <i class="fas fa-plus mr-2"></i> Add / Upload Video
            </button>
            <button onclick="loadMediaData()" class="p-2 border border-gray-300 rounded-lg text-gray-600 hover:text-black hover:bg-gray-100 transition-colors" title="Refresh">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    <!-- Active Broadcast Videos (Splash & Hero) Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Splash Screen Active Card -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                        <i class="fas fa-bolt mr-1.5"></i> Intro Splash Screen
                    </span>
                    <span class="text-xs text-gray-400 font-mono" id="splash-source-badge">assets/videos/2.mp4</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Active Splash Screen Video</h3>
                <p class="text-xs text-gray-500 mb-4">Plays on first arrival while loading or when visitors enter the site.</p>
                <div class="relative rounded-xl overflow-hidden bg-black aspect-video mb-4 border border-gray-800 flex items-center justify-center">
                    <video id="preview-splash-video" class="w-full h-full object-cover" muted loop controls>
                        <source src="" type="video/mp4">
                    </video>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" id="manual-splash-url" placeholder="Path e.g. assets/videos/2.mp4" class="flex-1 text-xs border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                <button onclick="saveActiveVideo('splash', document.getElementById('manual-splash-url').value)" class="px-3 py-2 bg-black text-white rounded-lg text-xs font-semibold hover:bg-gray-800">
                    Apply
                </button>
            </div>
        </div>

        <!-- Hero Section Active Card -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        <i class="fas fa-film mr-1.5"></i> Homepage Hero Background
                    </span>
                    <span class="text-xs text-gray-400 font-mono" id="hero-source-badge">assets/videos/1.mp4</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Active Hero Background Video</h3>
                <p class="text-xs text-gray-500 mb-4">Loops continuously behind the main headline on the homepage.</p>
                <div class="relative rounded-xl overflow-hidden bg-black aspect-video mb-4 border border-gray-800 flex items-center justify-center">
                    <video id="preview-hero-video" class="w-full h-full object-cover" muted loop controls>
                        <source src="" type="video/mp4">
                    </video>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" id="manual-hero-url" placeholder="Path e.g. assets/videos/1.mp4" class="flex-1 text-xs border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                <button onclick="saveActiveVideo('hero', document.getElementById('manual-hero-url').value)" class="px-3 py-2 bg-black text-white rounded-lg text-xs font-semibold hover:bg-gray-800">
                    Apply
                </button>
            </div>
        </div>
    </div>

    <!-- Gallery Management Table / Grid -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Motorsports Video Gallery</h2>
                <p class="text-xs text-gray-500">All media uploaded or curated for public display on the homepage gallery.</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <span id="gallery-count" class="font-semibold text-gray-900">0</span> videos found
            </div>
        </div>

        <div id="media-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            <!-- Dynamically populated -->
            <div class="col-span-full text-center py-12 text-gray-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p>Loading media gallery...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Upload / Edit Media -->
<div id="mediaModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 id="modalTitle" class="text-lg font-bold text-gray-900">Add / Upload Video</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-black">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form id="mediaForm" onsubmit="handleMediaSubmit(event)" class="p-6 space-y-4">
            <input type="hidden" name="id" id="media-id" value="0">
            <input type="hidden" name="action" value="save_media">

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Title *</label>
                <input type="text" name="title" id="media-title" required placeholder="e.g. Autocross Championship Finals 2025" class="w-full text-sm border border-gray-300 rounded-lg p-2.5 focus:ring-1 focus:ring-black">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Category / Tag</label>
                <input type="text" name="category" id="media-category" placeholder="e.g. Drift & Gymkhana, Time Attack, Exhibition" class="w-full text-sm border border-gray-300 rounded-lg p-2.5 focus:ring-1 focus:ring-black">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Description</label>
                <textarea name="description" id="media-description" rows="2" placeholder="Brief summary of this video clip" class="w-full text-sm border border-gray-300 rounded-lg p-2.5 focus:ring-1 focus:ring-black"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Video Source Type</label>
                <select name="video_source" id="media-source" onchange="toggleSourceFields()" class="w-full text-sm border border-gray-300 rounded-lg p-2.5 bg-white">
                    <option value="local">Local MP4 / Server File</option>
                    <option value="youtube">YouTube Video</option>
                    <option value="vimeo">Vimeo Video</option>
                </select>
            </div>

            <!-- Upload File Input (for local) -->
            <div id="file-upload-block">
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Upload Video File (.mp4, .webm)</label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-black transition-colors cursor-pointer" onclick="document.getElementById('video_file').click()">
                    <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-1"></i>
                    <p class="text-xs text-gray-600 font-medium" id="upload-label">Click to select video file</p>
                    <p class="text-[10px] text-gray-400">MP4, WebM or MOV up to server limit</p>
                    <input type="file" name="video_file" id="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime" class="hidden" onchange="updateFileLabel(this)">
                </div>
            </div>

            <!-- Direct URL / Path -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1" id="url-label">File Path or Link</label>
                <input type="text" name="file_url" id="media-file-url" placeholder="e.g. assets/videos/1.mp4 or YouTube URL" class="w-full text-sm border border-gray-300 rounded-lg p-2.5 focus:ring-1 focus:ring-black">
                <p class="text-[11px] text-gray-400 mt-1">If selecting existing server file, enter path (e.g. assets/videos/1.mp4). For YouTube, paste full link.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="media-sort" value="0" class="w-full text-sm border border-gray-300 rounded-lg p-2.5 focus:ring-1 focus:ring-black">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status</label>
                    <select name="is_active" id="media-active" class="w-full text-sm border border-gray-300 rounded-lg p-2.5 bg-white">
                        <option value="1">Active / Published</option>
                        <option value="0">Hidden / Draft</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" id="modal-submit-btn" class="px-5 py-2 bg-black text-white rounded-lg text-sm font-semibold hover:bg-gray-800 flex items-center gap-2">
                    <span>Save Video</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentSettings = { splash_video_url: 'assets/videos/2.mp4', hero_video_url: 'assets/videos/1.mp4' };
let allMedia = [];

document.addEventListener('DOMContentLoaded', () => {
    loadMediaData();
});

function loadMediaData() {
    fetch('api/media.php')
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                allMedia = res.data.media || [];
                currentSettings = res.data.settings || currentSettings;
                renderStatusCards();
                renderGalleryGrid();
            } else {
                alert('Failed to load media: ' + (res.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error loading media data.');
        });
}

function renderStatusCards() {
    // Splash Card
    document.getElementById('splash-source-badge').textContent = currentSettings.splash_video_url;
    document.getElementById('manual-splash-url').value = currentSettings.splash_video_url;
    const splashVideo = document.getElementById('preview-splash-video');
    splashVideo.src = '../' + currentSettings.splash_video_url;

    // Hero Card
    document.getElementById('hero-source-badge').textContent = currentSettings.hero_video_url;
    document.getElementById('manual-hero-url').value = currentSettings.hero_video_url;
    const heroVideo = document.getElementById('preview-hero-video');
    heroVideo.src = '../' + currentSettings.hero_video_url;
}

function renderGalleryGrid() {
    const grid = document.getElementById('media-grid');
    document.getElementById('gallery-count').textContent = allMedia.length;

    if (!allMedia.length) {
        grid.innerHTML = `
            <div class="col-span-full text-center py-12 text-gray-400">
                <i class="fas fa-video-slash text-4xl mb-3 text-gray-300"></i>
                <p class="font-medium text-gray-600">No media gallery items yet.</p>
                <button onclick="openUploadModal()" class="mt-4 px-4 py-2 bg-black text-white text-xs rounded-lg font-semibold">Upload First Video</button>
            </div>
        `;
        return;
    }

    grid.innerHTML = allMedia.map(item => {
        const isSplash = currentSettings.splash_video_url === item.file_url;
        const isHero = currentSettings.hero_video_url === item.file_url;

        let videoPreviewHtml = '';
        if (item.video_source === 'youtube' && item.embed_id) {
            videoPreviewHtml = `<iframe class="w-full h-full" src="https://www.youtube.com/embed/${item.embed_id}" allowfullscreen></iframe>`;
        } else if (item.video_source === 'vimeo' && item.embed_id) {
            videoPreviewHtml = `<iframe class="w-full h-full" src="https://player.vimeo.com/video/${item.embed_id}" allowfullscreen></iframe>`;
        } else {
            videoPreviewHtml = `
                <video class="w-full h-full object-cover" controls preload="metadata">
                    <source src="../${item.file_url}" type="video/mp4">
                </video>
            `;
        }

        return `
            <div class="bg-gray-50 border border-gray-200 rounded-xl overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="relative aspect-video bg-black">
                        ${videoPreviewHtml}
                        <div class="absolute top-2 left-2 flex gap-1">
                            <span class="px-2 py-0.5 text-[10px] uppercase font-bold tracking-wider rounded bg-black/70 text-white backdrop-blur-sm">
                                ${item.category || 'Motorsport'}
                            </span>
                        </div>
                        <div class="absolute top-2 right-2 flex gap-1">
                            ${isSplash ? '<span class="px-2 py-0.5 text-[10px] font-bold rounded bg-red-600 text-white">SPLASH</span>' : ''}
                            ${isHero ? '<span class="px-2 py-0.5 text-[10px] font-bold rounded bg-blue-600 text-white">HERO</span>' : ''}
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h4 class="font-bold text-gray-900 text-sm leading-snug">${escapeHtml(item.title)}</h4>
                            <span class="text-[11px] px-2 py-0.5 rounded-full ${item.is_active == 1 ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'}">
                                ${item.is_active == 1 ? 'Active' : 'Hidden'}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 line-clamp-2 mb-2">${escapeHtml(item.description || 'No description provided.')}</p>
                        <p class="text-[10px] font-mono text-gray-400 truncate" title="${item.file_url}">${item.file_url}</p>
                    </div>
                </div>

                <div class="p-4 pt-0 border-t border-gray-200/50 mt-2 space-y-2">
                    <!-- Quick Set Broadcast Buttons -->
                    <div class="grid grid-cols-2 gap-1.5 pt-2">
                        <button onclick="saveActiveVideo('splash', '${item.file_url}')" class="px-2 py-1.5 text-[11px] font-semibold border rounded-lg transition-colors flex items-center justify-center gap-1 ${isSplash ? 'bg-red-50 text-red-700 border-red-300 font-bold' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300'}">
                            <i class="fas fa-bolt text-[10px]"></i> ${isSplash ? 'Current Splash' : 'Set as Splash'}
                        </button>
                        <button onclick="saveActiveVideo('hero', '${item.file_url}')" class="px-2 py-1.5 text-[11px] font-semibold border rounded-lg transition-colors flex items-center justify-center gap-1 ${isHero ? 'bg-blue-50 text-blue-700 border-blue-300 font-bold' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300'}">
                            <i class="fas fa-film text-[10px]"></i> ${isHero ? 'Current Hero' : 'Set as Hero'}
                        </button>
                    </div>

                    <!-- Manage Actions -->
                    <div class="flex items-center justify-between pt-1">
                        <button onclick="toggleActive(${item.id}, ${item.is_active == 1 ? 0 : 1})" class="text-xs text-gray-500 hover:text-black">
                            <i class="fas ${item.is_active == 1 ? 'fa-eye-slash' : 'fa-eye'} mr-1"></i> ${item.is_active == 1 ? 'Hide' : 'Show'}
                        </button>
                        <div class="flex items-center gap-2">
                            <button onclick="editMedia(${item.id})" class="text-xs text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="deleteMedia(${item.id})" class="text-xs text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function saveActiveVideo(target, videoUrl) {
    if (!videoUrl) {
        alert('Please provide a valid video URL or path.');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'set_active_video');
    formData.append('target', target);
    formData.append('video_url', videoUrl);

    fetch('api/media.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            if (target === 'splash') currentSettings.splash_video_url = videoUrl;
            if (target === 'hero') currentSettings.hero_video_url = videoUrl;
            renderStatusCards();
            renderGalleryGrid();
            alert(res.message);
        } else {
            alert(res.message || 'Error updating video setting.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Network error while saving video setting.');
    });
}

function openUploadModal() {
    document.getElementById('mediaForm').reset();
    document.getElementById('media-id').value = '0';
    document.getElementById('modalTitle').textContent = 'Add / Upload Video';
    document.getElementById('upload-label').textContent = 'Click to select video file';
    toggleSourceFields();
    document.getElementById('mediaModal').classList.remove('hidden');
}

function editMedia(id) {
    const item = allMedia.find(m => m.id == id);
    if (!item) return;

    document.getElementById('media-id').value = item.id;
    document.getElementById('modalTitle').textContent = 'Edit Media Item';
    document.getElementById('media-title').value = item.title;
    document.getElementById('media-category').value = item.category || 'Motorsports';
    document.getElementById('media-description').value = item.description || '';
    document.getElementById('media-source').value = item.video_source || 'local';
    document.getElementById('media-file-url').value = item.file_url;
    document.getElementById('media-sort').value = item.sort_order || 0;
    document.getElementById('media-active').value = item.is_active;

    toggleSourceFields();
    document.getElementById('mediaModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('mediaModal').classList.add('hidden');
}

function toggleSourceFields() {
    const source = document.getElementById('media-source').value;
    const fileBlock = document.getElementById('file-upload-block');
    const urlLabel = document.getElementById('url-label');

    if (source === 'local') {
        fileBlock.classList.remove('hidden');
        urlLabel.textContent = 'Or Existing File Path';
    } else {
        fileBlock.classList.add('hidden');
        urlLabel.textContent = (source === 'youtube' ? 'YouTube Video URL or ID' : 'Vimeo Video URL');
    }
}

function updateFileLabel(input) {
    if (input.files && input.files[0]) {
        document.getElementById('upload-label').textContent = input.files[0].name + ' (' + (input.files[0].size / 1024 / 1024).toFixed(1) + ' MB)';
    }
}

function handleMediaSubmit(e) {
    e.preventDefault();
    const form = document.getElementById('mediaForm');
    const submitBtn = document.getElementById('modal-submit-btn');
    const origHtml = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    submitBtn.disabled = true;

    const formData = new FormData(form);

    fetch('api/media.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 'success') {
            closeModal();
            loadMediaData();
        } else {
            alert(res.message || 'Failed to save media.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Upload failed or server rejected file.');
    })
    .finally(() => {
        submitBtn.innerHTML = origHtml;
        submitBtn.disabled = false;
    });
}

function toggleActive(id, newStatus) {
    const formData = new FormData();
    formData.append('action', 'toggle_active');
    formData.append('id', id);
    formData.append('is_active', newStatus);

    fetch('api/media.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') loadMediaData();
            else alert(res.message);
        });
}

function deleteMedia(id) {
    if (!confirm('Are you sure you want to delete this media item? Local uploaded files will also be removed.')) return;

    const formData = new FormData();
    formData.append('action', 'delete_media');
    formData.append('id', id);

    fetch('api/media.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') loadMediaData();
            else alert(res.message);
        });
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, function(m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
    });
}
</script>

<?php include 'includes/footer.php'; ?>
