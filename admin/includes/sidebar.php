<!-- Top Navigation -->
<nav class="bg-gray-900/80 backdrop-blur-xl shadow-2xl border-b border-gray-700/50 fixed top-0 left-0 right-0 z-40">
    <div class="flex justify-between h-16 px-3 sm:px-6 lg:px-8">
        <div class="flex items-center">
            <button id="sidebar-toggle" class="text-gray-300 hover:text-white p-2 rounded-md lg:hidden">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <div class="flex-shrink-0 flex items-center ml-2 lg:ml-0">
                <img class="h-6 w-auto lg:h-8 hover:scale-110 transition-transform duration-300" src="../assets/logos/Krosskhana-02.png" alt="Krosskhana">
                <span class="ml-2 lg:ml-3 text-sm lg:text-xl font-bold text-white font-orbitron truncate max-w-32 lg:max-w-none"><?php echo $pageTitle ?? 'Admin Dashboard'; ?></span>
            </div>
        </div>
        <div class="flex items-center space-x-2 lg:space-x-4">
            <span class="text-gray-300 hidden lg:inline">Welcome, Admin</span>
            <a href="logout.php" class="bg-gradient-to-r from-white to-gray-300 hover:from-gray-200 hover:to-gray-400 text-black px-2 py-1 lg:px-4 lg:py-2 rounded-lg text-xs lg:text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-sign-out-alt mr-1 lg:mr-2"></i><span class="hidden sm:inline">Logout</span>
            </a>
        </div>
    </div>
</nav>

<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900/95 backdrop-blur-xl shadow-2xl border-r border-gray-700/50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="flex flex-col h-full">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-center h-16 border-b border-gray-700/50">
            <img class="h-8 w-auto lg:h-10" src="../assets/logos/Krosskhana-02.png" alt="Krosskhana">
        </div>
        
        <!-- Sidebar Navigation -->
        <nav class="flex-1 px-3 py-4 lg:px-4 lg:py-6 space-y-1 lg:space-y-2">
            <a href="index.php" class="flex items-center px-3 py-2 lg:px-4 lg:py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-white bg-gray-800/50 border border-gray-700/50' : ''; ?>">
                <i class="fas fa-tachometer-alt mr-2 lg:mr-3 text-sm lg:text-base"></i>
                <span class="font-medium text-sm lg:text-base">Dashboard</span>
            </a>
            
            <a href="newsletter.php" class="flex items-center px-3 py-2 lg:px-4 lg:py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg <?php echo basename($_SERVER['PHP_SELF']) == 'newsletter.php' ? 'text-white bg-gray-800/50 border border-gray-700/50' : ''; ?>">
                <i class="fas fa-newspaper mr-2 lg:mr-3 text-sm lg:text-base"></i>
                <span class="font-medium text-sm lg:text-base">Newsletter</span>
            </a>
            
            <a href="registrations.php" class="flex items-center px-3 py-2 lg:px-4 lg:py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg <?php echo basename($_SERVER['PHP_SELF']) == 'registrations.php' ? 'text-white bg-gray-800/50 border border-gray-700/50' : ''; ?>">
                <i class="fas fa-car mr-2 lg:mr-3 text-sm lg:text-base"></i>
                <span class="font-medium text-sm lg:text-base">Registrations</span>
            </a>
            
            <a href="contact.php" class="flex items-center px-3 py-2 lg:px-4 lg:py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'text-white bg-gray-800/50 border border-gray-700/50' : ''; ?>">
                <i class="fas fa-envelope mr-2 lg:mr-3 text-sm lg:text-base"></i>
                <span class="font-medium text-sm lg:text-base">Contact</span>
            </a>
            
            <a href="corporate.php" class="flex items-center px-3 py-2 lg:px-4 lg:py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg <?php echo basename($_SERVER['PHP_SELF']) == 'corporate.php' ? 'text-white bg-gray-800/50 border border-gray-700/50' : ''; ?>">
                <i class="fas fa-building mr-2 lg:mr-3 text-sm lg:text-base"></i>
                <span class="font-medium text-sm lg:text-base">Corporate</span>
            </a>
            
            <a href="sponsorship.php" class="flex items-center px-3 py-2 lg:px-4 lg:py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg <?php echo basename($_SERVER['PHP_SELF']) == 'sponsorship.php' ? 'text-white bg-gray-800/50 border border-gray-700/50' : ''; ?>">
                <i class="fas fa-handshake mr-2 lg:mr-3 text-sm lg:text-base"></i>
                <span class="font-medium text-sm lg:text-base">Sponsorship</span>
            </a>
            
            <a href="campaigns.php" class="flex items-center px-3 py-2 lg:px-4 lg:py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg <?php echo basename($_SERVER['PHP_SELF']) == 'campaigns.php' ? 'text-white bg-gray-800/50 border border-gray-700/50' : ''; ?>">
                <i class="fas fa-bullhorn mr-2 lg:mr-3 text-sm lg:text-base"></i>
                <span class="font-medium text-sm lg:text-base">Campaigns</span>
            </a>
            
            <a href="settings.php" class="flex items-center px-3 py-2 lg:px-4 lg:py-3 text-gray-300 rounded-lg transition-all duration-300 hover:bg-gray-800/50 hover:text-white hover:shadow-lg <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'text-white bg-gray-800/50 border border-gray-700/50' : ''; ?>">
                <i class="fas fa-cog mr-2 lg:mr-3 text-sm lg:text-base"></i>
                <span class="font-medium text-sm lg:text-base">Settings</span>
            </a>
        </nav>
        
        <!-- Sidebar Footer -->
        <div class="p-3 lg:p-4 border-t border-gray-700/50">
            <div class="text-center">
                <p class="text-xs text-gray-500">Krosskhana Admin</p>
                <p class="text-xs text-gray-600 mt-1">v1.0.0</p>
            </div>
        </div>
    </div>
</div>

<!-- Overlay for mobile -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden hidden"></div>

<!-- Main Content -->
<div class="lg:ml-64 pt-16 bg-gray-50 min-h-screen">
    <div class="p-6">
