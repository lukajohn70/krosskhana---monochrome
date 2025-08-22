<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Krosskhana Avant Garde</title>
    <meta name="description" content="Privacy Policy for Krosskhana Avant Garde - Learn how we collect, use, and protect your personal information.">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="font-poppins bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="bg-black/20 backdrop-blur-xl border-b border-white/10 p-4 fixed w-full z-50 top-0 transition-all duration-500 shadow-2xl">
        <div class="container mx-auto flex justify-between items-center py-2 px-4 md:px-0">
            <!-- Logo -->
            <div class="flex items-center space-x-4">
                <a href="index.php" class="relative group cursor-pointer">
                    <img src="assets/images/krosskhana.png" alt="Krosskhana Logo" class="h-14 w-auto transition-transform duration-300 group-hover:scale-110" />
                    <div class="absolute inset-0 bg-gradient-to-r from-white to-gray-300 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                </a>
            </div>
            
            <!-- Back to Home -->
            <a href="index.php" class="text-white hover:text-gray-300 transition-colors duration-300 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Home</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-24 pb-16">
        <div class="container mx-auto px-4 max-w-4xl">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-gray-600 to-gray-800 mb-6">
                    Privacy Policy
                </h1>
                <p class="text-lg text-gray-600 mb-4">Last updated: <?php echo date('F j, Y'); ?></p>
                <div class="w-32 h-1 bg-gradient-to-r from-gray-500 to-gray-700 mx-auto rounded-full"></div>
            </div>

            <!-- Content -->
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
                <div class="prose prose-lg max-w-none">
                    <p class="text-gray-600 mb-8">
                        At Krosskhana Avant Garde, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or use our services.
                    </p>

                    <h2 class="text-2xl font-bold text-gray-800 mb-4">1. Information We Collect</h2>
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-700 mb-3">Personal Information</h3>
                        <ul class="list-disc list-inside text-gray-600 space-y-2 mb-4">
                            <li>Name and email address (when you subscribe to our newsletter)</li>
                            <li>Contact information (when you submit a contact form)</li>
                            <li>Message content and category preferences</li>
                            <li>Company information (for corporate partnerships)</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-gray-700 mb-3">Automatically Collected Information</h3>
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>IP address and browser information</li>
                            <li>Pages visited and time spent on our website</li>
                            <li>Device information and operating system</li>
                            <li>Cookies and similar tracking technologies</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mb-4">2. How We Use Your Information</h2>
                    <div class="mb-8">
                        <p class="text-gray-600 mb-4">We use the information we collect for the following purposes:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>To provide and maintain our services</li>
                            <li>To send you newsletters and updates about our events</li>
                            <li>To respond to your inquiries and provide customer support</li>
                            <li>To improve our website and user experience</li>
                            <li>To analyze website usage and trends</li>
                            <li>To comply with legal obligations</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mb-4">3. Information Sharing and Disclosure</h2>
                    <div class="mb-8">
                        <p class="text-gray-600 mb-4">We do not sell, trade, or otherwise transfer your personal information to third parties, except in the following circumstances:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>With your explicit consent</li>
                            <li>To comply with legal requirements or court orders</li>
                            <li>To protect our rights, property, or safety</li>
                            <li>To trusted service providers who assist us in operating our website</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mb-4">4. Data Security</h2>
                    <div class="mb-8">
                        <p class="text-gray-600 mb-4">We implement appropriate security measures to protect your personal information:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li>Encryption of data in transit and at rest</li>
                            <li>Regular security assessments and updates</li>
                            <li>Access controls and authentication measures</li>
                            <li>Secure hosting and database management</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mb-4">5. Cookies and Tracking Technologies</h2>
                    <div class="mb-8">
                        <p class="text-gray-600 mb-4">We use cookies and similar technologies to enhance your browsing experience:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li><strong>Essential Cookies:</strong> Required for basic website functionality</li>
                            <li><strong>Analytics Cookies:</strong> Help us understand how visitors use our website</li>
                            <li><strong>Preference Cookies:</strong> Remember your settings and preferences</li>
                        </ul>
                        <p class="text-gray-600 mt-4">You can control cookie settings through your browser preferences.</p>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mb-4">6. Your Rights and Choices</h2>
                    <div class="mb-8">
                        <p class="text-gray-600 mb-4">You have the following rights regarding your personal information:</p>
                        <ul class="list-disc list-inside text-gray-600 space-y-2">
                            <li><strong>Access:</strong> Request a copy of your personal data</li>
                            <li><strong>Correction:</strong> Update or correct inaccurate information</li>
                            <li><strong>Deletion:</strong> Request deletion of your personal data</li>
                            <li><strong>Portability:</strong> Receive your data in a portable format</li>
                            <li><strong>Objection:</strong> Object to processing of your data</li>
                            <li><strong>Withdrawal:</strong> Withdraw consent at any time</li>
                        </ul>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mb-4">7. Data Retention</h2>
                    <div class="mb-8">
                        <p class="text-gray-600">
                            We retain your personal information only for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required or permitted by law. Newsletter subscribers' data is retained until they unsubscribe, and contact form submissions are retained for up to 3 years for customer service purposes.
                        </p>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mb-4">8. Children's Privacy</h2>
                    <div class="mb-8">
                        <p class="text-gray-600">
                            Our website is not intended for children under the age of 13. We do not knowingly collect personal information from children under 13. If you are a parent or guardian and believe your child has provided us with personal information, please contact us immediately.
                        </p>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mb-4">9. International Data Transfers</h2>
                    <div class="mb-8">
                        <p class="text-gray-600">
                            Your information may be transferred to and processed in countries other than your own. We ensure that such transfers comply with applicable data protection laws and implement appropriate safeguards to protect your information.
                        </p>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mb-4">10. Changes to This Privacy Policy</h2>
                    <div class="mb-8">
                        <p class="text-gray-600">
                            We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date. We encourage you to review this Privacy Policy periodically.
                        </p>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-800 mb-4">11. Contact Us</h2>
                    <div class="mb-8">
                        <p class="text-gray-600 mb-4">If you have any questions about this Privacy Policy or our data practices, please contact us:</p>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <p class="text-gray-600 mb-2"><strong>Email:</strong> info@krosskhana.org</p>
                            <p class="text-gray-600"><strong>Website:</strong> <a href="index.php" class="text-gray-600 hover:text-gray-700">www.krosskhana.org</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400">&copy; <?php echo date('Y'); ?> Krosskhana Avant Garde. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>
