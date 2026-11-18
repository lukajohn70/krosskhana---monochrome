<?php
ob_start(); // Prevent stray output from corrupting JSON
require_once '../../includes/config.php';
require_once 'includes/auth.php';
require_once '../../includes/email-templates.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    sendJSONResponse(['status' => 'error', 'message' => 'Unauthorized'], 401);
}

// Get predefined templates
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['template'])) {
        // Get specific template
        $templateKey = $_GET['template'];
        $template = getPredefinedTemplate($templateKey);
        
        if ($template) {
            // Add description and key to template
            $template['key'] = $templateKey;
            $template['description'] = getTemplateDescription($templateKey);
            
            sendJSONResponse([
                'status' => 'success',
                'template' => $template
            ]);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Template not found'], 404);
        }
    } else {
        // Get all templates
        $templates = getPredefinedCampaignTemplates();
        $templatesWithDescriptions = [];
        
        foreach ($templates as $key => $template) {
            $templatesWithDescriptions[] = [
                'key' => $key,
                'name' => $template['name'],
                'subject' => $template['subject'],
                'description' => getTemplateDescription($key)
            ];
        }
        
        sendJSONResponse([
            'status' => 'success',
            'templates' => $templatesWithDescriptions
        ]);
    }
} else {
    sendJSONResponse(['status' => 'error', 'message' => 'Invalid request method'], 405);
}

/**
 * Get template description
 */
function getTemplateDescription($templateKey) {
    $descriptions = [
        'newsletter' => 'Regular newsletter updates with news, events, and community highlights',
        'event_announcement' => 'Announce new events with details, registration info, and what to expect',
        'promotional' => 'Promote special offers, discounts, and exclusive deals for subscribers',
        'community_update' => 'Share community news, achievements, and upcoming developments',
        'seasonal' => 'Seasonal greetings and themed campaigns for holidays or special periods',
        'product_launch' => 'Announce new products, services, or features with launch details',
        'reminder' => 'Send event reminders with important details and preparation info',
        'feedback_request' => 'Request feedback after events with survey links and incentives'
    ];
    
    return $descriptions[$templateKey] ?? 'Professional email template for various campaigns';
}
?>




