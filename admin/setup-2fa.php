<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/totp.php';

// Verify there is an admin awaiting 2FA setup
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

// Generate or retrieve current setup secret from session
if (empty($_SESSION['setup_2fa_secret'])) {
    $_SESSION['setup_2fa_secret'] = TOTP::generateSecret(16);
}
$secret = $_SESSION['setup_2fa_secret'];

$error = '';
$info = '';
$setupSuccess = false;
$generatedRecoveryCodes = [];

// Handle Email OTP Request
if (isset($_GET['send_email_code']) && $_GET['send_email_code'] === '1') {
    $emailOtp = generateEmailOtp();
    $updateStmt = $pdo->prepare("UPDATE admin_users SET temp_2fa_code = ?, temp_2fa_expires_at = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE id = ?");
    $updateStmt->execute([$emailOtp, $user['id']]);

    sendTwoFactorEmailCode($user['email'], $emailOtp, $user['username']);
    $info = "A 6-digit confirmation code has been emailed to " . htmlspecialchars($user['email']) . ". (Valid for 10 minutes)";
    if (DEVELOPMENT_MODE) {
        $info .= " [Dev code: $emailOtp]";
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $code = str_replace([' ', '-'], '', $code);

    if (empty($code)) {
        $error = 'Please enter the 6-digit verification code.';
    } else {
        $verified = false;

        // 1. Verify TOTP code from Authenticator
        if (TOTP::verifyCode($secret, $code, 1)) {
            $verified = true;
        }

        // 2. Or verify Email OTP if active
        if (!$verified && !empty($user['temp_2fa_code']) && !empty($user['temp_2fa_expires_at'])) {
            $checkStmt = $pdo->prepare("SELECT id FROM admin_users WHERE id = ? AND temp_2fa_code = ? AND temp_2fa_expires_at > NOW() LIMIT 1");
            $checkStmt->execute([$user['id'], $code]);
            if ($checkStmt->fetch()) {
                $verified = true;
            }
        }

        if ($verified) {
            // Generate recovery codes
            $rawRecoveryCodes = generateRecoveryCodes(8);
            $hashedCodes = array_map(function($c) {
                return hash('sha256', strtoupper(str_replace('-', '', $c)));
            }, $rawRecoveryCodes);

            // Save 2FA to database
            $saveStmt = $pdo->prepare("UPDATE admin_users SET 
                two_factor_secret = ?, 
                two_factor_enabled = 1, 
                must_setup_2fa = 0, 
                two_factor_confirmed_at = NOW(), 
                recovery_codes = ?,
                temp_2fa_code = NULL,
                temp_2fa_expires_at = NULL,
                last_login = NOW()
                WHERE id = ?");
            $saveStmt->execute([$secret, json_encode($hashedCodes), $user['id']]);

            // Save clean session
            createAdminSession($user);
            unset($_SESSION['setup_2fa_secret']);

            $setupSuccess = true;
            $generatedRecoveryCodes = $rawRecoveryCodes;
        } else {
            $error = 'Invalid verification code. Please make sure the time on your device is accurate, or request an email code.';
        }
    }
}

$qrUrl = TOTP::getQrCodeImageUrl('Krosskhana', $user['email'], $secret);
$formattedSecret = implode(' ', str_split($secret, 4));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Two-Factor Authentication - Krosskhana Admin</title>
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
<body class="bg-gradient-to-br from-gray-900 via-black to-gray-900 min-h-screen font-poppins flex items-center justify-center py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg w-full space-y-8">
        <!-- Logo and Title -->
        <div class="text-center">
            <img class="mx-auto h-14 w-auto hover:scale-110 transition-transform duration-300" src="../assets/logos/Krosskhana-02.png" alt="Krosskhana">
            <h2 class="mt-4 text-2xl sm:text-3xl font-extrabold text-white font-orbitron">
                Security Setup (2FA)
            </h2>
            <p class="mt-2 text-sm text-gray-400">
                Mandatory Two-Factor Authentication for first-time sign-in
            </p>
        </div>

        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 sm:p-8 shadow-2xl border border-white/20">
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

            <?php if ($setupSuccess): ?>
                <!-- 2FA Setup Complete & Recovery Codes -->
                <div class="text-center space-y-5">
                    <div class="w-14 h-14 bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 rounded-full flex items-center justify-center mx-auto text-2xl">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white font-orbitron">2FA Activated Successfully!</h3>
                    <p class="text-sm text-gray-300">
                        Save these backup recovery codes in a secure place. If you ever lose your phone or authenticator app, each code can be used once to access your account:
                    </p>

                    <!-- Recovery codes box -->
                    <div id="recovery-codes-box" class="bg-black/60 border border-white/20 rounded-xl p-4 text-left font-mono text-sm grid grid-cols-2 gap-2 text-emerald-300">
                        <?php foreach ($generatedRecoveryCodes as $rcode): ?>
                            <div class="py-1 px-2 bg-white/5 rounded text-center tracking-wider"><?php echo htmlspecialchars($rcode); ?></div>
                        <?php endforeach; ?>
                    </div>

                    <div class="flex gap-3 justify-center pt-2">
                        <button type="button" onclick="copyRecoveryCodes()" id="btn-copy-codes" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-lg border border-white/20 transition flex items-center gap-1.5">
                            <i class="fas fa-copy"></i> Copy Codes
                        </button>
                    </div>

                    <div class="pt-4 border-t border-white/10">
                        <a href="index.php" class="block w-full py-3 px-4 rounded-xl text-black bg-gradient-to-r from-white to-gray-300 hover:from-gray-200 hover:to-gray-400 font-bold transition shadow-lg transform hover:scale-[1.02]">
                            Continue to Admin Dashboard <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <!-- 2FA Enrollment Steps -->
                <div class="space-y-6">
                    <!-- Step 1: Scan QR Code -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-white font-semibold text-sm">
                            <span class="w-6 h-6 rounded-full bg-white text-black text-xs font-bold flex items-center justify-center">1</span>
                            <span>Scan with your Authenticator App</span>
                        </div>
                        <p class="text-xs text-gray-300 leading-relaxed">
                            Open <strong>Google Authenticator</strong>, <strong>Microsoft Authenticator</strong>, or your device password manager and scan the QR code below:
                        </p>

                        <div class="flex flex-col items-center justify-center bg-white p-4 rounded-2xl shadow-inner max-w-xs mx-auto">
                            <img src="<?php echo htmlspecialchars($qrUrl); ?>" alt="2FA QR Code" class="w-44 h-44 object-contain">
                            <span class="text-[11px] text-gray-500 mt-2 font-medium">Krosskhana: <?php echo htmlspecialchars($user['email']); ?></span>
                        </div>

                        <!-- Manual Key Display -->
                        <div class="text-center">
                            <p class="text-[11px] text-gray-400 mb-1">Cannot scan? Enter code manually into your app:</p>
                            <code class="px-3 py-1.5 bg-black/40 border border-white/20 rounded-lg text-xs font-mono text-white tracking-widest inline-block select-all">
                                <?php echo htmlspecialchars($formattedSecret); ?>
                            </code>
                        </div>
                    </div>

                    <div class="border-t border-white/10"></div>

                    <!-- Step 2: Verification Code -->
                    <form class="space-y-4" method="POST">
                        <div class="flex items-center gap-2 text-white font-semibold text-sm">
                            <span class="w-6 h-6 rounded-full bg-white text-black text-xs font-bold flex items-center justify-center">2</span>
                            <span>Enter the 6-Digit Code</span>
                        </div>

                        <div>
                            <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="8" required autofocus autocomplete="one-time-code"
                                   class="text-center font-mono tracking-widest text-2xl appearance-none block w-full py-3 px-4 border border-white/20 rounded-xl placeholder-gray-500 text-white bg-white/10 focus:outline-none focus:ring-2 focus:ring-gray-500"
                                   placeholder="000000">
                        </div>

                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-black bg-gradient-to-r from-white to-gray-300 hover:from-gray-200 hover:to-gray-400 focus:outline-none transition-all duration-300 transform hover:scale-[1.02] shadow-lg">
                            <i class="fas fa-check-circle mr-2 mt-0.5"></i> Verify & Enable 2FA
                        </button>
                    </form>

                    <!-- Alternate: Email code -->
                    <div class="text-center pt-2">
                        <a href="setup-2fa.php?send_email_code=1" class="text-xs text-gray-400 hover:text-white transition underline">
                            <i class="fas fa-envelope mr-1"></i> Or send a 6-digit confirmation code to my email
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center pt-4 border-t border-white/10 mt-6">
                <a href="logout.php" class="text-xs text-gray-400 hover:text-white transition">
                    <i class="fas fa-sign-out-alt mr-1"></i> Cancel & Sign Out
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
        function copyRecoveryCodes() {
            const box = document.getElementById('recovery-codes-box');
            const text = Array.from(box.querySelectorAll('div')).map(d => d.innerText.trim()).join('\n');
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById('btn-copy-codes');
                btn.innerHTML = '<i class="fas fa-check text-emerald-400"></i> Copied!';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-copy"></i> Copy Codes';
                }, 2000);
            });
        }
    </script>
</body>
</html>
