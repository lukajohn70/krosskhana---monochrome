# Krosskhana Admin Dashboard

A comprehensive admin dashboard for managing newsletter subscriptions and racer registrations for the Krosskhana automotive lifestyle event website.

## 🚀 Features

### Dashboard Overview
- **Real-time Statistics**: View total subscribers, registrations, and status breakdowns
- **Recent Activity**: See the latest newsletter subscriptions and racer registrations
- **Quick Actions**: Easy navigation to detailed management pages

### Newsletter Management
- **View All Subscribers**: Complete list with search and filtering
- **Status Management**: Activate/deactivate subscriptions
- **Export Functionality**: Download subscriber data as CSV
- **Search & Filter**: Find subscribers by name, email, or status
- **Pagination**: Handle large datasets efficiently

### Racer Registration Management
- **Comprehensive View**: All registration details in organized tables
- **Status Updates**: Approve, reject, or mark as contacted
- **Admin Notes**: Add internal notes for each registration
- **Detailed View**: Full registration information in modal popup
- **Export Data**: Download registration data as CSV
- **Advanced Search**: Search by name, email, alias, or status

### Security Features
- **Secure Authentication**: Password-protected admin access
- **Session Management**: Automatic logout and session security
- **Input Validation**: Sanitized inputs and SQL injection protection
- **CSRF Protection**: Form submission security

## 📋 Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB 10.2+)
- Web server (Apache/Nginx)
- MAMP/XAMPP (for local development)

## 🛠️ Installation

### 1. Database Setup

1. **Run the Setup Script**:
   ```
   http://localhost/krosskhana/setup.php
   ```
   
   This will:
   - Create the `krosskhana_db` database
   - Create all necessary tables
   - Set up the default admin user

2. **Manual Database Setup** (Alternative):
   ```sql
   -- Import the SQL file directly
   mysql -u root -p < includes/database.sql
   ```

### 2. Configuration

1. **Database Configuration** (`includes/config.php`):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_PORT', 3306);
   define('DB_USER', 'root');
   define('DB_PASSWORD', 'root'); // MAMP default
   define('DB_NAME', 'krosskhana_db');
   ```

2. **Admin Password** (in `includes/config.php`):
   ```php
   define('ADMIN_PASSWORD', 'krosskhana2024');
   ```

### 3. File Permissions

Ensure the following directories are writable:
```bash
chmod 755 admin/
chmod 644 includes/config.php
```

## 🔐 Accessing the Admin Dashboard

### Default Login Credentials
- **Username**: `admin`
- **Password**: `krosskhana2024`

### Access URL
```
http://localhost/krosskhana/admin/login.php
```

## 📊 Database Schema

### Newsletter Subscriptions Table
```sql
newsletter_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

### Racer Registrations Table
```sql
racer_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Participation Types
    autocross_competitor BOOLEAN DEFAULT FALSE,
    drifting_challenge BOOLEAN DEFAULT FALSE,
    exhibition_driver BOOLEAN DEFAULT FALSE,
    safety_training BOOLEAN DEFAULT FALSE,
    
    -- Personal Information
    full_name VARCHAR(255) NOT NULL,
    racing_alias VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    location VARCHAR(255),
    
    -- Vehicle Details
    vehicle_make VARCHAR(100),
    vehicle_model VARCHAR(100),
    vehicle_year INT,
    modifications TEXT,
    
    -- Racing Experience
    novice BOOLEAN DEFAULT FALSE,
    intermediate BOOLEAN DEFAULT FALSE,
    semi_pro BOOLEAN DEFAULT FALSE,
    professional BOOLEAN DEFAULT FALSE,
    
    -- Additional Information
    additional_info TEXT,
    
    -- Status and Timestamps
    status ENUM('pending', 'approved', 'rejected', 'contacted') DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

## 🎯 Usage Guide

### Managing Newsletter Subscriptions

1. **View Subscribers**:
   - Navigate to "Newsletter" from the dashboard
   - Use search to find specific subscribers
   - Filter by status (active/unsubscribed)

2. **Update Status**:
   - Click "Activate" or "Unsubscribe" for each subscriber
   - Confirm the action in the modal dialog

3. **Export Data**:
   - Click "Export" button to download CSV file
   - Filter data before exporting if needed

### Managing Racer Registrations

1. **View Registrations**:
   - Navigate to "Registrations" from the dashboard
   - Use search to find specific registrations
   - Filter by status (pending/approved/rejected/contacted)

2. **View Details**:
   - Click "View" to see complete registration information
   - All form data is displayed in an organized modal

3. **Update Status**:
   - Click "Status" to change registration status
   - Add admin notes for internal reference
   - Choose from: pending, approved, rejected, contacted

4. **Export Data**:
   - Click "Export" to download comprehensive CSV
   - Includes all registration details and status information

## 🔧 Customization

### Adding New Admin Users

1. **Direct Database Insert**:
   ```sql
   INSERT INTO admin_users (username, password_hash, email, role) 
   VALUES ('newadmin', '$2y$10$...', 'admin@example.com', 'admin');
   ```

2. **Password Hashing**:
   ```php
   $hashedPassword = password_hash('your_password', PASSWORD_DEFAULT);
   ```

### Modifying Status Options

Edit the database schema to add new status options:
```sql
ALTER TABLE racer_registrations 
MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'contacted', 'new_status') DEFAULT 'pending';
```

### Customizing Email Templates

The system uses JSON responses for form processing. To add email notifications:

1. **Modify Processing Files**:
   - `process-newsletter.php`
   - `process-registration.php`

2. **Add Email Functionality**:
   ```php
   // Example email notification
   mail($adminEmail, 'New Registration', $message, $headers);
   ```

## 🛡️ Security Considerations

### Password Security
- Change the default admin password immediately
- Use strong, unique passwords
- Consider implementing password policies

### Database Security
- Use dedicated database user with limited permissions
- Regularly backup your database
- Keep MySQL/MySQLi updated

### File Security
- Ensure `includes/config.php` is not publicly accessible
- Set proper file permissions
- Consider moving sensitive files outside web root

## 📈 Analytics and Reporting

### Built-in Statistics
- Total subscribers count
- Active vs unsubscribed ratio
- Registration status breakdown
- Recent activity tracking

### Export Capabilities
- CSV export for newsletter subscribers
- Comprehensive registration data export
- Filtered exports based on search criteria

## 🔄 Maintenance

### Regular Tasks
1. **Database Backups**: Export data regularly
2. **Log Monitoring**: Check error logs for issues
3. **User Management**: Review and update admin accounts
4. **Data Cleanup**: Remove old or invalid entries

### Performance Optimization
- Index frequently searched columns
- Archive old data if needed
- Monitor database performance

## 🆘 Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Check database credentials in `config.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **Login Issues**:
   - Reset admin password in database
   - Check session configuration
   - Clear browser cookies

3. **Form Submission Errors**:
   - Check file permissions
   - Verify PHP error logs
   - Ensure all required files exist

### Error Logs
Check these locations for error information:
- PHP error log
- MySQL error log
- Web server error log

## 📞 Support

For technical support or feature requests:
- Check the main project documentation
- Review PHP and MySQL error logs
- Ensure all prerequisites are met

## 🔄 Updates

To update the admin dashboard:
1. Backup your database
2. Replace admin files with new versions
3. Run any database migration scripts
4. Test functionality

---

**Krosskhana Admin Dashboard** - Empowering event management with modern web technology.
