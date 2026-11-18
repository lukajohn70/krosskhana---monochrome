<?php
/**
 * TOTP (Time-based One-Time Password) RFC 6238 & 2FA Helper
 * Self-contained without external dependencies
 */

class TOTP {
    private static $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate random Base32 secret (16 chars = 80 bits, standard for TOTP)
     */
    public static function generateSecret($length = 16) {
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::$base32chars[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Decode a base32 string into raw bytes
     */
    private static function base32Decode($secret) {
        $secret = strtoupper(str_replace(' ', '', $secret));
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';
        for ($i = 0; $i < strlen($secret); $i++) {
            $val = strpos(self::$base32chars, $secret[$i]);
            if ($val === false) continue;
            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }
        return $output;
    }

    /**
     * Calculate TOTP code for a given secret & timeSlice
     */
    public static function getCode($secret, $timeSlice = null) {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }
        $secretKey = self::base32Decode($secret);
        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);
        $hmac = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashpart = substr($hmac, $offset, 4);
        $value = unpack('N', $hashpart)[1] & 0x7FFFFFFF;
        return str_pad($value % 1000000, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a 6-digit TOTP code with time discrepancy allowance
     */
    public static function verifyCode($secret, $code, $discrepancy = 1) {
        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }
        $currentTimeSlice = floor(time() / 30);
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            if (hash_equals(self::getCode($secret, $currentTimeSlice + $i), $code)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get standard otpauth:// URL
     */
    public static function getOtpAuthUrl($issuer, $accountName, $secret) {
        $issuerEncoded = rawurlencode($issuer);
        $accountEncoded = rawurlencode($accountName);
        return "otpauth://totp/{$issuerEncoded}:{$accountEncoded}?secret={$secret}&issuer={$issuerEncoded}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Get QR Code image URL via secure QR image service
     */
    public static function getQrCodeImageUrl($issuer, $accountName, $secret) {
        $otpUrl = self::getOtpAuthUrl($issuer, $accountName, $secret);
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=10&data=" . urlencode($otpUrl);
    }
}

/**
 * Generate recovery codes (e.g. 8 codes formatted XXXX-XXXX)
 */
function generateRecoveryCodes($count = 8) {
    $codes = [];
    for ($i = 0; $i < $count; $i++) {
        $p1 = strtoupper(bin2hex(random_bytes(2)));
        $p2 = strtoupper(bin2hex(random_bytes(2)));
        $codes[] = $p1 . '-' . $p2;
    }
    return $codes;
}

/**
 * Generate 6-digit Email OTP
 */
function generateEmailOtp() {
    return str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Send 2FA verification email
 */
function sendTwoFactorEmailCode($email, $code, $username = 'Admin') {
    require_once __DIR__ . '/../../includes/email-utils.php';

    $subject = "Your Krosskhana Admin Verification Code: $code";
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #0b0f17; color: #ffffff; padding: 20px; }
            .container { max-width: 560px; margin: 0 auto; background: #161e2e; border: 1px solid #2d3748; border-radius: 16px; padding: 32px; }
            .header { text-align: center; margin-bottom: 24px; }
            .code-box { background: #0f172a; border: 2px dashed #4b5563; border-radius: 12px; padding: 20px; text-align: center; margin: 24px 0; }
            .code { font-family: monospace; font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #ffffff; }
            .footer { margin-top: 32px; font-size: 12px; color: #9ca3af; text-align: center; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2 style="color:#ffffff; margin:0 0 8px 0; font-size:24px;">Two-Factor Authentication</h2>
                <p style="color:#9ca3af; font-size:14px; margin:0;">Security verification for Krosskhana Admin</p>
            </div>
            <p style="color:#e5e7eb; font-size:15px;">Hello <strong>' . htmlspecialchars($username) . '</strong>,</p>
            <p style="color:#cbd5e1; font-size:14px;">Use the verification code below to complete your sign-in to Krosskhana Admin. This code is valid for 10 minutes.</p>
            <div class="code-box">
                <div class="code">' . htmlspecialchars($code) . '</div>
            </div>
            <p style="color:#94a3b8; font-size:13px;">If you did not request this verification code, please review your account security immediately.</p>
            <div class="footer">
                &copy; ' . date('Y') . ' Krosskhana Motorsports. All rights reserved.
            </div>
        </div>
    </body>
    </html>
    ';

    return sendEmail($email, $subject, $message, true);
}
