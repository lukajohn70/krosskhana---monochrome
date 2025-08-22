# Krosskhana Campaign & Newsletter System

## Overview

The Krosskhana Campaign & Newsletter System is a comprehensive email marketing solution that allows you to create targeted campaigns, manage subscribers, and track email performance directly from your admin dashboard.

## Features

### 🎯 Campaign Management
- **Create Campaigns**: Build email campaigns with rich text editor
- **Target Audience**: Segment subscribers by tags, date ranges, or send to all
- **Scheduling**: Send immediately or schedule for later
- **Templates**: Use pre-built email templates or create custom ones
- **Draft System**: Save campaigns as drafts and edit later

### 📊 Analytics & Tracking
- **Open Tracking**: Track email opens with pixel tracking
- **Click Tracking**: Monitor link clicks and engagement
- **Performance Metrics**: View open rates, click rates, and delivery status
- **Recipient Analytics**: Detailed analytics per recipient
- **Export Data**: Export recipient lists and campaign data

### 👥 Subscriber Management
- **Tag System**: Organize subscribers with custom tags
- **Segmentation**: Target specific groups based on various criteria
- **Status Management**: Track active, unsubscribed, and bounced subscribers
- **Bulk Operations**: Manage multiple subscribers at once

### ⚙️ Email Configuration
- **SMTP Support**: Configure SMTP settings for reliable delivery
- **Template System**: Create and manage email templates
- **Tracking Integration**: Built-in open and click tracking
- **Unsubscribe Handling**: Automatic unsubscribe link generation

## Database Setup

Run the updated database schema to create the necessary tables:

```sql
-- Run the SQL commands in includes/database.sql
-- This will create:
-- - campaigns table
-- - campaign_recipients table
-- - email_templates table
-- - subscriber_tags table
-- - subscriber_tag_relationships table
-- - email_settings table
```

## File Structure

```
admin/
├── campaigns.php              # Main campaign management page
├── campaign-details.php       # Campaign analytics and details
├── api/
│   ├── campaigns.php          # Campaign CRUD operations
│   ├── tags.php              # Tag management
│   ├── templates.php         # Template management
│   ├── settings.php          # Email settings
│   └── campaign-recipients.php # Recipient management
├── tracking.php              # Email tracking system
└── index.php                 # Updated dashboard with campaign stats

includes/
├── email-helper.php          # Email sending utilities
└── database.sql              # Updated database schema
```

## Setup Instructions

### 1. Database Setup
1. Run the SQL commands in `includes/database.sql`
2. This creates all necessary tables and inserts default data

### 2. Email Configuration
1. Go to Admin Dashboard → Campaigns → Settings
2. Configure your SMTP settings:
   - SMTP Host (e.g., smtp.gmail.com)
   - SMTP Port (usually 587 for TLS)
   - SMTP Username (your email)
   - SMTP Password (your email password or app password)
   - From Email and Name

### 3. Create Email Templates
1. Go to Campaigns → Templates
2. Create or modify email templates
3. Use placeholders like `{{name}}`, `{{email}}`, `{{subject}}`

### 4. Set Up Subscriber Tags
1. Go to Newsletter Management
2. Create tags for subscriber segmentation
3. Assign tags to subscribers

## Usage Guide

### Creating a Campaign

1. **Navigate to Campaigns**
   - Go to Admin Dashboard → Campaigns

2. **Create New Campaign**
   - Click "Create Campaign"
   - Fill in campaign name and subject
   - Choose target audience:
     - All Active Subscribers
     - Subscribers with Specific Tags
     - Custom Segment (by date range)

3. **Design Email Content**
   - Use the rich text editor
   - Add images, links, and formatting
   - Use placeholders: `{{name}}`, `{{email}}`

4. **Preview Recipients**
   - Click "Preview" to see recipient count
   - Review target audience

5. **Send or Schedule**
   - Send immediately
   - Schedule for later
   - Save as draft

### Managing Subscribers

1. **Add Tags**
   - Create tags like "Racing Enthusiasts", "Corporate Partners"
   - Assign colors for easy identification

2. **Segment Subscribers**
   - Assign tags to subscribers
   - Use date ranges for targeting
   - Create custom segments

3. **Monitor Status**
   - Track active vs unsubscribed
   - Monitor bounce rates
   - Export subscriber lists

### Analyzing Campaign Performance

1. **View Campaign Details**
   - Click on any campaign to see detailed analytics
   - View open rates, click rates, and delivery status

2. **Recipient Analytics**
   - See individual recipient performance
   - Track opens and clicks per recipient
   - Export detailed reports

3. **Performance Charts**
   - Visual representation of campaign performance
   - Compare different campaigns

## Email Templates

### Default Template
The system includes a default email template with:
- Krosskhana branding
- Responsive design
- Unsubscribe link
- Web version link
- Tracking integration

### Custom Templates
Create custom templates with:
- HTML/CSS styling
- Placeholder support
- Responsive design
- Brand consistency

### Template Placeholders
- `{{name}}` - Subscriber name
- `{{email}}` - Subscriber email
- `{{subject}}` - Campaign subject
- `{{unsubscribe_url}}` - Unsubscribe link
- `{{web_version_url}}` - Web version link

## Tracking System

### Open Tracking
- Automatic pixel tracking
- Tracks when emails are opened
- Updates campaign statistics

### Click Tracking
- All links are automatically tracked
- Monitors click-through rates
- Provides detailed click analytics

### Privacy Compliance
- Unsubscribe links in every email
- GDPR-compliant tracking
- Clear privacy notices

## Best Practices

### Campaign Creation
1. **Subject Lines**: Keep them clear and engaging
2. **Content**: Use compelling copy and clear CTAs
3. **Testing**: Send test emails before campaigns
4. **Segmentation**: Target specific audiences for better engagement

### Email Design
1. **Mobile Responsive**: Ensure emails work on all devices
2. **Brand Consistency**: Use consistent colors and fonts
3. **Clear CTAs**: Make call-to-actions prominent
4. **Alt Text**: Add alt text to images

### List Management
1. **Regular Cleaning**: Remove inactive subscribers
2. **Permission-Based**: Only email subscribers who opted in
3. **Segmentation**: Use tags for targeted campaigns
4. **Monitoring**: Track bounce rates and engagement

## Troubleshooting

### Email Delivery Issues
1. **Check SMTP Settings**: Verify SMTP configuration
2. **Test Connection**: Use email settings test
3. **Check Spam Filters**: Ensure emails aren't marked as spam
4. **Monitor Bounces**: Handle bounced emails promptly

### Tracking Issues
1. **Check Tracking URLs**: Ensure tracking.php is accessible
2. **Verify Tokens**: Check tracking token generation
3. **Monitor Logs**: Check server error logs

### Performance Issues
1. **Database Optimization**: Ensure proper indexing
2. **Server Resources**: Monitor server performance
3. **Batch Processing**: Use batch sending for large campaigns

## Security Considerations

1. **Authentication**: Ensure admin authentication is secure
2. **Data Protection**: Encrypt sensitive subscriber data
3. **Access Control**: Limit admin access to authorized users
4. **Regular Updates**: Keep system updated

## Support

For technical support or questions about the campaign system:
1. Check this documentation
2. Review error logs
3. Test with small campaigns first
4. Contact system administrator

## Future Enhancements

Potential improvements for the system:
- A/B testing capabilities
- Advanced automation workflows
- Integration with CRM systems
- Enhanced analytics and reporting
- Mobile app for campaign management
- Advanced segmentation tools
- Email personalization features

---

**Krosskhana Campaign System** - Empowering your email marketing with powerful tools and insights.
