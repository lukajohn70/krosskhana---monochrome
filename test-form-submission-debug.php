<?php
require_once 'includes/config.php';

echo "<h2>🔍 Testing Actual Form Submission Process</h2>";

// Simulate a POST request to process-contact.php
echo "<h3>📝 Testing Contact Form Submission</h3>";

// Create test data
$_POST = [
    'name' => 'Test User',
    'email' => 'lukajohn70@gmail.com',
    'subject' => 'Debug Test Contact Form',
    'message' => 'This is a test contact form submission to debug the notification system.',
    'phone' => '123-456-7890',
    'inquiry_type' => 'general'
];

// Capture output
ob_start();

// Include the contact form processing file
include 'process-contact.php';

$output = ob_get_clean();

echo "<div style='background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Contact Form Processing Output:</h4>";
echo "<pre style='background: white; padding: 10px; border-radius: 4px; overflow-x: auto;'>" . htmlspecialchars($output) . "</pre>";
echo "</div>";

// Reset POST data
$_POST = [];

echo "<h3>📧 Testing Newsletter Form Submission</h3>";

// Create test data for newsletter
$_POST = [
    'name' => 'Test Subscriber',
    'email' => 'lukajohn70@gmail.com'
];

// Capture output
ob_start();

// Include the newsletter form processing file
include 'process-newsletter.php';

$output = ob_get_clean();

echo "<div style='background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Newsletter Form Processing Output:</h4>";
echo "<pre style='background: white; padding: 10px; border-radius: 4px; overflow-x: auto;'>" . htmlspecialchars($output) . "</pre>";
echo "</div>";

echo "<hr>";
echo "<h3>🎯 What This Test Shows:</h3>";
echo "<ul>";
echo "<li>Whether the form processing files are working correctly</li>";
echo "<li>Whether the notification system is being called</li>";
echo "<li>What response the forms are generating</li>";
echo "<li>Any errors in the form processing</li>";
echo "</ul>";

echo "<h3>📧 Expected Results:</h3>";
echo "<ul>";
echo "<li>You should receive 2 emails from the contact form test</li>";
echo "<li>You should receive 2 emails from the newsletter test</li>";
echo "<li>The JSON response should show 'success' status</li>";
echo "</ul>";

echo "<p style='color: blue; font-weight: bold;'>💡 Check your Gmail inbox for the test emails!</p>";
?>

