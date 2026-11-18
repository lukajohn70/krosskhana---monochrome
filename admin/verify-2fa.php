<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/totp.php';

// Check if admin is pending 2FA verification
if (empty($_SESSION['admin_2fa_pending']) || empty($_SESSION['admin_2fa_user_id'])) {
    if (isAdminLoggedIn()) {
        header('Location: index.php');
        exit;
    }
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['admin_2fa_user_id'];
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ? AND is_active = 1 LIMIT 1");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    unset($_SESSION['admin_2fa_pending']);
    unset($_SESSION['admin_2fa_user_id']);
    header('Location: login.php?error=auth_required');
    exit;
}

// If user has not yet set up 2FA, redirect to setup
if (empty($user['two_factor_enabled']) || empty($user['two_factor_secret'])) {
    $_SESSION['admin_2fa_action'] = 'setup';
    header('Location: setup-2fa.php');
    exit;
}

$error = '';
$info = '';
$useRecoveryMode = isset($_GET['recovery']) && $_GET['recovery'] === '1';

// Handle email code request
if (isset($_GET['send_email_code']) && $_GET['send_email_code'] === '1') {
    $emailOtp = generateEmailOtp();
    $updateStmt = $pdo->prepare("UPDATE admin_users SET temp_2fa_code = ?, temp_2fa_expires_at = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE id = ?");
    $updateStmt->execute([$emailOtp, $user['id']]);

    sendTwoFactorEmailCode($user['email'], $emailOtp, $user['username']);
    $info = "A 6-digit verification code has been sent to " . htmlspecialchars($user['email']) . ". (Valid for 10 minutes)";
    if (DEVELOPMENT_MODE) {
        $info .= " [Dev code: $emailOtp]";
    }
}

