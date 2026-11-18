<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();

$id = intval($_GET['id'] ?? 0);
$isEdit = ($id > 0);
$event = [
    'id' => 0,
    'title' => '',
    'slug' => '',
    'event_type' => 'upcoming',
    'badge_text' => 'Coming Soon',
    'event_date_text' => '',
    'event_date' => '',
    'description' => '',
    'image_url' => '',
    'gradient_theme' => 'from-white to-gray-300',
    'button_text' => 'Learn More',
    'button_link' => '',
    'sort_order' => 0,
    'is_active' => 1
];

if ($isEdit && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $fetched = $stmt->fetch();
        if ($fetched) {
            $event = array_merge($event, $fetched);
        } else {
            header('Location: events.php?error=not_found');
            exit;
        }
    } catch (PDOException $e) {
        error_log('Event load error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit Event: ' . htmlspecialchars($event['title']) : 'Create New Event'; ?> - Krosskhana Admin</title>
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
    <!-- Top Navigation Bar -->
    <nav class="border-b border-gray-800 bg-dark-900/80 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="events.php" class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-800/60 transition">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                            <?php echo $isEdit ? 'Edit Event' : 'Create New Event'; ?>
                        </h1>
                        <p class="text-xs text-gray-400">Events Management &rsaquo; <?php echo $isEdit ? htmlspecialchars($event['title']) : 'New Event'; ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="events.php" class="px-4 py-2 border border-gray-700 hover:border-gray-600 text-gray-300 rounded-xl text-xs font-medium hover:bg-gray-800/40 transition">
                        Cancel
                    </a>
                    <button type="submit" form="event-page-form" id="btn-submit-page" class="px-5 py-2 bg-gradient-to-r from-white to-gray-200 text-black font-semibold rounded-xl text-xs hover:from-gray-100 hover:to-gray-300 transition shadow-lg flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span id="btn-submit-text"><?php echo $isEdit ? 'Save Changes' : 'Publish Event'; ?></span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Toast Notification Area -->
        <div id="toast" class="hidden fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 transition-all duration-300"></div>

        <form id="event-page-form" onsubmit="handleFormSubmit(event)" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'create'; ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
            <?php endif; ?>

            <!-- Left 2 Columns: Main Event Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Card 1: Core Details -->
                <div class="bg-dark-900/60 border border-gray-800/80 backdrop-blur-xl rounded-2xl p-6 shadow-xl space-y-5">
                    <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-info-circle text-gray-400"></i> Event Details
                    </h2>

                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-2">Event Title <span class="text-red-400">*</span></label>
                        <input type="text" name="title" id="inp-title" value="<?php echo htmlspecialchars($event['title']); ?>" required placeholder="e.g. Avant Gardex 2026" class="w-full bg-dark-950/80 border border-gray-800 rounded-xl px-4 py-3 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-gray-500 transition" oninput="updateLivePreview()">
                    </div>

                    <!-- Slug -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-2">Slug (URL identifier)</label>
                        <input type="text" name="slug" id="inp-slug" value="<?php echo htmlspecialchars($event['slug']); ?>" placeholder="auto-generated-from-title" class="w-full bg-dark-950/80 border border-gray-800 rounded-xl px-4 py-2.5 text-xs text-gray-300 placeholder-gray-600 focus:outline-none focus:border-gray-500 transition">
                        <span class="text-[11px] text-gray-500 mt-1 block">Leave blank to generate automatically from title.</span>
                    </div>

                    <!-- Event Type Selector -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-2">Event Category <span class="text-red-400">*</span></label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative flex items-center p-4 border border-gray-800 rounded-xl cursor-pointer hover:border-gray-700 transition bg-dark-950/40">
                                <input type="radio" name="event_type" value="upcoming" <?php echo $event['event_type'] === 'upcoming' ? 'checked' : ''; ?> class="text-white focus:ring-0" onchange="handleTypeChange('upcoming')">
                                <div class="ml-3">
                                    <span class="block text-sm font-semibold text-white">Upcoming Event</span>
                                    <span class="block text-xs text-gray-400 mt-0.5">Show under 'Coming Soon' carousel</span>
                                </div>
                            </label>
                            <label class="relative flex items-center p-4 border border-gray-800 rounded-xl cursor-pointer hover:border-gray-700 transition bg-dark-950/40">
                                <input type="radio" name="event_type" value="past" <?php echo $event['event_type'] === 'past' ? 'checked' : ''; ?> class="text-white focus:ring-0" onchange="handleTypeChange('past')">
                                <div class="ml-3">
                                    <span class="block text-sm font-semibold text-white">Past Event</span>
                                    <span class="block text-xs text-gray-400 mt-0.5">Show in historical gallery / archive</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Date Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-2">Date Display Text <span class="text-red-400">*</span></label>
                            <input type="text" name="event_date_text" id="inp-date-text" value="<?php echo htmlspecialchars($event['event_date_text']); ?>" required placeholder="e.g. December 2026, May 15-18" class="w-full bg-dark-950/80 border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-gray-500 transition" oninput="updateLivePreview()">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-2">Specific Date (for calendar sorting)</label>
                            <input type="date" name="event_date" id="inp-date" value="<?php echo htmlspecialchars($event['event_date'] ?? ''); ?>" class="w-full bg-dark-950/80 border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-gray-500 transition">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-2">Description <span class="text-red-400">*</span></label>
                        <textarea name="description" id="inp-desc" rows="4" required placeholder="Describe the highlights, competitions, attendees, and special attractions of this event..." class="w-full bg-dark-950/80 border border-gray-800 rounded-xl p-4 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-gray-500 transition" oninput="updateLivePreview()"><?php echo htmlspecialchars($event['description']); ?></textarea>
                    </div>
                </div>

                <!-- Card 2: Call to Action & Badges -->
                <div class="bg-dark-900/60 border border-gray-800/80 backdrop-blur-xl rounded-2xl p-6 shadow-xl space-y-5">
                    <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-tag text-gray-400"></i> Badges & Call to Action
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Badge -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-2">Badge Text</label>
                            <input type="text" name="badge_text" id="inp-badge" value="<?php echo htmlspecialchars($event['badge_text']); ?>" placeholder="Coming Soon / Past Event" class="w-full bg-dark-950/80 border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-gray-500 transition" oninput="updateLivePreview()">
                        </div>

                        <!-- Button Text -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-2">Button Text</label>
                            <input type="text" name="button_text" id="inp-button-text" value="<?php echo htmlspecialchars($event['button_text']); ?>" placeholder="Learn More / View Details" class="w-full bg-dark-950/80 border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-gray-500 transition" oninput="updateLivePreview()">
                        </div>
                    </div>

                    <!-- Button Link -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-2">Button Link URL</label>
                        <input type="text" name="button_link" id="inp-button-link" value="<?php echo htmlspecialchars($event['button_link'] ?? ''); ?>" placeholder="https://... or #participate or racer-registration.php" class="w-full bg-dark-950/80 border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-gray-500 transition">
                    </div>
                </div>

                <!-- Card 3: Theme Gradient -->
                <div class="bg-dark-900/60 border border-gray-800/80 backdrop-blur-xl rounded-2xl p-6 shadow-xl space-y-4">
                    <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-palette text-gray-400"></i> Card Visual Gradient
                    </h2>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <?php 
                        $gradients = [
                            'from-white to-gray-300' => 'Monochrome Silver',
                            'from-gray-300 to-white' => 'Inverted Silver',
                            'from-gray-800 to-black' => 'Deep Carbon',
                            'from-red-700 to-gray-900' => 'Racing Red'
                        ];
                        foreach ($gradients as $val => $label):
                            $checked = ($event['gradient_theme'] === $val);
                        ?>
                        <label class="border border-gray-800 rounded-xl p-3 flex flex-col items-center cursor-pointer hover:border-gray-600 transition bg-dark-950/40">
                            <input type="radio" name="gradient_theme" value="<?php echo $val; ?>" <?php echo $checked ? 'checked' : ''; ?> class="sr-only" onchange="updateLivePreview()">
                            <div class="w-full h-8 rounded-lg bg-gradient-to-r <?php echo $val; ?> mb-2 border border-gray-700/50"></div>
                            <span class="text-[11px] text-gray-300 font-medium text-center"><?php echo $label; ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Image Upload & Live Preview -->
            <div class="space-y-6">
                <!-- Image Upload Card -->
                <div class="bg-dark-900/60 border border-gray-800/80 backdrop-blur-xl rounded-2xl p-6 shadow-xl space-y-4">
                    <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-image text-gray-400"></i> Event Image / Poster
                    </h2>

                    <!-- Hidden remove flag -->
                    <input type="hidden" name="remove_image" id="remove-image-flag" value="0">
                    <input type="hidden" name="image_url" id="inp-image-url" value="<?php echo htmlspecialchars($event['image_url'] ?? ''); ?>">

                    <!-- Current image display or upload box -->
                    <div id="image-upload-box" class="border-2 border-dashed border-gray-800 hover:border-gray-600 rounded-2xl p-6 text-center cursor-pointer transition bg-dark-950/40 relative" onclick="document.getElementById('file-input').click()">
                        <input type="file" name="image_file" id="file-input" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden" onchange="handleFileSelect(this)">
                        
                        <div id="upload-placeholder" class="<?php echo !empty($event['image_url']) ? 'hidden' : ''; ?>">
                            <div class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                <i class="fas fa-cloud-upload-alt text-xl"></i>
                            </div>
                            <p class="text-xs font-medium text-gray-300">Click to upload image</p>
                            <p class="text-[11px] text-gray-500 mt-1">PNG, JPG, WEBP up to 5MB</p>
                        </div>

                        <div id="image-preview-container" class="<?php echo empty($event['image_url']) ? 'hidden' : ''; ?> relative">
                            <img id="image-preview" src="<?php echo !empty($event['image_url']) ? '../' . htmlspecialchars($event['image_url']) : ''; ?>" alt="Preview" class="w-full h-40 object-cover rounded-xl shadow-lg border border-gray-700/50 mb-3">
                            <button type="button" onclick="removeImage(event)" class="text-xs text-red-400 hover:text-red-300 transition flex items-center gap-1 mx-auto bg-red-950/40 px-3 py-1.5 rounded-lg border border-red-800/40">
                                <i class="fas fa-trash-alt"></i> Remove photo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Live Preview Card -->
                <div class="bg-dark-900/60 border border-gray-800/80 backdrop-blur-xl rounded-2xl p-6 shadow-xl space-y-4">
                    <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wider flex items-center justify-between">
                        <span class="flex items-center gap-2"><i class="fas fa-eye text-gray-400"></i> Live Card Preview</span>
                        <span class="text-[10px] text-gray-500">As shown on website</span>
                    </h2>

                    <!-- Preview Container -->
                    <div class="bg-gray-900/90 rounded-2xl border border-gray-800 overflow-hidden shadow-2xl">
                        <!-- Top Image / Gradient Area -->
                        <div id="prev-header" class="relative h-44 bg-gradient-to-br from-white to-gray-300 overflow-hidden transition-all">
                            <img id="prev-img" src="" alt="Event" class="absolute inset-0 w-full h-full object-cover hidden">
                            <div id="prev-overlay" class="absolute inset-0 bg-black/30"></div>
                            
                            <!-- Badge -->
                            <div class="absolute top-3 right-3 bg-gray-900/80 text-white px-2.5 py-1 rounded-full text-[11px] font-semibold border border-white/20 backdrop-blur-sm">
                                <span id="prev-badge"><?php echo htmlspecialchars($event['badge_text'] ?: 'Coming Soon'); ?></span>
                            </div>

                            <!-- Title in Header -->
                            <div class="absolute bottom-3 left-4 right-4">
                                <h3 id="prev-title" class="text-lg font-bold text-white truncate drop-shadow-md">
                                    <?php echo htmlspecialchars($event['title'] ?: 'Event Title'); ?>
                                </h3>
                                <p id="prev-date" class="text-xs text-gray-200 opacity-90 drop-shadow">
                                    <?php echo htmlspecialchars($event['event_date_text'] ?: 'Event Date'); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 space-y-4">
                            <p id="prev-desc" class="text-xs text-gray-300 line-clamp-3 leading-relaxed">
                                <?php echo htmlspecialchars($event['description'] ?: 'Event description will appear here as you type in the form.'); ?>
                            </p>
                            <div>
                                <button type="button" class="w-full bg-gradient-to-r from-gray-300 to-white text-black font-semibold py-2 px-4 rounded-xl text-xs hover:opacity-90 transition shadow">
                                    <span id="prev-btn-text"><?php echo htmlspecialchars($event['button_text'] ?: 'Learn More'); ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings & Status Card -->
                <div class="bg-dark-900/60 border border-gray-800/80 backdrop-blur-xl rounded-2xl p-6 shadow-xl space-y-4">
                    <h2 class="text-sm font-semibold text-gray-300 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-sliders-h text-gray-400"></i> Publishing Status
                    </h2>

                    <div class="flex items-center justify-between p-3 bg-dark-950/40 rounded-xl border border-gray-800">
                        <div>
                            <span class="block text-xs font-semibold text-white">Active Status</span>
                            <span class="block text-[11px] text-gray-400">Visible to website visitors</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" <?php echo $event['is_active'] ? 'checked' : ''; ?> class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-300 mb-1">Display Sort Order</label>
                        <input type="number" name="sort_order" value="<?php echo intval($event['sort_order']); ?>" class="w-full bg-dark-950/80 border border-gray-800 rounded-xl px-4 py-2 text-xs text-white">
                        <span class="text-[10px] text-gray-500 mt-1 block">Lower numbers appear first (e.g. 1, 2, 3).</span>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function updateLivePreview() {
            const title = document.getElementById('inp-title').value.trim() || 'Event Title';
            const dateText = document.getElementById('inp-date-text').value.trim() || 'Event Date';
            const desc = document.getElementById('inp-desc').value.trim() || 'Event description will appear here as you type in the form.';
            const badge = document.getElementById('inp-badge').value.trim() || 'Coming Soon';
            const btnText = document.getElementById('inp-button-text').value.trim() || 'Learn More';

            document.getElementById('prev-title').innerText = title;
            document.getElementById('prev-date').innerText = dateText;
            document.getElementById('prev-desc').innerText = desc;
            document.getElementById('prev-badge').innerText = badge;
            document.getElementById('prev-btn-text').innerText = btnText;

            // Gradient
            const selectedGrad = document.querySelector('input[name="gradient_theme"]:checked');
            const gradClass = selectedGrad ? selectedGrad.value : 'from-white to-gray-300';
            const prevHeader = document.getElementById('prev-header');
            prevHeader.className = `relative h-44 bg-gradient-to-br ${gradClass} overflow-hidden transition-all`;
        }

        function handleTypeChange(type) {
            const badgeInput = document.getElementById('inp-badge');
            const btnInput = document.getElementById('inp-button-text');
            if (type === 'past') {
                if (!badgeInput.value || badgeInput.value === 'Coming Soon') badgeInput.value = 'Past Event';
                if (!btnInput.value || btnInput.value === 'Learn More') btnInput.value = 'View Gallery';
            } else {
                if (!badgeInput.value || badgeInput.value === 'Past Event') badgeInput.value = 'Coming Soon';
                if (!btnInput.value || btnInput.value === 'View Gallery') btnInput.value = 'Learn More';
            }
            updateLivePreview();
        }

        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').src = e.target.result;
                    document.getElementById('upload-placeholder').classList.add('hidden');
                    document.getElementById('image-preview-container').classList.remove('hidden');

                    // Live preview update
                    const prevImg = document.getElementById('prev-img');
                    prevImg.src = e.target.result;
                    prevImg.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
                document.getElementById('remove-image-flag').value = '0';
            }
        }

        function removeImage(e) {
            e.stopPropagation();
            document.getElementById('file-input').value = '';
            document.getElementById('inp-image-url').value = '';
            document.getElementById('remove-image-flag').value = '1';
            document.getElementById('image-preview-container').classList.add('hidden');
            document.getElementById('upload-placeholder').classList.remove('hidden');

            const prevImg = document.getElementById('prev-img');
            prevImg.src = '';
            prevImg.classList.add('hidden');
        }

        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerText = msg;
            toast.className = `fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-2xl flex items-center gap-3 transition-all duration-300 ${type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'}`;
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.add('hidden'); }, 3500);
        }

        function handleFormSubmit(e) {
            e.preventDefault();
            const form = document.getElementById('event-page-form');
            const formData = new FormData(form);
            const btn = document.getElementById('btn-submit-page');
            const originalText = document.getElementById('btn-submit-text').innerText;

            btn.disabled = true;
            document.getElementById('btn-submit-text').innerText = 'Saving...';

            fetch('api/events.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                document.getElementById('btn-submit-text').innerText = originalText;

                if (data.status === 'success') {
                    showToast(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = 'events.php';
                    }, 1000);
                } else {
                    showToast(data.message || 'Failed to save event', 'error');
                }
            })
            .catch(err => {
                btn.disabled = false;
                document.getElementById('btn-submit-text').innerText = originalText;
                console.error('Submit error:', err);
                showToast('Failed to save event. Check console or server logs.', 'error');
            });
        }

        // Initialize preview on page load
        window.addEventListener('DOMContentLoaded', () => {
            const currentImg = document.getElementById('image-preview').getAttribute('src');
            if (currentImg) {
                const prevImg = document.getElementById('prev-img');
                prevImg.src = currentImg;
                prevImg.classList.remove('hidden');
            }
            updateLivePreview();
        });
    </script>
</body>
</html>
