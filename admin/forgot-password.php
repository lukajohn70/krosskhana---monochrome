<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/email-utils.php';

// Check if already logged in
if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$message = '';
$messageType = '';
$devResetLink = '';
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');

    if (empty($identifier)) {
        $message = 'Please enter your email or username.';
        $messageType = 'error';
    } else {
        $pdo = getDBConnection();
        if ($pdo) {
            try {
                // Ensure password_resets table exists (self-healing)
                $pdo->exec("
                CREATE TABLE IF NOT EXISTS password_resets (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    admin_id INT NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    token_hash VARCHAR(255) NOT NULL,
                    expires_at DATETIME NOT NULL,
                    used_at DATETIME NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_token_hash (token_hash),
                    INDEX idx_admin_id (admin_id),
                    INDEX idx_email (email),
                    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ");

                $stmt = $pdo->prepare("SELECT id, username, email FROM admin_users WHERE (username = ? OR email = ?) AND is_active = 1 LIMIT 1");
                $stmt->execute([$identifier, $identifier]);
                $user = $stmt->fetch();

                if ($user) {
                    // Invalidate previous unused reset tokens for this user
                    $pdo->prepare("UPDATE password_resets SET used_at = NOW() WHERE admin_id = ? AND used_at IS NULL")
                        ->execute([$user['id']]);

                    // Generate raw token and hash
                    $rawToken = bin2hex(random_bytes(32));
                    $tokenHash = hash('sha256', $rawToken);

                    // Insert reset token (valid for 1 hour)
                    $insertStmt = $pdo->prepare("INSERT INTO password_resets (admin_id, email, token_hash, expires_at) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
                    $insertStmt->execute([$user['id'], $user['email'], $tokenHash]);

                    // Build reset link
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? "https://" : "http://";
                    $host = $_SERVER['HTTP_HOST'] ?? 'krosskhana.org';
                    
                    if (defined('SITE_URL') && !empty(SITE_URL)) {
                        $resetUrl = rtrim(SITE_URL, '/') . '/admin/reset-password.php?token=' . urlencode($rawToken);
                    } else {
                        $resetUrl = $protocol . $host . '/admin/reset-password.php?token=' . urlencode($rawToken);
                    }

                    // Email content
                    $subject = "Reset Your Krosskhana Admin Password";
                    $emailBody = '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="utf-8">
                        <style>
                            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #0b0f17; color: #ffffff; padding: 20px; }
                            .container { max-width: 560px; margin: 0 auto; background: #161e2e; border: 1px solid #2d3748; border-radius: 16px; padding: 32px; }
                            .header { text-align: center; margin-bottom: 24px; }
                            .btn { display: inline-block; background: linear-gradient(135deg, #ffffff, #d1d5db); color: #000000 !important; font-weight: bold; text-decoration: none; padding: 14px 28px; border-radius: 10px; margin: 20px 0; }
                            .footer { margin-top: 32px; font-size: 12px; color: #9ca3af; text-align: center; }
                            .note { font-size: 12px; color: #94a3b8; word-break: break-all; }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="header">
                                <h2 style="color:#ffffff; margin:0 0 8px 0; font-size:24px;">Password Reset Request</h2>
                                <p style="color:#9ca3af; font-size:14px; margin:0;">Krosskhana Motorsports Admin Portal</p>
                            </div>
                            <p style="color:#e5e7eb; font-size:15px;">Hello <strong>' . htmlspecialchars($user['username']) . '</strong>,</p>
                            <p style="color:#cbd5e1; font-size:14px; line-height:1.5;">We received a request to reset your password. Click the button below to set a new password. This link is valid for 1 hour.</p>
                            <div style="text-align: center;">
                                <a href="' . htmlspecialchars($resetUrl) . '" class="btn">Reset My Password</a>
                            </div>
                            <p class="note">If the button does not work, copy and paste this link into your browser:<br>' . htmlspecialchars($resetUrl) . '</p>
                            <p style="color:#94a3b8; font-size:13px; margin-top:20px;">If you did not request a password reset, you can safely ignore this email. Your account remains secure.</p>
                            <div class="footer">
                                &copy; ' . date('Y') . ' Krosskhana Motorsports. All rights reserved.
                            </div>
                        </div>
                    </body>
                    </html>';

                    $sent = sendEmail($user['email'], $subject, $emailBody, true);

                    if ($sent) {
                        $message = 'Password reset instructions have been sent to ' . htmlspecialchars($user['email']) . '. Please check your inbox and spam folder.';
                        $messageType = 'success';
                    } else {
                        $message = 'We could not send the reset email at this moment. Please check the server mail logs or contact the administrator.';
                        $messageType = 'error';
                    }

                    if (DEVELOPMENT_MODE) {
                        $devResetLink = $resetUrl;
                    }
                } else {
                    // Ambiguous message for security if user not found
                    $message = 'If an account matches that email or username, password reset instructions have been sent.';
                    $messageType = 'success';
                }
            } catch (Exception $e) {
                error_log("Password reset error: " . $e->getMessage());
                $message = 'An error occurred while processing your request. Please try again.';
                $messageType = 'error';
            }
        } else {
            $message = 'Database connection error. Please try again later.';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Krosskhana Admin</title>
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
                Reset Password
            </h2>
            <p class="mt-2 text-sm text-gray-400">
                Enter your registered username or email to receive reset instructions
            </p>
        </div>

        <!-- Card Container -->
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20">
            <?php if ($message): ?>
                <div class="mb-5 p-4 rounded-xl text-sm border <?php echo $messageType === 'error' ? 'bg-red-500/20 border-red-500/40 text-red-200' : 'bg-emerald-500/20 border-emerald-500/40 text-emerald-200'; ?>">
                    <div class="flex items-start gap-3">
                        <i class="fas <?php echo $messageType === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'; ?> text-lg mt-0.5"></i>
                        <div><?php echo htmlspecialchars($message); ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($devResetLink): ?>
                <!-- Development Direct Helper -->
                <div class="mb-5 p-3 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-200 text-xs">
                    <div class="font-bold flex items-center gap-1.5 mb-1"><i class="fas fa-tools"></i> Development Link:</div>
                    <a href="<?php echo htmlspecialchars($devResetLink); ?>" class="underline break-all text-white hover:text-amber-300">
                        <?php echo htmlspecialchars($devResetLink); ?>
                    </a>
                </div>
            <?php endif; ?>

            <form class="space-y-6" method="POST">
                <div>
                    <label for="identifier" class="block text-sm font-medium text-gray-300">
                        Email Address or Username
                    </label>
                    <div class="mt-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="identifier" name="identifier" type="text" required 
                            class="appearance-none relative block w-full pl-10 pr-3 py-3 border border-white/20 rounded-lg placeholder-gray-400 text-white bg-white/10 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-300"
                            value="<?php echo htmlspecialchars($identifier); ?>"
                            placeholder="Enter your email or username">
                    </div>
                </div>

                <div>
                    <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-black bg-gradient-to-r from-white to-gray-300 hover:from-gray-200 hover:to-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-paper-plane text-gray-600 group-hover:text-gray-700"></i>
                        </span>
                        Send Reset Link
                    </button>
                </div>

                <div class="text-center pt-2">
                    <a href="login.php" class="text-sm text-gray-400 hover:text-white transition flex items-center justify-center gap-1.5">
                        <i class="fas fa-arrow-left text-xs"></i> Back to Sign In
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-xs text-gray-500">
                &copy; <?php echo date('Y'); ?> Krosskhana Motorsports. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
