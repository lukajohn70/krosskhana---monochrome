<?php
require_once __DIR__ . '/includes/config.php';

$pdo = getDBConnection();
ensureCoreTables($pdo);

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$event = null;

if (!empty($slug)) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE slug = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$slug]);
    $event = $stmt->fetch();
}

if (!$event && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
}

// Fallback if not found or no params: get the first upcoming or latest event
if (!$event) {
    $stmt = $pdo->query("SELECT * FROM events WHERE is_active = 1 ORDER BY event_type ASC, sort_order ASC, id DESC LIMIT 1");
    $event = $stmt ? $stmt->fetch() : null;
}

if (!$event) {
    header("Location: index.php#events");
    exit;
}

// Prepare display values
$title = htmlspecialchars($event['title']);
$description = htmlspecialchars($event['description']);
$eventType = $event['event_type'];
$badgeText = htmlspecialchars($event['badge_text'] ?: ($eventType === 'upcoming' ? 'Upcoming Event' : 'Past Event'));
$dateText = htmlspecialchars($event['event_date_text'] ?: 'Date TBA');
$imageUrl = !empty($event['image_url']) ? $event['image_url'] : 'pictures/1.png';
$buttonText = htmlspecialchars($event['button_text'] ?: ($eventType === 'upcoming' ? 'Register Now' : 'View Highlights'));
$buttonLink = trim($event['button_link'] ?? '');
$gradient = !empty($event['gradient_theme']) ? $event['gradient_theme'] : 'from-white to-gray-300';
$eventDate = $event['event_date'];
$isUpcoming = ($eventType === 'upcoming');

