<?php
require_once '../includes/config.php';
require_once 'includes/auth.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();
$adminUser = null;

if ($pdo) {
    // Get current admin user info
    $stmt = $pdo->prepare("SELECT username, email, role, last_login FROM admin_users WHERE username = ?");
    $stmt->execute([$_SESSION['admin_username']]);
    $adminUser = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings - Krosskhana Admin</title>
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
    <?php $pageTitle = 'Admin Settings'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 font-orbitron">Admin Settings</h1>
                <p class="text-gray-600 mt-2">Manage your account settings and preferences</p>
            </div>

            <!-- Settings Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Account Information -->
                <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50">
                    <div class="px-6 py-4 border-b border-gray-200/50">
                        <h3 class="text-lg font-medium text-gray-900 font-orbitron">Account Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <span class="text-gray-900 font-medium"><?php echo htmlspecialchars($adminUser['username'] ?? 'N/A'); ?></span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center mr-3">
                                        <i class="fas fa-envelope text-white"></i>
                                    </div>
                                    <span class="text-gray-900"><?php echo htmlspecialchars($adminUser['email'] ?? 'N/A'); ?></span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center mr-3">
                                        <i class="fas fa-shield-alt text-white"></i>
                                    </div>
                                    <span class="text-gray-900 font-medium capitalize"><?php echo htmlspecialchars($adminUser['role'] ?? 'N/A'); ?></span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Login</label>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center mr-3">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                    <span class="text-gray-900"><?php echo $adminUser['last_login'] ? date('M j, Y g:i A', strtotime($adminUser['last_login'])) : 'Never'; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50">
                    <div class="px-6 py-4 border-b border-gray-200/50">
                        <h3 class="text-lg font-medium text-gray-900 font-orbitron">Change Password</h3>
                    </div>
                    <div class="p-6">
                        <form id="password-form" class="space-y-4">
                            <div>
                                <label for="current-password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input type="password" id="current-password" name="current_password" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                            </div>
                            
                            <div>
                                <label for="new-password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" id="new-password" name="new_password" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                            </div>
                            
                            <div>
                                <label for="confirm-password" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                <input type="password" id="confirm-password" name="confirm_password" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                            </div>
                            
                            <button type="submit" class="w-full bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-key mr-2"></i>Update Password
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Email Settings -->
                <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 lg:col-span-2">
                    <div class="px-6 py-4 border-b border-gray-200/50">
                        <h3 class="text-lg font-medium text-gray-900 font-orbitron">Email Configuration</h3>
                    </div>
                    <div class="p-6">
                        <div id="email-settings-loading" class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-gray-600 text-2xl"></i>
                            <p class="text-gray-600 mt-2">Loading email settings...</p>
                        </div>
                        
                        <form id="email-settings-form" class="hidden space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="smtp-host" class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                                    <input type="text" id="smtp-host" name="smtp_host" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                                </div>
                                
                                <div>
                                    <label for="smtp-port" class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                                    <input type="number" id="smtp-port" name="smtp_port" value="587" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                                </div>
                                
                                <div>
                                    <label for="smtp-username" class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                                    <input type="text" id="smtp-username" name="smtp_username" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                                </div>
                                
                                <div>
                                    <label for="smtp-password" class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                                    <input type="password" id="smtp-password" name="smtp_password"
                                        placeholder="Leave blank to keep current password"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                                </div>
                                
                                <div>
                                    <label for="smtp-encryption" class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                                    <select id="smtp-encryption" name="smtp_encryption" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900">
                                        <option value="tls">TLS</option>
                                        <option value="ssl">SSL</option>
                                        <option value="none">None</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="from-email" class="block text-sm font-medium text-gray-700 mb-2">From Email</label>
                                    <input type="email" id="from-email" name="from_email" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                                </div>
                                
                                <div>
                                    <label for="from-name" class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                                    <input type="text" id="from-name" name="from_name" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                                </div>
                                
                                <div>
                                    <label for="reply-to-email" class="block text-sm font-medium text-gray-700 mb-2">Reply To Email</label>
                                    <input type="email" id="reply-to-email" name="reply_to_email"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                                </div>
                                
                                <div>
                                    <label for="daily-send-limit" class="block text-sm font-medium text-gray-700 mb-2">Daily Send Limit</label>
                                    <input type="number" id="daily-send-limit" name="daily_send_limit" value="1000"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                                </div>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="testEmailSettings()" class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                                    <i class="fas fa-paper-plane mr-2"></i>Test Connection
                                </button>
                                <button type="submit" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                                    <i class="fas fa-save mr-2"></i>Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Success/Error Messages -->
                    <div id="message-container" class="fixed top-4 right-4 z-50 hidden">
        <div id="message" class="px-6 py-4 rounded-lg shadow-lg max-w-md">
            <div class="flex items-center">
                <i id="message-icon" class="mr-3"></i>
                <span id="message-text"></span>
            </div>
        </div>
    </div>

    <script src="includes/sidebar.js"></script>
    <script>
        // Load email settings on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadEmailSettings();
        });

        // Password change form
        document.getElementById('password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('current-password').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            if (newPassword !== confirmPassword) {
                showMessage('Passwords do not match', 'error');
                return;
            }
            
            if (newPassword.length < 6) {
                showMessage('New password must be at least 6 characters long', 'error');
                return;
            }
            
            // Submit password change
            const formData = new FormData();
            formData.append('action', 'change_password');
            formData.append('current_password', currentPassword);
            formData.append('new_password', newPassword);
            
            fetch('api/settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showMessage('Password updated successfully', 'success');
                    document.getElementById('password-form').reset();
                } else {
                    showMessage(data.message || 'Failed to update password', 'error');
                }
            })
            .catch(error => {
                showMessage('An error occurred while updating password', 'error');
            });
        });

        // Email settings form
        document.getElementById('email-settings-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'update');
            
            fetch('api/settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showMessage('Email settings updated successfully', 'success');
                } else {
                    showMessage(data.message || 'Failed to update email settings', 'error');
                }
            })
            .catch(error => {
                showMessage('An error occurred while updating email settings', 'error');
            });
        });

        function loadEmailSettings() {
            fetch('api/settings.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('email-settings-loading').classList.add('hidden');
                
                if (data.status === 'success') {
                    document.getElementById('email-settings-form').classList.remove('hidden');
                    
                    // Populate form fields
                    data.data.forEach(setting => {
                        const field = document.getElementById(setting.setting_key.replace('_', '-'));
                        if (field) {
                            field.value = setting.setting_value;
                        }
                    });
                } else {
                    document.getElementById('email-settings-loading').innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-triangle text-gray-600 text-2xl"></i>
                            <p class="text-gray-600 mt-2">${data.message}</p>
                            <a href="../setup-campaign-system.php" class="text-gray-500 hover:text-gray-700 underline mt-2 inline-block">
                                Setup Email System
                            </a>
                        </div>
                    `;
                }
            })
            .catch(error => {
                document.getElementById('email-settings-loading').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-gray-600 text-2xl"></i>
                        <p class="text-gray-600 mt-2">Failed to load email settings</p>
                    </div>
                `;
            });
        }

        function testEmailSettings() {
            const formData = new FormData(document.getElementById('email-settings-form'));
            formData.append('action', 'test_email');
            
            showMessage('Testing email connection...', 'info');
            
            fetch('api/settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showMessage('Email connection test successful', 'success');
                } else {
                    showMessage(data.message || 'Email connection test failed', 'error');
                }
            })
            .catch(error => {
                showMessage('An error occurred during email test', 'error');
            });
        }

        function showMessage(message, type) {
            const container = document.getElementById('message-container');
            const messageDiv = document.getElementById('message');
            const icon = document.getElementById('message-icon');
            const text = document.getElementById('message-text');
            
            // Set message content
            text.textContent = message;
            
            // Set styling based on type
            messageDiv.className = 'px-6 py-4 rounded-lg shadow-lg max-w-md';
            icon.className = 'mr-3';
            
            switch(type) {
                case 'success':
                    messageDiv.classList.add('bg-gray-100', 'border', 'border-gray-400', 'text-gray-700');
                    icon.classList.add('fas', 'fa-check-circle', 'text-gray-500');
                    break;
                case 'error':
                    messageDiv.classList.add('bg-gray-100', 'border', 'border-gray-400', 'text-gray-700');
                    icon.classList.add('fas', 'fa-exclamation-circle', 'text-gray-500');
                    break;
                case 'info':
                    messageDiv.classList.add('bg-gray-100', 'border', 'border-gray-400', 'text-gray-700');
                    icon.classList.add('fas', 'fa-info-circle', 'text-gray-500');
                    break;
                default:
                    messageDiv.classList.add('bg-gray-100', 'border', 'border-gray-400', 'text-gray-700');
                    icon.classList.add('fas', 'fa-info-circle', 'text-gray-500');
            }
            
            // Show message
            container.classList.remove('hidden');
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                container.classList.add('hidden');
            }, 5000);
        }
    </script>
</body>
</html>
