<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Krosskhana Avant Garde</title>
    <meta name="description" content="Thank you for your submission to Krosskhana. We'll be in touch soon.">

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
        <div class="container mx-auto px-4 max-w-2xl">
            <!-- Thank You Message -->
            <div class="text-center">
                <div class="mb-8">
                    <i class="fas fa-check-circle text-8xl text-green-400 mb-6"></i>
                </div>

                <h1 class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-300 mb-6">
                    Thank You!
                </h1>

                <p class="text-xl text-gray-300 mb-8">
                    Your submission has been received successfully. We'll review your information and get back to you soon.
                </p>

                <div class="w-32 h-1 bg-gradient-to-r from-white to-gray-300 mx-auto rounded-full mb-8"></div>

                <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20">
                    <h2 class="text-2xl font-bold text-white mb-4">What happens next?</h2>
                    <div class="text-left text-gray-300 space-y-3">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-clock text-green-400 mt-1"></i>
                            <span>We'll review your submission within 24-48 hours</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-envelope text-green-400 mt-1"></i>
                            <span>You'll receive a confirmation email shortly</span>
                        </div>
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-phone text-green-400 mt-1"></i>
                            <span>Our team will contact you to discuss next steps</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 space-y-4">
                    <a href="index.php" class="inline-block px-8 py-4 bg-gradient-to-r from-white to-gray-300 text-black font-bold rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300">
                        Return to Home
                    </a>

                    <div class="text-gray-400 text-sm">
                        <p>Have questions? Contact us at <a href="mailto:info@krosskhana.org" class="text-white hover:text-gray-300">info@krosskhana.org</a></p>
                    </div>
                </div>
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
</body>
</html>