// Handle Verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isRecoverySubmission = !empty($_POST['is_recovery']);

    if ($isRecoverySubmission) {
        // Recovery Code Verification
        $recoveryInput = strtoupper(trim($_POST['recovery_code'] ?? ''));
        $cleanRecovery = str_replace(['-', ' '], '', $recoveryInput);

        if (empty($cleanRecovery)) {
            $error = 'Please enter a recovery code.';
        } else {
            $recoveryHash = hash('sha256', $cleanRecovery);
            $storedCodes = json_decode($user['recovery_codes'] ?? '[]', true) ?: [];

            $foundIndex = array_search($recoveryHash, $storedCodes);
            if ($foundIndex !== false) {
                // Consume the recovery code (single use)
                unset($storedCodes[$foundIndex]);
                $storedCodes = array_values($storedCodes);

                $updateRecovery = $pdo->prepare("UPDATE admin_users SET recovery_codes = ?, last_login = NOW() WHERE id = ?");
                $updateRecovery->execute([json_encode($storedCodes), $user['id']]);

                // Create full admin session
                createAdminSession($user);
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid recovery code. Please check and try again.';
            }
        }
    } else {
        // 6-digit Code Verification
        $code = trim($_POST['code'] ?? '');
        $code = str_replace([' ', '-'], '', $code);

        if (empty($code)) {
            $error = 'Please enter your 6-digit authentication code.';
        } else {
            $verified = false;

            // 1. Check TOTP Authenticator code
            if (!empty($user['two_factor_secret']) && TOTP::verifyCode($user['two_factor_secret'], $code, 1)) {
                $verified = true;
            }

            // 2. Check temporary email code
            if (!$verified && !empty($user['temp_2fa_code']) && !empty($user['temp_2fa_expires_at'])) {
                $emailStmt = $pdo->prepare("SELECT id FROM admin_users WHERE id = ? AND temp_2fa_code = ? AND temp_2fa_expires_at > NOW() LIMIT 1");
                $emailStmt->execute([$user['id'], $code]);
                if ($emailStmt->fetch()) {
                    $verified = true;
                    // Invalidate email code after use
                    $pdo->prepare("UPDATE admin_users SET temp_2fa_code = NULL, temp_2fa_expires_at = NULL WHERE id = ?")->execute([$user['id']]);
                }
            }

            if ($verified) {
                updateAdminLastLogin($user['id']);
                createAdminSession($user);
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid verification code. Please check your app or request an email code.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Verification - Krosskhana Admin</title>
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
                Security Check
            </h2>
            <p class="mt-2 text-sm text-gray-400">
                Two-Factor Authentication for <strong class="text-white"><?php echo htmlspecialchars($user['username']); ?></strong>
            </p>
        </div>

        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 shadow-2xl border border-white/20">
            <?php if ($error): ?>
                <div class="mb-5 p-4 rounded-xl text-sm bg-red-500/20 border border-red-500/40 text-red-200 flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-lg mt-0.5"></i>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <?php if ($info): ?>
                <div class="mb-5 p-4 rounded-xl text-sm bg-blue-500/20 border border-blue-500/40 text-blue-200 flex items-start gap-3">
                    <i class="fas fa-info-circle text-lg mt-0.5"></i>
                    <div><?php echo htmlspecialchars($info); ?></div>
                </div>
            <?php endif; ?>

            <?php if ($useRecoveryMode): ?>
                <!-- Recovery Code Form -->
                <form class="space-y-6" method="POST">
                    <input type="hidden" name="is_recovery" value="1">
                    <div>
                        <label for="recovery_code" class="block text-sm font-medium text-gray-300">
                            Enter Backup Recovery Code
                        </label>
                        <p class="text-xs text-gray-400 mt-1 mb-2">
                            Enter one of your 8-character recovery codes (e.g. 1A2B-3C4D):
                        </p>
                        <input id="recovery_code" name="recovery_code" type="text" required autofocus
                               class="text-center font-mono tracking-widest text-xl appearance-none block w-full py-3 px-4 border border-white/20 rounded-xl placeholder-gray-500 text-white bg-white/10 focus:outline-none focus:ring-2 focus:ring-gray-500 uppercase"
                               placeholder="XXXX-XXXX">
                    </div>

                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-black bg-gradient-to-r from-white to-gray-300 hover:from-gray-200 hover:to-gray-400 focus:outline-none transition-all duration-300 transform hover:scale-[1.02] shadow-lg">
                        <i class="fas fa-key mr-2 mt-0.5"></i> Verify Recovery Code
                    </button>

                    <div class="text-center pt-2">
                        <a href="verify-2fa.php" class="text-xs text-gray-400 hover:text-white transition">
                            <i class="fas fa-shield-alt mr-1"></i> Use 6-Digit Authenticator Code instead
                        </a>
                    </div>
                </form>

            <?php else: ?>
                <!-- Standard 6-Digit Code Form -->
                <form class="space-y-6" method="POST">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-300 text-center">
                            Enter 6-Digit Authentication Code
                        </label>
                        <p class="text-xs text-gray-400 text-center mt-1 mb-4">
                            From your Authenticator App (Google Authenticator, Microsoft Authenticator, etc.)
                        </p>
                        <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="8" required autofocus autocomplete="one-time-code"
                               class="text-center font-mono tracking-widest text-3xl appearance-none block w-full py-3 px-4 border border-white/20 rounded-xl placeholder-gray-500 text-white bg-white/10 focus:outline-none focus:ring-2 focus:ring-gray-500"
                               placeholder="000000">
                    </div>

                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-black bg-gradient-to-r from-white to-gray-300 hover:from-gray-200 hover:to-gray-400 focus:outline-none transition-all duration-300 transform hover:scale-[1.02] shadow-lg">
                        <i class="fas fa-check-circle mr-2 mt-0.5"></i> Verify & Sign In
                    </button>

                    <div class="flex items-center justify-between pt-2 text-xs text-gray-400">
                        <a href="verify-2fa.php?send_email_code=1" class="hover:text-white transition underline">
                            <i class="fas fa-envelope mr-1"></i> Email me a code
                        </a>
                        <a href="verify-2fa.php?recovery=1" class="hover:text-white transition underline">
                            <i class="fas fa-key mr-1"></i> Use recovery code
                        </a>
                    </div>
                </form>
            <?php endif; ?>

            <div class="text-center pt-4 border-t border-white/10 mt-6">
                <a href="logout.php" class="text-xs text-gray-400 hover:text-white transition">
                    <i class="fas fa-sign-out-alt mr-1"></i> Sign out and cancel
                </a>
            </div>
        </div>

        <div class="text-center">
            <p class="text-xs text-gray-500">
                &copy; <?php echo date('Y'); ?> Krosskhana Motorsports. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
