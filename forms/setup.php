<?php
require_once 'includes/config.php';

// Check if setup has already been completed
$pdo = getDBConnection();
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
        $result = $stmt->fetch();
        if ($result['count'] > 0) {
            die("Setup has already been completed. Admin user exists in the database.");
        }
    } catch (PDOException $e) {
        // Database tables don't exist yet, continue with setup
    }
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create database if it doesn't exist
        $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4", DB_USER, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE " . DB_NAME);
        
        // Read and execute SQL file
        $sqlFile = 'includes/database.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS\s+`?[\w]+`?;/i', '', $sql);
            $sql = preg_replace('/USE\s+`?[\w]+`?;/i', '', $sql);
            $pdo->exec($sql);
            $message = "Database setup completed successfully!";
        } else {
            $error = "Database SQL file not found: $sqlFile";
        }
        
    } catch (PDOException $e) {
        $error = "Database setup failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krosskhana Admin Setup</title>
    
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
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        'orange': {
                            500: '#f97316',
                            600: '#ea580c',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-black to-gray-900 min-h-screen font-poppins">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo and Title -->
            <div class="text-center">
                <img class="mx-auto h-16 w-auto" src="assets/images/krosskhana.png" alt="Krosskhana">
                <h2 class="mt-6 text-3xl font-extrabold text-white">
                    Admin Setup
                </h2>
                <p class="mt-2 text-sm text-gray-400">
                    Initialize your Krosskhana admin dashboard
                </p>
            </div>

            <!-- Setup Form -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20">
                <?php if ($message): ?>
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
                    </div>
                    <div class="text-center">
                        <a href="admin/login.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Go to Admin Login
                        </a>
                    </div>
                <?php elseif ($error): ?>
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php else: ?>
                    <form class="space-y-6" method="POST">
                        <div class="text-center">
                            <i class="fas fa-database text-orange-500 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-300 mb-4">Database Setup</h3>
                            <p class="text-sm text-gray-400 mb-6">
                                This will create the necessary database tables and admin user for your Krosskhana admin dashboard.
                            </p>
                        </div>

                        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                            <h4 class="text-sm font-medium text-gray-300 mb-2">What will be created:</h4>
                            <ul class="text-xs text-gray-400 space-y-1">
                                <li>• Database: krosskhana_db</li>
                                <li>• Newsletter subscriptions table</li>
                                <li>• Racer registrations table</li>
                                <li>• Admin users table</li>
                                <li>• Admin users table</li>
                            </ul>
                        </div>

                        <div>
                            <button type="submit" 
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-300 transform hover:scale-105">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <i class="fas fa-cog text-orange-300 group-hover:text-orange-200"></i>
                                </span>
                                Initialize Database
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <!-- Database Configuration Info -->
                <div class="mt-6 p-4 bg-gray-800/50 rounded-lg border border-gray-700">
                    <h4 class="text-sm font-medium text-gray-300 mb-2">Database Configuration:</h4>
                    <div class="text-xs text-gray-400 space-y-1">
                        <p><strong>Host:</strong> <?php echo DB_HOST; ?></p>
                        <p><strong>Port:</strong> <?php echo DB_PORT; ?></p>
                        <p><strong>Database:</strong> <?php echo DB_NAME; ?></p>
                        <p><strong>User:</strong> <?php echo DB_USER; ?></p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    &copy; <?php echo date('Y'); ?> Krosskhana. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
