<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Racer Registration - Krosskhana Motorsports</title>
    <meta name="description" content="Join Krosskhana racing events and showcase your skills on the track. Register now for autocross, drifting, and exhibition events.">

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
                    Racer Registration
                </h1>
                <p class="text-lg text-gray-300 mb-4">Join our racing events and showcase your skills on the track</p>
                <div class="w-32 h-1 bg-gradient-to-r from-white to-gray-300 mx-auto rounded-full"></div>
            </div>

            <!-- Form -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl p-8 md:p-12 border border-white/20">
                <form id="racer-registration-form" class="space-y-6">
                    <!-- Participation Type Selection -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-white mb-4">Participation Type Selection</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center space-x-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="participation_type" value="autocross_competitor" class="w-5 h-5 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Autocross Competitor</span>
                            </label>
                            <label class="flex items-center space-x-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="participation_type" value="drifting_challenge" class="w-5 h-5 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Drifting Challenge</span>
                            </label>
                            <label class="flex items-center space-x-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="participation_type" value="exhibition_driver" class="w-5 h-5 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Exhibition Driver</span>
                            </label>
                            <label class="flex items-center space-x-3 p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="participation_type" value="safety_training" class="w-5 h-5 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Safety Training</span>
                            </label>
                        </div>
                    </div>

                    <!-- Personal Contact Information -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-white mb-4">Personal Contact Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-200 font-medium mb-2">Full Name *</label>
                                <input type="text" name="full_name" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                            <div>
                                <label class="block text-gray-200 font-medium mb-2">Racing Alias</label>
                                <input type="text" name="racing_alias" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                            <div>
                                <label class="block text-gray-200 font-medium mb-2">Email *</label>
                                <input type="email" name="email" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                            <div>
                                <label class="block text-gray-200 font-medium mb-2">Phone *</label>
                                <input type="tel" name="phone" required class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-200 font-medium mb-2">Location</label>
                                <input type="text" name="location" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Details -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-white mb-4">Vehicle Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-200 font-medium mb-2">Make</label>
                                <input type="text" name="vehicle_make" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                            <div>
                                <label class="block text-gray-200 font-medium mb-2">Model</label>
                                <input type="text" name="vehicle_model" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                            <div>
                                <label class="block text-gray-200 font-medium mb-2">Year</label>
                                <input type="number" name="vehicle_year" min="1900" max="2030" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-200 font-medium mb-2">Modifications</label>
                            <textarea name="modifications" rows="3" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400 resize-none" placeholder="Describe any modifications to your vehicle"></textarea>
                        </div>
                    </div>

                    <!-- Racing Experience -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-white mb-4">Racing Experience</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center space-x-3 p-3 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="racing_experience" value="novice" class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Novice</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="racing_experience" value="intermediate" class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Intermediate</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="racing_experience" value="semi_pro" class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Semi-Pro</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer">
                                <input type="radio" name="racing_experience" value="professional" class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-gray-200">Professional</span>
                            </label>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="space-y-4">
                        <h3 class="text-xl font-semibold text-white mb-4">Additional Information</h3>
                        <div>
                            <label class="block text-gray-200 font-medium mb-2">Additional Information</label>
                            <textarea name="additional_info" rows="4" class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-white placeholder-gray-400 resize-none" placeholder="Any additional information about your racing experience, goals, or special requirements"></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center pt-8">
                        <button type="submit" class="px-12 py-4 bg-gradient-to-r from-white to-gray-300 text-black font-bold rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
                            Register Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md mx-4 text-center">
            <div class="text-green-500 text-6xl mb-4">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Thank You!</h3>
            <p class="text-gray-600 mb-6" id="successMessage">Your racer registration has been submitted successfully! You will be redirected to the home page in a few seconds.</p>
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

    <script>
        // Form validation and submission handling
        document.getElementById('racer-registration-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Check if at least one option is selected in each section
            const participationChecked = document.querySelector('input[name="participation_type"]:checked');
            const experienceChecked = document.querySelector('input[name="racing_experience"]:checked');

            if (!participationChecked) {
                alert('Please select at least one participation type.');
                return false;
            }

            if (!experienceChecked) {
                alert('Please select your racing experience level.');
                return false;
            }

            // Get form data
            const formData = new FormData(this);
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            submitBtn.disabled = true;

            // Submit form via fetch
            fetch('process-registration.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
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