// Build canonical URL for OpenGraph
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$currentUrl = $protocol . ($_SERVER['HTTP_HOST'] ?? 'krosskhana.org') . $_SERVER['REQUEST_URI'];
$absoluteImageUrl = (strpos($imageUrl, 'http') === 0) ? $imageUrl : (rtrim(SITE_URL, '/') . '/' . ltrim($imageUrl, '/'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Krosskhana Motorsports</title>
    <meta name="description" content="<?php echo substr($description, 0, 160); ?>">

    <!-- OpenGraph / Social Sharing -->
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?php echo $title; ?> - Krosskhana">
    <meta property="og:description" content="<?php echo substr($description, 0, 200); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($absoluteImageUrl); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($currentUrl); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $title; ?>">
    <meta name="twitter:description" content="<?php echo substr($description, 0, 200); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($absoluteImageUrl); ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/logos/Krosskhana-05.png">
    <link rel="shortcut icon" type="image/png" href="assets/logos/Krosskhana-05.png">
    <link rel="apple-touch-icon" href="assets/logos/Krosskhana-05.png">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'Orbitron';
            src: url('assets/fonts/Orbitron-VariableFont_wght.ttf') format('truetype');
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }
    </style>

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
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-black text-white font-poppins min-h-screen flex flex-col selection:bg-white selection:text-black">
    <!-- Ambient Background Lighting -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute -top-40 left-1/4 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -right-40 w-96 h-96 bg-gray-500/5 rounded-full blur-3xl"></div>
    </div>

    <!-- Navigation -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-black/80 backdrop-blur-xl border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center space-x-3 group">
                <img src="assets/logos/Krosskhana-05.png" alt="Krosskhana Logo" class="h-10 w-auto group-hover:scale-105 transition-transform duration-300">
                <span class="font-orbitron font-bold tracking-wider text-sm hidden sm:inline-block">KROSSKHANA</span>
            </a>

            <div class="flex items-center space-x-3">
                <a href="index.php#events" class="px-4 py-2 text-xs sm:text-sm font-semibold text-gray-300 hover:text-white rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 transition-colors flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>All Events</span>
                </a>
                <a href="index.php#participate" class="px-5 py-2 text-xs sm:text-sm font-bold text-black bg-gradient-to-r from-white to-gray-300 rounded-lg hover:from-gray-100 hover:to-gray-400 transition-all shadow-lg hover:shadow-white/20">
                    Register
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 flex-1 pt-20">
        <!-- Hero Section with Poster Backdrop -->
        <section class="relative min-h-[60vh] flex items-end overflow-hidden border-b border-white/10">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0">
                <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo $title; ?>" class="w-full h-full object-cover object-center filter brightness-50 contrast-125 transform scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/80 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/40 to-black/90"></div>
            </div>

            <!-- Hero Details -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 w-full">
                <div class="max-w-3xl">
                    <!-- Status & Date Badges -->
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="px-3.5 py-1 rounded-full text-xs font-bold tracking-wider uppercase <?php echo $isUpcoming ? 'bg-white text-black' : 'bg-white/20 text-white backdrop-blur-md border border-white/20'; ?>">
                            <?php echo $badgeText; ?>
                        </span>
                        <span class="px-3.5 py-1 rounded-full text-xs font-semibold bg-white/10 backdrop-blur-md text-gray-200 border border-white/10 flex items-center gap-1.5">
                            <i class="fas fa-calendar-alt text-xs"></i>
                            <?php echo $dateText; ?>
                        </span>
                        <span class="px-3.5 py-1 rounded-full text-xs font-semibold bg-white/10 backdrop-blur-md text-gray-200 border border-white/10 flex items-center gap-1.5">
                            <i class="fas fa-map-marker-alt text-xs"></i>
                            Abuja, Nigeria
                        </span>
                    </div>

                    <!-- Main Title -->
                    <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white mb-6 drop-shadow-lg leading-tight font-orbitron">
                        <?php echo $title; ?>
                    </h1>

                    <!-- Lead Description -->
                    <p class="text-lg sm:text-xl text-gray-300 leading-relaxed drop-shadow mb-8 font-light">
                        <?php echo $description; ?>
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-wrap items-center gap-4">
                        <?php if (!empty($buttonLink)): ?>
                            <a href="<?php echo htmlspecialchars($buttonLink); ?>" class="px-8 py-3.5 bg-gradient-to-r from-white to-gray-300 text-black font-bold text-sm sm:text-base rounded-xl shadow-xl hover:shadow-white/20 transform hover:-translate-y-0.5 transition-all">
                                <?php echo $buttonText; ?>
                            </a>
                        <?php else: ?>
                            <a href="index.php#participate" class="px-8 py-3.5 bg-gradient-to-r from-white to-gray-300 text-black font-bold text-sm sm:text-base rounded-xl shadow-xl hover:shadow-white/20 transform hover:-translate-y-0.5 transition-all">
                                <?php echo $isUpcoming ? 'Secure Participation' : 'Register for Next Edition'; ?>
                            </a>
                        <?php endif; ?>

                        <a href="corporate-partnership.php" class="px-6 py-3.5 bg-white/10 backdrop-blur-md text-white hover:bg-white/20 border border-white/20 font-semibold text-sm sm:text-base rounded-xl transition-all">
                            Partner with Event
                        </a>

                        <button onclick="shareEvent()" class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-md hover:bg-white/20 border border-white/20 text-white flex items-center justify-center transition-colors" title="Share Event">
                            <i class="fas fa-share-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Countdown Bar (If upcoming date is set) -->
        <?php if ($isUpcoming && !empty($eventDate)): ?>
        <section class="bg-gradient-to-r from-gray-900 via-gray-950 to-gray-900 border-b border-white/10 py-6 px-4">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white border border-white/20">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm uppercase tracking-wider text-white">Event Countdown</h4>
                        <p class="text-xs text-gray-400">Time remaining until lights out</p>
                    </div>
                </div>

                <div id="countdown-timer" data-target="<?php echo htmlspecialchars($eventDate); ?>" class="flex items-center gap-4 text-center">
                    <div class="bg-black/60 border border-white/10 px-4 py-2 rounded-xl min-w-[70px]">
                        <span id="cd-days" class="text-2xl font-black text-white font-orbitron">00</span>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Days</p>
                    </div>
                    <span class="text-gray-600 font-bold">:</span>
                    <div class="bg-black/60 border border-white/10 px-4 py-2 rounded-xl min-w-[70px]">
                        <span id="cd-hours" class="text-2xl font-black text-white font-orbitron">00</span>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Hours</p>
                    </div>
                    <span class="text-gray-600 font-bold">:</span>
                    <div class="bg-black/60 border border-white/10 px-4 py-2 rounded-xl min-w-[70px]">
                        <span id="cd-minutes" class="text-2xl font-black text-white font-orbitron">00</span>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Mins</p>
                    </div>
                    <span class="text-gray-600 font-bold">:</span>
                    <div class="bg-black/60 border border-white/10 px-4 py-2 rounded-xl min-w-[70px]">
                        <span id="cd-seconds" class="text-2xl font-black text-white font-orbitron">00</span>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Secs</p>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Event Explanatory Body -->
        <section class="py-16 sm:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Main Narrative -->
                <div class="lg:col-span-2 space-y-12">
                    <!-- About Experience -->
                    <div>
                        <div class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/10 text-gray-300 border border-white/10 mb-3">
                            The Experience
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-black text-white mb-6 font-orbitron">
                            Pure Adrenaline Meets Automotive Culture
                        </h2>
                        <div class="prose prose-invert max-w-none text-gray-300 space-y-4 leading-relaxed">
                            <p>
                                Krosskhana represents Nigeria's premier sanctioned automotive spectacle, merging precision motorsport performance, avant-garde design, and high-energy urban entertainment.
                            </p>
                            <p>
                                Whether you're competing on the autocross circuit, pushing lateral limits in the drift arena, or joining our exclusive VIP paddock, every element is engineered for maximum immersion, safety, and sensory thrill.
                            </p>
                        </div>
                    </div>

                    <!-- Event Highlights Grid -->
                    <div>
                        <h3 class="text-xl font-bold text-white mb-6 font-orbitron flex items-center gap-2">
                            <i class="fas fa-bolt text-gray-400"></i> Event Attractions & Highlights
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-5 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 hover:border-white/30 transition-all">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white mb-4">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <h4 class="font-bold text-white text-base mb-1">Autocross & Drift Battle</h4>
                                <p class="text-xs text-gray-400 leading-relaxed">Precision time attacks and tandem smoke battles with top-tier national competitors.</p>
                            </div>

                            <div class="p-5 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 hover:border-white/30 transition-all">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white mb-4">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <h4 class="font-bold text-white text-base mb-1">VIP Paddock & Pit Lane</h4>
                                <p class="text-xs text-gray-400 leading-relaxed">Exclusive access to build teams, drivers, trackside hospitality, and celebrity guests.</p>
                            </div>

                            <div class="p-5 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 hover:border-white/30 transition-all">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white mb-4">
                                    <i class="fas fa-car-side"></i>
                                </div>
                                <h4 class="font-bold text-white text-base mb-1">Supercar & Tuner Showcase</h4>
                                <p class="text-xs text-gray-400 leading-relaxed">Curated exhibition of high-performance imports, rare exotics, and bespoke custom engineering.</p>
                            </div>

                            <div class="p-5 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 hover:border-white/30 transition-all">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white mb-4">
                                    <i class="fas fa-music"></i>
                                </div>
                                <h4 class="font-bold text-white text-base mb-1">Lifestyle & Sound Stage</h4>
                                <p class="text-xs text-gray-400 leading-relaxed">Live DJ sets, streetwear activations, culinary experiences, and brand pop-ups.</p>
                            </div>
                        </div>
                    </div>

                    <!-- High-Res Gallery Preview -->
                    <div>
                        <h3 class="text-xl font-bold text-white mb-6 font-orbitron flex items-center gap-2">
                            <i class="fas fa-images text-gray-400"></i> Event Imagery
                        </h3>
                        <div class="relative rounded-2xl overflow-hidden border border-white/20 shadow-2xl bg-black group">
                            <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo $title; ?>" class="w-full h-80 sm:h-96 object-cover object-center group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex items-end p-6">
                                <div>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-300">Official Event Poster</span>
                                    <h4 class="text-lg font-bold text-white"><?php echo $title; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Dossier -->
                <div class="space-y-6">
                    <!-- Event Specs Card -->
                    <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/10 space-y-6">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 font-orbitron">Event Details</h3>

                        <div class="space-y-4 text-sm">
                            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                                <span class="text-gray-400">Category</span>
                                <span class="font-semibold text-white capitalize"><?php echo $eventType; ?> Event</span>
                            </div>
                            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                                <span class="text-gray-400">Date</span>
                                <span class="font-semibold text-white"><?php echo $dateText; ?></span>
                            </div>
                            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                                <span class="text-gray-400">Host City</span>
                                <span class="font-semibold text-white">Abuja, Nigeria</span>
                            </div>
                            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                                <span class="text-gray-400">Scrutineering</span>
                                <span class="font-semibold text-emerald-400">FIA & ASN Standards</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Spectator Capacity</span>
                                <span class="font-semibold text-white">2,500+ Guests</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-white/10 space-y-3">
                            <a href="index.php#participate" class="w-full text-center block py-3.5 bg-gradient-to-r from-white to-gray-300 text-black font-bold rounded-xl shadow-lg hover:shadow-white/20 transition-all text-sm">
                                Register as Racer
                            </a>
                            <a href="corporate-partnership.php" class="w-full text-center block py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl border border-white/20 transition-all text-sm">
                                Corporate Inquiries
                            </a>
                        </div>
                    </div>

                    <!-- Safety Guarantee -->
                    <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/10">
                        <div class="flex items-center space-x-3 mb-3 text-white">
                            <i class="fas fa-shield-alt text-2xl text-gray-300"></i>
                            <h4 class="font-bold text-sm font-orbitron uppercase tracking-wider">Safety & Regulation</h4>
                        </div>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            Safety is the cornerstone of Krosskhana. Track barriers, certified marshals, medical staff, and driver scrutineering comply with international motorsport safety guidelines.
                        </p>
                    </div>

                    <!-- Share Widget -->
                    <div class="bg-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/10 text-center">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Share with Drivers & Friends</h4>
                        <div class="flex justify-center gap-3">
                            <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($title . ' at Krosskhana: ' . $currentUrl); ?>" target="_blank" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($currentUrl); ?>" target="_blank" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($title . ' - Check this out: ' . $currentUrl); ?>" target="_blank" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <button onclick="copyCurrentLink()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors" title="Copy Link">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-black border-t border-white/10 py-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <img src="assets/logos/Krosskhana-05.png" alt="Krosskhana" class="h-12 w-auto mx-auto mb-4">
            <p class="text-gray-400 text-sm mb-4">Where automotive culture meets artistic expression. Experience the revolution.</p>
            <div class="flex justify-center space-x-6 text-sm text-gray-400">
                <a href="index.php" class="hover:text-white transition-colors">Home</a>
                <a href="index.php#events" class="hover:text-white transition-colors">Events</a>
                <a href="index.php#participate" class="hover:text-white transition-colors">Participate</a>
                <a href="corporate-partnership.php" class="hover:text-white transition-colors">Corporate</a>
                <a href="contact.php" class="hover:text-white transition-colors">Contact</a>
            </div>
            <p class="text-gray-600 text-xs mt-8">© <?php echo date('Y'); ?> Krosskhana Motorsports. All rights reserved.</p>
        </div>
    </footer>

    <!-- Copy Link Notification Toast -->
    <div id="copy-toast" class="fixed bottom-6 right-6 z-50 bg-white text-black px-4 py-2.5 rounded-xl shadow-2xl font-semibold text-xs transition-opacity duration-300 opacity-0 pointer-events-none">
        <i class="fas fa-check-circle mr-1 text-green-600"></i> Event link copied to clipboard!
    </div>

    <script>
    function copyCurrentLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            const toast = document.getElementById('copy-toast');
            toast.classList.remove('opacity-0', 'pointer-events-none');
            setTimeout(() => {
                toast.classList.add('opacity-0', 'pointer-events-none');
            }, 2500);
        });
    }

    function shareEvent() {
        if (navigator.share) {
            navigator.share({
                title: '<?php echo addslashes($title); ?> - Krosskhana',
                text: '<?php echo addslashes(substr($description, 0, 100)); ?>',
                url: window.location.href,
            }).catch(console.error);
        } else {
            copyCurrentLink();
        }
    }

    // Countdown Clock script
    const countdownContainer = document.getElementById('countdown-timer');
    if (countdownContainer) {
        const targetDateStr = countdownContainer.getAttribute('data-target');
        if (targetDateStr) {
            const targetDate = new Date(targetDateStr).getTime();

            function updateCountdown() {
                const now = new Date().getTime();
                const diff = targetDate - now;

                if (diff <= 0) {
                    document.getElementById('cd-days').textContent = '00';
                    document.getElementById('cd-hours').textContent = '00';
                    document.getElementById('cd-minutes').textContent = '00';
                    document.getElementById('cd-seconds').textContent = '00';
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                document.getElementById('cd-days').textContent = String(days).padStart(2, '0');
                document.getElementById('cd-hours').textContent = String(hours).padStart(2, '0');
                document.getElementById('cd-minutes').textContent = String(minutes).padStart(2, '0');
                document.getElementById('cd-seconds').textContent = String(seconds).padStart(2, '0');
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
    }
    </script>
</body>
</html>
