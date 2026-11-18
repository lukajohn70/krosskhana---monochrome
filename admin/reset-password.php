<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Check if already logged in
if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$rawToken = trim($_GET['token'] ?? ($_POST['token'] ?? ''));
$isValidToken = false;
$resetRecord = null;
$error = '';
$success = '';

$pdo = getDBConnection();

if ($rawToken && $pdo) {
    try {
        $tokenHash = hash('sha256', $rawToken);
        $stmt = $pdo->prepare("SELECT pr.*, u.username 
                               FROM password_resets pr
                               JOIN admin_users u ON pr.admin_id = u.id
                               WHERE pr.token_hash = ? 
                                 AND pr.used_at IS NULL 
                                 AND pr.expires_at > NOW() 
                                 AND u.is_active = 1
                               LIMIT 1");
        $stmt->execute([$tokenHash]);
        $resetRecord = $stmt->fetch();
        if ($resetRecord) {
            $isValidToken = true;
        } else {
            $error = 'This password reset link is invalid or has expired. Please request a new one.';
        }
    } catch (Exception $e) {
        error_log("Token verification error: " . $e->getMessage());
        $error = 'A database error occurred. Please try again.';
    }
} else {
    $error = 'No reset token provided. Please use the link sent to your email.';
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isValidToken) {
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            // Hash new password
            $newHash = password_hash($password, PASSWORD_DEFAULT);

            // Update user password
            $updateUser = $pdo->prepare("UPDATE admin_users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
            $updateUser->execute([$newHash, $resetRecord['admin_id']]);

            // Mark token as used
            $markUsed = $pdo->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ?");
            $markUsed->execute([$resetRecord['id']]);

            // Redirect to login with success
            header('Location: login.php?reset=success');
            exit;
        } catch (Exception $e) {
            error_log("Password update error: " . $e->getMessage());
            $error = 'Failed to update password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - Krosskhana Admin</title>
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
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-black to-gray-900 min-h-screen font-poppins flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo and Title -->
        <div class="text-center">
            <img class="mx-auto h-16 w-auto hover:scale-110 transition-transform duration-300" src="../assets/logos/Krosskhana-02.png" alt="Krosskhana">
            <h2 class="mt-6 text-3xl font-extrabold text-white font-orbitron">
                Set New Password
            </h2>
            <?php if ($isValidToken): ?>
                <p class="mt-2 text-sm text-gray-400">
                    Create a secure password for <strong class="text-white"><?php echo htmlspecialchars($resetRecord['username']); ?></strong>
                </p>
            <?php endif; ?>
        </div>

        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20">
            <?php if ($error): ?>
                <div class="mb-5 p-4 rounded-xl text-sm bg-red-500/20 border border-red-500/40 text-red-200">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-circle text-lg mt-0.5"></i>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($isValidToken): ?>
                <form class="space-y-5" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($rawToken); ?>">

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300">
                            New Password
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" required minlength="8"
                                class="appearance-none relative block w-full pl-10 pr-10 py-3 border border-white/20 rounded-lg placeholder-gray-400 text-white bg-white/10 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-300"
                                placeholder="Minimum 8 characters">
                            <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-300">
                            Confirm New Password
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-check-double text-gray-400"></i>
                            </div>
                            <input id="password_confirm" name="password_confirm" type="password" required minlength="8"
                                class="appearance-none relative block w-full pl-10 pr-10 py-3 border border-white/20 rounded-lg placeholder-gray-400 text-white bg-white/10 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-300"
                                placeholder="Re-type new password">
                            <button type="button" onclick="togglePasswordVisibility('password_confirm', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-black bg-gradient-to-r from-white to-gray-300 hover:from-gray-200 hover:to-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-key text-gray-600 group-hover:text-gray-700"></i>
                            </span>
                            Save New Password
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center py-4 space-y-4">
                    <p class="text-sm text-gray-400">Need a fresh reset link?</p>
                    <a href="forgot-password.php" 
                       class="inline-block px-5 py-2.5 rounded-xl bg-white text-black font-semibold text-sm hover:bg-gray-200 transition shadow">
                        Request New Reset Link
                    </a>
                </div>
            <?php endif; ?>

            <div class="text-center pt-4 border-t border-white/10 mt-6">
                <a href="login.php" class="text-sm text-gray-400 hover:text-white transition flex items-center justify-center gap-1.5">
                    <i class="fas fa-arrow-left text-xs"></i> Back to Sign In
                </a>
            </div>
        </div>

        <div class="text-center">
            <p class="text-xs text-gray-500">
                &copy; <?php echo date('Y'); ?> Krosskhana Motorsports. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }
    </script>
</body>
</html>
