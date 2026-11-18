<?php
require_once '../includes/config.php';
require_once 'includes/auth.php';

// Check if already logged in
if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$successMessage = '';
$identifier = '';

if (isset($_GET['error']) && $_GET['error'] === 'auth_required') {
    $error = 'Please sign in to continue to the admin dashboard.';
}

if (isset($_GET['reset']) && $_GET['reset'] === 'success') {
    $successMessage = 'Your password has been reset successfully. Please sign in with your new password.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($identifier) || empty($password)) {
        $error = 'Please enter your email or username and password.';
    } else {
        // Use the authentication function
        $user = verifyAdminCredentials($identifier, $password);
        
        if ($user) {
            // Check if 2FA setup is required (first-time sign-in or not yet configured)
            $mustSetup2FA = ($user['must_setup_2fa'] == 1 || empty($user['two_factor_enabled']) || empty($user['two_factor_secret']));
            
            $_SESSION['admin_2fa_pending'] = true;
            $_SESSION['admin_2fa_user_id'] = $user['id'];
            
            if ($mustSetup2FA) {
                $_SESSION['admin_2fa_action'] = 'setup';
                header('Location: setup-2fa.php');
                exit;
            } else {
                $_SESSION['admin_2fa_action'] = 'verify';
                header('Location: verify-2fa.php');
                exit;
            }
        } else {
            $error = 'Invalid email/username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Krosskhana</title>
    
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
</head>
<body class="bg-gradient-to-br from-gray-900 via-black to-gray-900 min-h-screen font-poppins">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo and Title -->
            <div class="text-center">
                <img class="mx-auto h-16 w-auto hover:scale-110 transition-transform duration-300" src="../assets/logos/Krosskhana-02.png" alt="Krosskhana">
                <h2 class="mt-6 text-3xl font-extrabold text-white font-orbitron">
                    Admin Dashboard
                </h2>
                <p class="mt-2 text-sm text-gray-400">
                    Sign in to manage your Krosskhana data
                </p>
            </div>

            <!-- Login Form -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20">
                <?php if ($successMessage): ?>
                    <div class="mb-4 bg-emerald-500/20 border border-emerald-500/40 text-emerald-200 px-4 py-3 rounded-xl relative flex items-start gap-3" role="alert">
                        <i class="fas fa-check-circle text-lg mt-0.5"></i>
                        <span class="block sm:inline"><?php echo htmlspecialchars($successMessage); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="mb-4 bg-red-500/20 border border-red-500/40 text-red-200 px-4 py-3 rounded-xl relative flex items-start gap-3" role="alert">
                        <i class="fas fa-exclamation-circle text-lg mt-0.5"></i>
                        <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <form class="space-y-6" method="POST">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-300">
                            Email or Username
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="username" name="username" type="text" required 
                                class="appearance-none relative block w-full pl-10 pr-3 py-3 border border-white/20 rounded-lg placeholder-gray-400 text-white bg-white/10 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-300"
                                value="<?php echo htmlspecialchars($identifier); ?>"
                                placeholder="Enter your email or username">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-medium text-gray-300">
                                Password
                            </label>
                            <a href="forgot-password.php" class="text-xs text-gray-400 hover:text-white transition">
                                Forgot password?
                            </a>
                        </div>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" required 
                                class="appearance-none relative block w-full pl-10 pr-3 py-3 border border-white/20 rounded-lg placeholder-gray-400 text-white bg-white/10 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-300"
                                placeholder="Enter your password">
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-black bg-gradient-to-r from-white to-gray-300 hover:from-gray-200 hover:to-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-sign-in-alt text-gray-600 group-hover:text-gray-700"></i>
                            </span>
                            Sign in
                        </button>
                    </div>
                </form>

            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    &copy; <?php echo date('Y'); ?> Krosskhana. All rights reserved.
                </p>
                <p class="text-xs text-gray-600 mt-1">
                    Created by <a href="https://www.jlm.com.ng" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-gray-400 transition-colors duration-300">JLM</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-gray-500');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-gray-500');
                });
            });
            
            // Add loading animation to form submission
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                const button = this.querySelector('button[type="submit"]');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Signing in...';
                button.disabled = true;
                
                // Reset after 3 seconds if no redirect
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 3000);
            });
        });
    </script>
</body>
</html>
