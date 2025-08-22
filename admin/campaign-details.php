<?php
require_once '../includes/config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$campaignId = $_GET['id'] ?? 0;

if (!$campaignId) {
    header('Location: campaigns.php');
    exit;
}

$pdo = getDBConnection();
$campaign = null;
$stats = [];

if ($pdo) {
    // Get campaign details
    $stmt = $pdo->prepare("SELECT * FROM campaigns WHERE id = ?");
    $stmt->execute([$campaignId]);
    $campaign = $stmt->fetch();
    
    if ($campaign) {
        // Get campaign statistics
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_recipients,
                COUNT(CASE WHEN status = 'sent' THEN 1 END) as sent_count,
                COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_count,
                COUNT(CASE WHEN status = 'bounced' THEN 1 END) as bounced_count,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_count,
                COUNT(CASE WHEN opened_at IS NOT NULL THEN 1 END) as opened_count,
                COUNT(CASE WHEN clicked_at IS NOT NULL THEN 1 END) as clicked_count
            FROM campaign_recipients 
            WHERE campaign_id = ?
        ");
        $stmt->execute([$campaignId]);
        $stats = $stmt->fetch();
    }
}

if (!$campaign) {
    header('Location: campaigns.php');
    exit;
}

function getStatusClass($status) {
    switch ($status) {
        case 'draft': return 'bg-gray-100 text-gray-800';
        case 'scheduled': return 'bg-gray-200 text-gray-500';
        case 'sending': return 'bg-gray-200 text-gray-500';
        case 'sent': return 'bg-gray-200 text-gray-500';
        case 'cancelled': return 'bg-gray-200 text-gray-500';
        default: return 'bg-gray-100 text-gray-800';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Details - Krosskhana Admin</title>
    <link rel="icon" type="image/png" href="../assets/logos/Krosskhana-05.png">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
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
</head>
<body class="bg-gradient-to-br from-gray-900 via-black to-gray-900 min-h-screen font-poppins">
    <?php $pageTitle = 'Campaign Details'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Toast Container -->
    <div id="toast-container" class="toast-container"></div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
                            <dt class="text-sm font-medium text-gray-600 truncate">Campaign Status</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo ucfirst($campaign['status'] ?? 'Unknown'); ?></dd>
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
                            <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 truncate">Recipients</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo $campaign['sent_count'] ?? 0; ?>/<?php echo $campaign['total_recipients'] ?? 0; ?></dd>
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
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 truncate">Open Rate</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                <?php 
                                $openRate = $campaign['total_recipients'] > 0 ? 
                                    round(($campaign['opened_count'] / $campaign['total_recipients']) * 100, 1) : 0;
                                echo $openRate . '%';
                                ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
            <?php if ($campaign): ?>
                <!-- Filters and Actions -->
                <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 mb-6">
                    <div class="px-6 py-4 border-b border-gray-200/50">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                            <h3 class="text-lg font-medium text-gray-900 font-orbitron">Campaign Details</h3>
                            <div class="flex space-x-3">
                                <a href="campaigns.php" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                                    <i class="fas fa-arrow-left mr-2"></i>Back to Campaigns
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campaign Details Card -->
                <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 overflow-hidden">
                    
                    <div class="p-4 md:p-6">
                        <!-- Desktop Layout -->
                        <div class="hidden md:grid md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Campaign Information</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Campaign Name</label>
                                        <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($campaign['name']); ?></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email Subject</label>
                                        <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($campaign['subject']); ?></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <span class="mt-1 inline-flex px-2 py-1 text-xs font-medium rounded-full <?php echo getStatusClass($campaign['status']); ?>">
                                            <?php echo ucfirst($campaign['status']); ?>
                                        </span>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Created</label>
                                        <p class="mt-1 text-sm text-gray-900"><?php echo date('F j, Y g:i A', strtotime($campaign['created_at'])); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Performance Metrics</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Recipients</label>
                                        <p class="mt-1 text-sm text-gray-900"><?php echo $campaign['sent_count']; ?> / <?php echo $campaign['total_recipients']; ?></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Open Rate</label>
                                        <p class="mt-1 text-sm text-gray-900">
                                            <?php 
                                            $openRate = $campaign['total_recipients'] > 0 ? 
                                                round(($campaign['opened_count'] / $campaign['total_recipients']) * 100, 1) : 0;
                                            echo $openRate . '%';
                                            ?>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Click Rate</label>
                                        <p class="mt-1 text-sm text-gray-900">
                                            <?php 
                                            $clickRate = $campaign['total_recipients'] > 0 ? 
                                                round(($campaign['clicked_count'] / $campaign['total_recipients']) * 100, 1) : 0;
                                            echo $clickRate . '%';
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Layout -->
                        <div class="md:hidden space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Campaign Information</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Campaign Name</label>
                                        <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($campaign['name']); ?></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email Subject</label>
                                        <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($campaign['subject']); ?></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <span class="mt-1 inline-flex px-2 py-1 text-xs font-medium rounded-full <?php echo getStatusClass($campaign['status']); ?>">
                                            <?php echo ucfirst($campaign['status']); ?>
                                        </span>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Created</label>
                                        <p class="mt-1 text-sm text-gray-900"><?php echo date('F j, Y g:i A', strtotime($campaign['created_at'])); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Performance Metrics</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Recipients</label>
                                        <p class="mt-1 text-sm text-gray-900"><?php echo $campaign['sent_count']; ?> / <?php echo $campaign['total_recipients']; ?></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Open Rate</label>
                                        <p class="mt-1 text-sm text-gray-900">
                                            <?php 
                                            $openRate = $campaign['total_recipients'] > 0 ? 
                                                round(($campaign['opened_count'] / $campaign['total_recipients']) * 100, 1) : 0;
                                            echo $openRate . '%';
                                            ?>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Click Rate</label>
                                        <p class="mt-1 text-sm text-gray-900">
                                            <?php 
                                            $clickRate = $campaign['total_recipients'] > 0 ? 
                                                round(($campaign['clicked_count'] / $campaign['total_recipients']) * 100, 1) : 0;
                                            echo $clickRate . '%';
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($campaign['content']): ?>
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Email Content</h3>
                            <div class="bg-gray-50 rounded-lg p-4 border">
                                <div class="prose max-w-none text-sm">
                                    <?php echo $campaign['content']; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Campaign Not Found -->
                <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 p-6">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-yellow-400 text-4xl mb-4"></i>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Campaign Not Found</h2>
                        <p class="text-gray-600 mb-4">The campaign you're looking for doesn't exist or has been deleted.</p>
                        <a href="campaigns.php" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Campaigns
                        </a>
                    </div>
                </div>
            <?php endif; ?>

    <!-- Toast Notifications CSS -->
    <link rel="stylesheet" href="assets/notifications.css">
    
    <!-- Toast Notifications JS -->
    <script src="assets/toast-notifications.js"></script>
    
    <!-- Sidebar JavaScript -->
    <script src="includes/sidebar.js"></script>
</body>
</html>
