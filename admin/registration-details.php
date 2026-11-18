<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';

requireAdminAuth();

$pdo = getDBConnection();
ensureCoreTables($pdo);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$registration = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM racer_registrations WHERE id = ?");
    $stmt->execute([$id]);
    $registration = $stmt->fetch();
}

if (!$registration) {
    header("Location: registrations.php");
    exit;
}

$pageTitle = 'Driver Profile: ' . htmlspecialchars($registration['full_name']);
require_once __DIR__ . '/includes/sidebar.php';

// Disciplines
$disciplines = [];
if ($registration['autocross_competitor']) $disciplines[] = 'Autocross Competitor';
if ($registration['drifting_challenge']) $disciplines[] = 'Drifting Challenge';
if ($registration['exhibition_driver']) $disciplines[] = 'Exhibition Driver';
if ($registration['safety_training']) $disciplines[] = 'Safety Training';

// Experience
$exp = 'Not Specified';
if ($registration['professional']) $exp = 'Professional Driver';
elseif ($registration['semi_pro']) $exp = 'Semi-Professional';
elseif ($registration['intermediate']) $exp = 'Intermediate Track Day';
elseif ($registration['novice']) $exp = 'Novice / Beginner';
?>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Actions -->
    <div class="flex items-center justify-between">
        <a href="registrations.php" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-black font-semibold">
            <i class="fas fa-arrow-left"></i> Back to Registrations
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-3.5 py-2 text-xs font-semibold bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 flex items-center gap-1.5">
                <i class="fas fa-print"></i> Print Dossier
            </button>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Hero Header -->
        <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-black p-8 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center text-3xl font-black border border-white/20">
                        <i class="fas fa-flag-checkered"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black"><?php echo htmlspecialchars($registration['full_name']); ?></h1>
                        <p class="text-gray-300 text-sm mt-0.5">Racing Alias: <span class="font-bold text-white"><?php echo htmlspecialchars($registration['racing_alias'] ?: 'None'); ?></span></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wider <?php
                        echo match($registration['status']) {
                            'approved' => 'bg-green-500/20 text-green-300 border border-green-500/40',
                            'contacted' => 'bg-blue-500/20 text-blue-300 border border-blue-500/40',
                            'rejected' => 'bg-red-500/20 text-red-300 border border-red-500/40',
                            default => 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/40'
                        };
                    ?>">
                        <?php echo htmlspecialchars($registration['status']); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="p-8 space-y-8">
            <!-- Contact & Personal Details -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Email Address</p>
                        <a href="mailto:<?php echo htmlspecialchars($registration['email']); ?>" class="text-sm font-bold text-blue-600 hover:underline mt-1 block">
                            <?php echo htmlspecialchars($registration['email']); ?>
                        </a>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Phone Number</p>
                        <a href="tel:<?php echo htmlspecialchars($registration['phone']); ?>" class="text-sm font-bold text-gray-900 hover:underline mt-1 block">
                            <?php echo htmlspecialchars($registration['phone']); ?>
                        </a>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Location</p>
                        <p class="text-sm font-bold text-gray-900 mt-1"><?php echo htmlspecialchars($registration['location'] ?: 'Not specified'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Vehicle Details -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Vehicle Specification</h3>
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Make</p>
                            <p class="text-base font-bold text-gray-900 mt-1"><?php echo htmlspecialchars($registration['vehicle_make'] ?: 'N/A'); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Model</p>
                            <p class="text-base font-bold text-gray-900 mt-1"><?php echo htmlspecialchars($registration['vehicle_model'] ?: 'N/A'); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Year</p>
                            <p class="text-base font-bold text-gray-900 mt-1"><?php echo htmlspecialchars($registration['vehicle_year'] ?: 'N/A'); ?></p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-500 font-medium mb-2">Modifications & Upgrades:</p>
                        <div class="p-4 bg-white rounded-lg border border-gray-200 text-sm text-gray-800 whitespace-pre-wrap">
                            <?php echo nl2br(htmlspecialchars($registration['modifications'] ?: 'Stock vehicle / No modifications stated.')); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discipline & Experience -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Racing Disciplines</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php if (!empty($disciplines)): ?>
                            <?php foreach ($disciplines as $d): ?>
                                <span class="px-3 py-1.5 rounded-lg bg-gray-900 text-white text-xs font-semibold flex items-center gap-1.5">
                                    <i class="fas fa-check text-green-400"></i> <?php echo htmlspecialchars($d); ?>
                                </span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-sm text-gray-400">General participant</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Experience Level</h3>
                    <span class="px-3.5 py-2 rounded-lg bg-indigo-50 text-indigo-900 border border-indigo-200 text-sm font-bold inline-flex items-center gap-2">
                        <i class="fas fa-trophy text-indigo-600"></i> <?php echo htmlspecialchars($exp); ?>
                    </span>
                </div>
            </div>

            <?php if (!empty($registration['additional_info'])): ?>
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Additional Information</h3>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-sm text-gray-800 whitespace-pre-wrap">
                    <?php echo nl2br(htmlspecialchars($registration['additional_info'])); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Admin Status Form -->
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-4">Admin Status & Notes</h3>
                <form id="details-update-form" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Driver Application Status</label>
                            <select id="update-status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium">
                                <option value="pending" <?php echo $registration['status'] === 'pending' ? 'selected' : ''; ?>>Pending Review</option>
                                <option value="approved" <?php echo $registration['status'] === 'approved' ? 'selected' : ''; ?>>Approved Driver</option>
                                <option value="contacted" <?php echo $registration['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                <option value="rejected" <?php echo $registration['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected / Ineligible</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Scrutineering / Administrative Notes</label>
                        <textarea id="update-notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="License verified, car scrutineering passed..."><?php echo htmlspecialchars($registration['admin_notes'] ?: ''); ?></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-black text-white font-bold rounded-lg hover:bg-gray-800 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('details-update-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const status = document.getElementById('update-status').value;
    const notes = document.getElementById('update-notes').value;

    fetch('api/registrations.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'update_status',
            id: <?php echo $registration['id']; ?>,
            status: status,
            admin_notes: notes
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Driver profile updated successfully');
            window.location.reload();
        } else {
            alert(data.message || 'Update failed');
        }
    })
    .catch(err => alert('Network error while saving changes'));
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
