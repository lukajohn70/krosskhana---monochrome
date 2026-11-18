<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corporate Partnership - Krosskhana Avant Garde</title>
    <meta name="description" content="Partner with Krosskhana for business opportunities and corporate collaborations in the automotive lifestyle industry.">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/logos/Krosskhana-05.png">
    <link rel="shortcut icon" type="image/png" href="assets/logos/Krosskhana-05.png">
    <link rel="apple-touch-icon" href="assets/logos/Krosskhana-05.png">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom Fonts -->
    <style>
        @font-face {
            font-family: 'Orbitron';
            src: url('assets/fonts/Orbitron-VariableFont_wght.ttf') format('truetype');
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }
    </style>

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
                    },
                    colors: {
                        'monochrome': {
                            50: '#fafafa',
                            100: '#f5f5f5',
                            200: '#e5e5e5',
                            300: '#d4d4d4',
                            400: '#a3a3a3',
                            500: '#737373',
                            600: '#525252',
                            700: '#404040',
                            800: '#262626',
                            900: '#171717',
                            950: '#0a0a0a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="font-poppins bg-gradient-to-br from-gray-900 via-black to-gray-900 min-h-screen">
    <!-- Navigation Bar -->
    <nav class="bg-black/20 backdrop-blur-xl border-b border-white/10 p-4 fixed w-full z-[9999] top-0 transition-all duration-500 shadow-2xl">
        <div class="container mx-auto flex justify-between items-center py-2 px-4 md:px-0">
            <!-- Logo -->
            <div class="flex items-center space-x-4">
                <a href="index.php" class="relative group cursor-pointer">
                    <img src="assets/logos/Krosskhana-05.png" alt="Krosskhana Logo" class="h-14 w-auto transition-transform duration-300 group-hover:scale-110" />
                    <div class="absolute inset-0 bg-gradient-to-r from-white to-gray-300 rounded-lg opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                </a>
            </div>

            <!-- Back to Home -->
            <a href="index.php" class="bg-white/20 backdrop-blur-sm text-white hover:text-gray-300 hover:bg-white/30 transition-all duration-300 flex items-center gap-2 px-4 py-2 rounded-lg border border-white/20">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Home</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="pt-32 pb-16">
        <div class="container mx-auto px-4 max-w-4xl">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-300 mb-6">
                    Corporate Partnership
                </h1>
                <p class="text-lg text-gray-300 mb-4">Partner with Krosskhana for business opportunities and corporate collaborations</p>
                <div class="w-32 h-1 bg-gradient-to-r from-white to-gray-300 mx-auto rounded-full"></div>
            </div>

            <!-- Form -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl p-8 md:p-12 border border-white/20">
                <form id="corporate-partnership-form" class="space-y-6">
                    <!-- Initial Selection -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-white mb-4">Initial Selection</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="flex items-center space-x-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="partnership_type" value="brand_collaboration" class="w-5 h-5 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Brand Collaboration</span>
                            </label>
                            <label class="flex items-center space-x-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="partnership_type" value="vendor_opportunities" class="w-5 h-5 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Vendor Opportunities</span>
                            </label>
                            <label class="flex items-center space-x-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="partnership_type" value="media_representative" class="w-5 h-5 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Media Representative</span>
                            </label>
                        </div>
                    </div>

                    <!-- Company/Contact Information -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-white mb-4">Company/Contact Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-200 font-medium mb-2">Company/Organization *</label>
                                <input type="text" name="company" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                            <div>
                                <label class="block text-gray-200 font-medium mb-2">Contact Person *</label>
                                <input type="text" name="contact_person" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                            <div>
                                <label class="block text-gray-200 font-medium mb-2">Role/Title *</label>
                                <input type="text" name="role_title" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                            <div>
                                <label class="block text-gray-200 font-medium mb-2">Email *</label>
                                <input type="email" name="email" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                            <div>
                                <label class="block text-gray-200 font-medium mb-2">Phone *</label>
                                <input type="tel" name="phone" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                            <div>
                                <label class="block text-gray-200 font-medium mb-2">Website/Social</label>
                                <input type="url" name="website_social" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                        </div>
                    </div>

                    <!-- Primary Interest -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-white mb-4">Primary Interest</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center space-x-3 p-3 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="primary_interest" value="event_integration" class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Event Integration</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="primary_interest" value="product_showcase" class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Product Showcase</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="primary_interest" value="brand_activation" class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Brand Activation</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="primary_interest" value="content_collaboration" class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Content Collaboration</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="primary_interest" value="rd_demonstration" class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">R&D Demonstration</span>
                            </label>
                            <div class="col-span-full flex items-center space-x-3 p-3 bg-white/5 rounded-lg border border-white/10">
                                <input type="radio" name="primary_interest" value="other" class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Other:</span>
                                <input type="text" name="other_interest_text" class="flex-1 px-3 py-2 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent text-white placeholder-gray-400" placeholder="Please specify">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center pt-8">
                        <button type="submit" class="px-12 py-4 bg-gradient-to-r from-white to-gray-300 text-black font-bold rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
                            Connect with Krosskhana
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="relative py-8 md:py-12 lg:py-16 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-black to-gray-900"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-white/5 via-transparent to-gray-300/5"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center">
                <img src="assets/logos/Krosskhana-05.png" alt="Krosskhana Logo" class="h-16 w-auto mx-auto mb-6">
                <p class="text-gray-400 mb-4">Where automotive culture meets artistic expression. Experience the revolution.</p>
                <div class="flex justify-center space-x-6 text-gray-400">
                    <a href="index.php" class="bg-white/20 backdrop-blur-sm text-white hover:text-gray-300 hover:bg-white/30 transition-all duration-300 flex items-center gap-2 px-4 py-2 rounded-lg border border-white/20">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Home</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md mx-4 text-center">
            <div class="text-green-500 text-6xl mb-4">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Thank You!</h3>
            <p class="text-gray-600 mb-6" id="successMessage">Your corporate partnership inquiry has been submitted successfully! You will be redirected to the home page in a few seconds.</p>
            <button onclick="closeSuccessModal()" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-300">
                Go to Home Now
            </button>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md mx-4 text-center">
            <div class="text-red-500 text-6xl mb-4">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Oops!</h3>
            <p class="text-gray-600 mb-6" id="errorMessage">Something went wrong. Please try again.</p>
            <button onclick="closeErrorModal()" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-300">
                Try Again
            </button>
        </div>
    </div>

    <script>
        console.log('JavaScript loaded for corporate partnership form');
        
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up form listener');
            
            const form = document.getElementById('corporate-partnership-form');
            if (form) {
                console.log('Form found, adding event listener');
                
                // Form validation and submission handling
                form.addEventListener('submit', function(e) {
                    console.log('Form submission intercepted!');
                    e.preventDefault();
                    console.log('Default prevented');
                    
                    // Check Initial Selection (partnership_type radio buttons)
                    const initialSelection = document.querySelector('input[name="partnership_type"]:checked');
                    console.log('Initial selection:', initialSelection);
                    
                    // Check Primary Interest (primary_interest radio buttons)
                    const primaryInterest = document.querySelector('input[name="primary_interest"]:checked');
                    console.log('Primary interest:', primaryInterest);

                    if (!initialSelection || !primaryInterest) {
                        alert('Please select at least one option from Initial Selection and Primary Interest sections.');
                        return false;
                    }

                    // Get form data
                    const formData = new FormData(this);
                    
                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                    submitBtn.disabled = true;

                    console.log('Submitting form via fetch...');
                    // Submit form via fetch
                    fetch('forms/process-corporate.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        console.log('Response received:', response);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data parsed:', data);
                        if (data.status === 'success') {
                            // Show success message briefly, then redirect to home page
                            document.getElementById('successMessage').textContent = data.message;
                            document.getElementById('successModal').classList.remove('hidden');
                            document.getElementById('successModal').classList.add('flex');
                            
                            // Reset form
                            this.reset();
                            
                            // Redirect to home page after 2 seconds
                            setTimeout(() => {
                                window.location.href = 'index.php';
                            }, 2000);
                        } else {
                            // Show error modal
                            document.getElementById('errorMessage').textContent = data.message || 'Something went wrong. Please try again.';
                            document.getElementById('errorModal').classList.remove('hidden');
                            document.getElementById('errorModal').classList.add('flex');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('errorMessage').textContent = 'An error occurred. Please try again.';
                        document.getElementById('errorModal').classList.remove('hidden');
                        document.getElementById('errorModal').classList.add('flex');
                    })
                    .finally(() => {
                        // Reset button state
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
                });
            } else {
                console.error('Form not found!');
            }
        });

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
            document.getElementById('successModal').classList.remove('flex');
            // Redirect to home page immediately when modal is closed
            window.location.href = 'index.php';
        }

        function closeErrorModal() {
            document.getElementById('errorModal').classList.add('hidden');
            document.getElementById('errorModal').classList.remove('flex');
        }
    </script>
</body>
</html>
