<?php
require_once 'includes/config.php';

$pdo = getDBConnection();

if (!$pdo) {
    die("Database connection failed");
}

echo "<h2>Adding Email Templates</h2>";

// Define additional templates
$templates = [
    [
        'name' => 'Event Announcement',
        'subject' => '🚗 New Krosskhana Event Coming Soon!',
        'content' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f97316, #ea580c); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #fff; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .btn { display: inline-block; padding: 12px 24px; background: #f97316; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .unsubscribe { color: #666; font-size: 12px; }
        .event-details { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏁 Krosskhana</h1>
            <p>Fueling Culture. Driving Stories. Partnering for Impact.</p>
        </div>
        <div class="content">
            <h2>🚗 New Event Announcement</h2>
            <p>Get ready for another incredible Krosskhana experience!</p>
            
            <div class="event-details">
                <h3>Event Details:</h3>
                <p><strong>Date:</strong> [Event Date]</p>
                <p><strong>Location:</strong> [Event Location]</p>
                <p><strong>Time:</strong> [Event Time]</p>
            </div>
            
            <p>Join us for an unforgettable day of:</p>
            <ul>
                <li>🏎️ High-octane racing action</li>
                <li>🎵 Live music performances</li>
                <li>👗 Fashion showcases</li>
                <li>🍔 Food and refreshments</li>
                <li>📸 Photo opportunities</li>
            </ul>
            
            <a href="#" class="btn">Register Now</a>
            <a href="#" class="btn">Learn More</a>
        </div>
        <div class="footer">
            <p>&copy; 2024 Krosskhana. All rights reserved.</p>
            <p class="unsubscribe">
                <a href="{{unsubscribe_url}}">Unsubscribe</a> | 
                <a href="{{web_version_url}}">View in browser</a>
            </p>
        </div>
    </div>
</body>
</html>'
    ],
    [
        'name' => 'Racer Registration Confirmation',
        'subject' => '✅ Your Krosskhana Registration is Confirmed!',
        'content' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f97316, #ea580c); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #fff; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .btn { display: inline-block; padding: 12px 24px; background: #f97316; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .unsubscribe { color: #666; font-size: 12px; }
        .confirmation-box { background: #e8f5e8; border: 2px solid #4caf50; padding: 20px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏁 Krosskhana</h1>
            <p>Fueling Culture. Driving Stories. Partnering for Impact.</p>
        </div>
        <div class="content">
            <div class="confirmation-box">
                <h2>✅ Registration Confirmed!</h2>
                <p>Welcome to the Krosskhana family! Your registration has been successfully processed.</p>
            </div>
            
            <h3>Registration Details:</h3>
            <p><strong>Racer Name:</strong> [Racer Name]</p>
            <p><strong>Racing Alias:</strong> [Racing Alias]</p>
            <p><strong>Event:</strong> [Event Name]</p>
            <p><strong>Registration ID:</strong> [Registration ID]</p>
            
            <h3>What\'s Next?</h3>
            <ol>
                <li>Check your email for detailed event information</li>
                <li>Review the safety guidelines and rules</li>
                <li>Prepare your vehicle for inspection</li>
                <li>Join our pre-event briefing</li>
            </ol>
            
            <p><strong>Important:</strong> Please arrive 2 hours before your scheduled time for vehicle inspection and safety briefing.</p>
            
            <a href="#" class="btn">View Event Details</a>
            <a href="#" class="btn">Download Safety Guidelines</a>
        </div>
        <div class="footer">
            <p>&copy; 2024 Krosskhana. All rights reserved.</p>
            <p class="unsubscribe">
                <a href="{{unsubscribe_url}}">Unsubscribe</a> | 
                <a href="{{web_version_url}}">View in browser</a>
            </p>
        </div>
    </div>
</body>
</html>'
    ],
    [
        'name' => 'Sponsorship Opportunity',
        'subject' => '🤝 Partner with Krosskhana - Exclusive Sponsorship Opportunities',
        'content' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f97316, #ea580c); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #fff; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .btn { display: inline-block; padding: 12px 24px; background: #f97316; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .unsubscribe { color: #666; font-size: 12px; }
        .highlight-box { background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏁 Krosskhana</h1>
            <p>Fueling Culture. Driving Stories. Partnering for Impact.</p>
        </div>
        <div class="content">
            <h2>🤝 Exclusive Sponsorship Opportunity</h2>
            <p>Join Krosskhana as a strategic partner and connect with our passionate community of automotive enthusiasts, music lovers, and fashion-forward individuals.</p>
            
            <div class="highlight-box">
                <h3>Why Partner with Krosskhana?</h3>
                <ul>
                    <li>🎯 Reach a diverse, engaged audience</li>
                    <li>📈 Increase brand visibility and recognition</li>
                    <li>🤝 Build meaningful partnerships</li>
                    <li>📊 Measurable ROI and analytics</li>
                    <li>🌟 Premium brand positioning</li>
                </ul>
            </div>
            
            <h3>Sponsorship Packages Available:</h3>
            <ul>
                <li><strong>Platinum Partner:</strong> Premium branding, exclusive access, VIP events</li>
                <li><strong>Gold Partner:</strong> Prominent branding, event participation</li>
                <li><strong>Silver Partner:</strong> Standard branding, media mentions</li>
                <li><strong>Bronze Partner:</strong> Basic branding, social media presence</li>
            </ul>
            
            <p><strong>Limited Availability:</strong> Only a few sponsorship slots remain for our upcoming events.</p>
            
            <a href="#" class="btn">Download Sponsorship Kit</a>
            <a href="#" class="btn">Schedule a Meeting</a>
        </div>
        <div class="footer">
            <p>&copy; 2024 Krosskhana. All rights reserved.</p>
            <p class="unsubscribe">
                <a href="{{unsubscribe_url}}">Unsubscribe</a> | 
                <a href="{{web_version_url}}">View in browser</a>
            </p>
        </div>
    </div>
</body>
</html>'
    ],
    [
        'name' => 'Newsletter Update',
        'subject' => '📰 Krosskhana Newsletter - Latest Updates & Events',
        'content' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f97316, #ea580c); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #fff; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .btn { display: inline-block; padding: 12px 24px; background: #f97316; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .unsubscribe { color: #666; font-size: 12px; }
        .news-item { border-bottom: 1px solid #eee; padding: 15px 0; }
        .news-item:last-child { border-bottom: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏁 Krosskhana</h1>
            <p>Fueling Culture. Driving Stories. Partnering for Impact.</p>
        </div>
        <div class="content">
            <h2>📰 Latest News & Updates</h2>
            <p>Stay connected with the latest happenings in the Krosskhana community!</p>
            
            <div class="news-item">
                <h3>🏆 Recent Event Highlights</h3>
                <p>Our last event was a massive success with over 500 attendees! Check out the amazing photos and videos from the day.</p>
                <a href="#" class="btn">View Gallery</a>
            </div>
            
            <div class="news-item">
                <h3>🎵 Music & Entertainment</h3>
                <p>Discover the incredible artists who performed at our latest event and stay tuned for upcoming musical collaborations.</p>
                <a href="#" class="btn">Meet the Artists</a>
            </div>
            
            <div class="news-item">
                <h3>👗 Fashion & Style</h3>
                <p>From streetwear to high fashion, see how style meets speed at Krosskhana events.</p>
                <a href="#" class="btn">Fashion Highlights</a>
            </div>
            
            <div class="news-item">
                <h3>🚗 Upcoming Events</h3>
                <p>Mark your calendars! We have some exciting events planned for the coming months.</p>
                <a href="#" class="btn">View Calendar</a>
            </div>
            
            <p><strong>Stay Connected:</strong> Follow us on social media for real-time updates and behind-the-scenes content!</p>
        </div>
        <div class="footer">
            <p>&copy; 2024 Krosskhana. All rights reserved.</p>
            <p class="unsubscribe">
                <a href="{{unsubscribe_url}}">Unsubscribe</a> | 
                <a href="{{web_version_url}}">View in browser</a>
            </p>
        </div>
    </div>
</body>
</html>'
    ]
];

try {
    foreach ($templates as $template) {
        $stmt = $pdo->prepare("INSERT INTO email_templates (name, subject, content, is_default, created_by) VALUES (?, ?, ?, FALSE, ?)");
        $stmt->execute([$template['name'], $template['subject'], $template['content'], 1]);
        echo "<p style='color: green;'>✅ Added template: <strong>{$template['name']}</strong></p>";
    }
    
    echo "<h3>✅ All Templates Added Successfully!</h3>";
    echo "<p>You now have <strong>" . (count($templates) + 1) . " templates</strong> available:</p>";
    echo "<ul>";
    echo "<li>Default Newsletter Template (existing)</li>";
    foreach ($templates as $template) {
        echo "<li>{$template['name']}</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error adding templates: " . $e->getMessage() . "</p>";
}

echo "<p><a href='admin/campaigns.php'>Go to Admin Panel</a></p>";
?>
