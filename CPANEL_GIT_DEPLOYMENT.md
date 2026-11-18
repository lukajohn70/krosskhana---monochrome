# cPanel Git Version Control & Deployment Guide
**Domain**: `https://krosskhana.org`  
**cPanel Account**: `krosskha`  
**Server Home Directory**: `/home1/krosskha/`  
**Production Document Root**: `/home1/krosskha/public_html/`  
**Repository Path on Server**: `/home1/krosskha/repositories/krosskhana---monochrome`  
**Remote GitHub URL**: `https://github.com/lukajohn70/krosskhana---monochrome.git`  

---

## 🚀 1. How cPanel Version Control Works

This repository contains a configured [`.cpanel.yml`](file:///.cpanel.yml) file. When you deploy via cPanel Git Version Control, cPanel automatically executes the deployment tasks to copy files to `/home1/krosskha/public_html/`.

### `.cpanel.yml` Deployment Script:
```yaml
---
deployment:
  tasks:
    - export DEPLOYPATH=/home1/krosskha/public_html/
    - /bin/cp -R * $DEPLOYPATH
```

---

## 🛠️ 2. Setting Up Git in cPanel

### Method B: Via GitHub (Current Configuration)
1. Repository on GitHub: `https://github.com/lukajohn70/krosskhana---monochrome.git`.
2. In cPanel **Git™ Version Control**:
   - Repository Path: `/home1/krosskha/repositories/krosskhana---monochrome`
   - Remote URL: `https://github.com/lukajohn70/krosskhana---monochrome.git`
   - Checked-Out Branch: `main`
3. In cPanel, under **Manage** -> **Pull or Deploy**:
   - Click **Update from Remote** to pull the latest commits.
   - Click **Deploy HEAD Commit** to copy all files live to `/home1/krosskha/public_html/`.

---

## ⚠️ 3. Troubleshooting cPanel "The system cannot deploy" Errors

If cPanel shows:
> *"The system cannot deploy. For deployment, ensure that your repository meets the following requirements: A valid .cpanel.yml file exists... No uncommitted changes exist on the checked-out branch."*

Here is how to resolve each requirement:

### A. "A valid .cpanel.yml file exists"
- **Cause**: The server path in older configurations was set to `/home/krosskha/public_html/`, but your server account root is actually `/home1/krosskha/public_html/`.
- **Fix**: The repository now has the correct path `/home1/krosskha/public_html/`. Click **Update from Remote** in cPanel to fetch this fix before clicking Deploy.

### B. "No uncommitted changes exist on the checked-out branch"
- **Cause**: If any file inside `/home1/krosskha/repositories/krosskhana---monochrome` was edited, created, or generated directly on the server (e.g., via File Manager or cPanel editor), Git considers the branch "dirty" and will refuse to deploy.
- **Fix (Terminal in cPanel)**:
  1. Open **Terminal** in cPanel (under the "Advanced" or "Tools" section).
  2. Run:
     ```bash
     cd /home1/krosskha/repositories/krosskhana---monochrome
     git reset --hard HEAD
     git clean -fd
     git status
     ```
  3. Ensure it reports `nothing to commit, working tree clean`.
  4. Run `git pull origin main`.
  5. Go back to cPanel **Git™ Version Control** &rarr; **Pull or Deploy** &rarr; click **Deploy HEAD Commit**.

---

## 🔐 4. Database Migration for 2FA & Password Reset

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
