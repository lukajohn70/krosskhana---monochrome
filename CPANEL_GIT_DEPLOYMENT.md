# cPanel Git Version Control & Deployment Guide
**Domain**: `https://krosskhana.org`  
**cPanel Account**: `krosskha`  
**Production Document Root**: `/home/krosskha/public_html/`  

---

## 🚀 1. How cPanel Version Control Works

This repository contains a configured [`.cpanel.yml`](file:///.cpanel.yml) file. When you push to cPanel (or deploy via cPanel Git Version Control), cPanel automatically executes the deployment tasks to copy files to `/home/krosskha/public_html/`.

### `.cpanel.yml` Deployment Script:
```yaml
---
deployment:
  tasks:
    - export DEPLOYPATH=/home/krosskha/public_html/
    - /bin/cp -R * $DEPLOYPATH
    - /bin/chmod -R 755 $DEPLOYPATH/admin
    - /bin/chmod -R 755 $DEPLOYPATH/assets
    - /bin/chmod -R 755 $DEPLOYPATH/includes
    - /bin/chmod 755 $DEPLOYPATH/index.php
```

---

## 🛠️ 2. Setting Up Git in cPanel

### Method A: Direct cPanel Git Push (Recommended)
1. Log into your **cPanel** at `https://krosskhana.org:2083`.
2. Under the **Files** section, click **Git™ Version Control**.
3. Click the **Create** button.
4. Toggle **Create a New Repository**.
   - **Repository Path**: `repositories/krosskhana`
   - **Repository Name**: `krosskhana`
5. Click **Create**.
6. Copy the **Clone URL** provided by cPanel (e.g., `ssh://krosskha@krosskhana.org/home/krosskha/repositories/krosskhana` or custom SSH port).
7. On your local machine in terminal, add the remote and push:
   ```bash
   git remote add cpanel ssh://krosskha@krosskhana.org/home/krosskha/repositories/krosskhana
   git push cpanel master
   ```
8. In cPanel, click **Manage** -> **Pull or Deploy** -> **Deploy HEAD Commit** (or enable automated deployment).

---

### Method B: Via GitHub / GitLab
1. Create a repository on GitHub (e.g., `https://github.com/lukajohn70/krosskhana.git`).
2. Locally, set origin and push:
   ```bash
   git remote add origin https://github.com/lukajohn70/krosskhana.git
   git push -u origin master
   ```
3. In cPanel **Git™ Version Control**:
   - Click **Create**.
   - Toggle **Clone a Repository**.
   - **Clone URL**: `https://github.com/lukajohn70/krosskhana.git`
   - **Repository Path**: `repositories/krosskhana`
   - Click **Create**.
4. In cPanel, under **Manage** -> **Pull or Deploy**:
   - Click **Update from Remote**.
   - Click **Deploy HEAD Commit** to push live to `/home/krosskha/public_html/`.

---

## 🔐 3. Database Migration for 2FA & Password Reset

Ensure the production database on cPanel has the new schema:
1. Open **phpMyAdmin** in cPanel.
2. Select your production database (`krosskha_krosskhana_db`).
3. Click the **SQL** tab and execute:

```sql
-- 1. Password Resets Table
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

-- 2. 2FA Columns for admin_users
ALTER TABLE admin_users 
    ADD COLUMN IF NOT EXISTS two_factor_secret VARCHAR(255) NULL AFTER role,
    ADD COLUMN IF NOT EXISTS two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER two_factor_secret,
    ADD COLUMN IF NOT EXISTS two_factor_confirmed_at DATETIME NULL AFTER two_factor_enabled,
    ADD COLUMN IF NOT EXISTS recovery_codes TEXT NULL AFTER two_factor_confirmed_at,
    ADD COLUMN IF NOT EXISTS temp_2fa_code VARCHAR(20) NULL AFTER recovery_codes,
    ADD COLUMN IF NOT EXISTS temp_2fa_expires_at DATETIME NULL AFTER temp_2fa_code,
    ADD COLUMN IF NOT EXISTS must_setup_2fa TINYINT(1) NOT NULL DEFAULT 1 AFTER temp_2fa_expires_at;
```

---

## 🛡️ 4. How 2FA on First Sign-in Works

1. Admin enters username/email and password on `admin/login.php`.
2. Because `must_setup_2fa = 1` or `two_factor_enabled = 0`, the system redirects to [`admin/setup-2fa.php`](file:///admin/setup-2fa.php).
3. The admin scans the QR code in **Google Authenticator**, **Microsoft Authenticator**, or Apple Keychain (or inputs the secret key manually, or clicks "Email me a 6-digit code").
4. Admin submits the 6-digit code.
5. System activates 2FA, generates 8 backup recovery codes for the admin, and logs them in.
6. On all subsequent sign-ins, [`admin/verify-2fa.php`](file:///admin/verify-2fa.php) prompts for the 6-digit authenticator code (with email OTP or recovery code options).

---

## 🔑 5. How Password Reset Works

1. Admin clicks **Forgot password?** on [`admin/login.php`](file:///admin/login.php) or navigates to [`admin/forgot-password.php`](file:///admin/forgot-password.php).
2. Enters their email or username.
3. System sends a secure, single-use, 1-hour expiration link to their email.
4. Admin clicks the link to [`admin/reset-password.php?token=...`](file:///admin/reset-password.php).
5. Sets a new password (min. 8 characters) and signs in.
