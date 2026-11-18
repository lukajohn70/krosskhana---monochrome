<?php
require_once '../includes/config.php';
require_once 'includes/auth.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pdo = getDBConnection();
$stats = [];

if ($pdo) {
    // Get Sponsorship Inquiries statistics
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
        COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted,
        COUNT(CASE WHEN status = 'resolved' THEN 1 END) as resolved,
        COUNT(CASE WHEN status = 'spam' THEN 1 END) as spam
        FROM sponsorship_inquiries");
    $stats = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsorship Inquiries - Krosskhana Admin</title>
    <link rel="icon" type="image/png" href="../assets/logos/Krosskhana-05.png">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Toast Notifications CSS -->
    <link rel="stylesheet" href="assets/notifications.css">
    
    <!-- Toast Notifications JS -->
    <script src="assets/toast-notifications.js"></script>
    
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
                        'gray': {
                            400: '#9ca3af',
                            500: '#6b7280',
                            600: '#4b5563',
                            700: '#374151',
                            800: '#1f2937',
                            900: '#111827',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Custom Fonts -->
    <style>
        @font-face {
            font-family: 'Orbitron';
            src: url('../assets/fonts/Orbitron-VariableFont_wght.ttf') format('truetype');
            font-weight: 100 900;
            font-style: normal;
        }
        
        @font-face {
            font-family: 'Orbitron';
            src: url('../assets/fonts/Orbitron-Regular.ttf') format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        
        @font-face {
            font-family: 'Orbitron';
            src: url('../assets/fonts/Orbitron-Bold.ttf') format('truetype');
            font-weight: 700;
            font-style: normal;
        }
        
        @font-face {
            font-family: 'Orbitron';
            src: url('../assets/fonts/Orbitron-Black.ttf') format('truetype');
            font-weight: 900;
            font-style: normal;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-black to-gray-900 min-h-screen font-poppins">
    <?php $pageTitle = 'Sponsorship Inquiries'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Toast Container -->
    <div id="toast-container" class="toast-container"></div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                                                            <i class="fas fa-building text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 truncate">Total Inquiries</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo $stats['total'] ?? 0; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                                                            <i class="fas fa-clock text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 truncate">Pending</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo $stats['pending'] ?? 0; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                                                            <i class="fas fa-phone text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 truncate">Contacted</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo $stats['contacted'] ?? 0; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                                                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 truncate">Resolved</dt>
                            <dd class="text-lg font-medium text-gray-900"><?php echo $stats['resolved'] ?? 0; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sponsorship Inquiries Table -->
    <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <h3 class="text-lg font-medium text-gray-900 font-orbitron">Sponsorship Inquiries</h3>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                    <div class="relative">
                        <input type="text" id="search" placeholder="Search..." 
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-600"></i>
                        </div>
                    </div>
                    <select id="status-filter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="contacted">Contacted</option>
                        <option value="resolved">Resolved</option>
                        <option value="spam">Spam</option>
                    </select>
                    <button onclick="exportData()" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>
        </div>
        
            <!-- Desktop Table -->
            <div class="hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Company
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Proposal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="sponsorship-table-body-desktop" class="bg-white divide-y divide-gray-200">
                        <!-- Content will be loaded via AJAX -->
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-spinner fa-spin text-gray-600 text-2xl"></i>
                                <p class="text-gray-500 mt-2">Loading data...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden" id="sponsorship-table-body-mobile">
                <!-- Content will be loaded via AJAX -->
                <div class="p-6 text-center">
                    <i class="fas fa-spinner fa-spin text-gray-600 text-2xl"></i>
                    <p class="text-gray-500 mt-2">Loading data...</p>
                </div>
            </div>
        
        <!-- Pagination -->
        <div id="pagination" class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
            <!-- Pagination will be loaded via AJAX -->
        </div>
    </div>
    </div>

    <!-- Sidebar JavaScript -->
    <script src="includes/sidebar.js"></script>
    
    <script>
        let currentPage = 1;
        let currentStatus = '';
        let currentSearch = '';

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
            
            // Add event listeners
            document.getElementById('search').addEventListener('input', debounce(function() {
                currentSearch = this.value;
                currentPage = 1;
                loadData();
            }, 300));
            
            document.getElementById('status-filter').addEventListener('change', function() {
                currentStatus = this.value;
                currentPage = 1;
                loadData();
            });
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function loadData() {
            const params = new URLSearchParams({
                page: currentPage,
                search: currentSearch,
                status: currentStatus
            });

            fetch(`api/sponsorship.php?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderSponsorshipTable(data.data);
                        renderPagination(data.pagination);
                    } else {
                        showError('Error loading data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error loading data');
                });
        }

        function renderSponsorshipTable(data) {
            const desktopTbody = document.getElementById('sponsorship-table-body-desktop');
            const mobileContainer = document.getElementById('sponsorship-table-body-mobile');
            
            if (data.length === 0) {
                desktopTbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-gray-600 text-2xl"></i>
                            <p class="text-gray-500 mt-2">No data found</p>
                        </td>
                    </tr>
                `;
                mobileContainer.innerHTML = `
                    <div class="p-6 text-center">
                        <i class="fas fa-inbox text-gray-600 text-2xl"></i>
                        <p class="text-gray-500 mt-2">No data found</p>
                    </div>
                `;
                return;
            }

            // Render desktop table
            desktopTbody.innerHTML = data.map(sponsorship => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${sponsorship.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${sponsorship.email}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${sponsorship.phone}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${sponsorship.company}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${sponsorship.sponsorship_type}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${sponsorship.created_at}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${sponsorship.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : sponsorship.status === 'contacted' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                            ${sponsorship.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="viewSponsorship(${sponsorship.id})" class="text-gray-600 hover:text-gray-900 mr-2">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="deleteSponsorship(${sponsorship.id})" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');

            // Render mobile cards
            mobileContainer.innerHTML = data.map(sponsorship => `
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-user text-gray-600"></i>
                            <span class="font-medium text-gray-900">${sponsorship.name}</span>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full ${sponsorship.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : sponsorship.status === 'contacted' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                            ${sponsorship.status}
                        </span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-envelope text-gray-500"></i>
                            <span>${sponsorship.email}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-phone text-gray-500"></i>
                            <span>${sponsorship.phone}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-building text-gray-500"></i>
                            <span>${sponsorship.company}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-handshake text-gray-500"></i>
                            <span>${sponsorship.sponsorship_type}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-calendar text-gray-500"></i>
                            <span>${sponsorship.created_at}</span>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2 mt-3 pt-3 border-t border-gray-100">
                        <button onclick="viewSponsorship(${sponsorship.id})" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="deleteSponsorship(${sponsorship.id})" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function renderPagination(pagination) {
            const paginationContainer = document.getElementById('pagination');
            if (!pagination || pagination.total_pages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }

            let paginationHTML = '<div class="flex items-center justify-between">';
            paginationHTML += '<div class="flex-1 flex justify-between sm:hidden">';
            
            if (pagination.current_page > 1) {
                paginationHTML += `<button onclick="changePage(${pagination.current_page - 1})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</button>`;
            }
            if (pagination.current_page < pagination.total_pages) {
                paginationHTML += `<button onclick="changePage(${pagination.current_page + 1})" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</button>`;
            }
            
            paginationHTML += '</div>';
            paginationHTML += '<div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">';
            paginationHTML += `<div class="text-sm text-gray-700">Showing <span class="font-medium">${pagination.from}</span> to <span class="font-medium">${pagination.to}</span> of <span class="font-medium">${pagination.total}</span> results</div>`;
            paginationHTML += '<div>';
            paginationHTML += '<nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">';
            
            // Previous button
            if (pagination.current_page > 1) {
                paginationHTML += `<button onclick="changePage(${pagination.current_page - 1})" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Previous</button>`;
            }
            
            // Page numbers
            for (let i = Math.max(1, pagination.current_page - 2); i <= Math.min(pagination.total_pages, pagination.current_page + 2); i++) {
                paginationHTML += `<button onclick="changePage(${i})" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium ${i === pagination.current_page ? 'z-10 bg-gray-50 border-gray-500 text-gray-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'}">${i}</button>`;
            }
            
            // Next button
            if (pagination.current_page < pagination.total_pages) {
                paginationHTML += `<button onclick="changePage(${pagination.current_page + 1})" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">Next</button>`;
            }
            
            paginationHTML += '</nav>';
            paginationHTML += '</div>';
            paginationHTML += '</div>';
            paginationHTML += '</div>';
            
            paginationContainer.innerHTML = paginationHTML;
        }

        function changePage(page) {
            currentPage = page;
            loadData();
        }

        function viewSponsorship(id) {
            // Show loading state
            showLoading('Loading sponsorship inquiry details...');
            
            fetch(`api/sponsorship.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showSponsorshipModal(data.data);
                    } else {
                        showError('Error loading sponsorship inquiry details');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showError('Error loading sponsorship inquiry details');
                });
        }

        function showSponsorshipModal(sponsorship) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.id = 'sponsorship-modal';
            
            modal.innerHTML = `
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Sponsorship Inquiry Details</h3>
                            <button onclick="closeSponsorshipModal()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <p class="mt-1 text-sm text-gray-900">${sponsorship.name || 'N/A'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">${sponsorship.email || 'N/A'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900">${sponsorship.phone || 'N/A'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Company</label>
                                    <p class="mt-1 text-sm text-gray-900">${sponsorship.company || 'N/A'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Sponsorship Type</label>
                                    <p class="mt-1 text-sm text-gray-900">${sponsorship.sponsorship_type || 'N/A'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${sponsorship.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : sponsorship.status === 'contacted' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                                        ${sponsorship.status || 'N/A'}
                                    </span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Message</label>
                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">${sponsorship.message || 'N/A'}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Created At</label>
                                <p class="mt-1 text-sm text-gray-900">${sponsorship.created_at || 'N/A'}</p>
                            </div>
                            
                            ${sponsorship.updated_at ? `
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                                <p class="mt-1 text-sm text-gray-900">${sponsorship.updated_at}</p>
                            </div>
                            ` : ''}
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                            <button onclick="closeSponsorshipModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        function closeSponsorshipModal() {
            const modal = document.getElementById('sponsorship-modal');
            if (modal) {
                modal.remove();
            }
        }

        function deleteSponsorship(id) {
            showDeleteModal(id, 'sponsorship inquiry', 'sponsorship');
        }

        function showDeleteModal(id, itemType, pageType) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.id = 'delete-modal';
            
            modal.innerHTML = `
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-4">
                            <i class="fas fa-exclamation-triangle text-gray-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Confirm Deletion</h3>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to delete this ${itemType}? This action cannot be undone.
                            </p>
                        </div>
                        <div class="flex justify-center space-x-3 mt-6">
                            <button onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Cancel
                            </button>
                            <button onclick="confirmDelete(${id}, '${pageType}')" class="px-4 py-2 text-sm font-medium text-white bg-gray-600 border border-transparent rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            if (modal) {
                modal.remove();
            }
        }

        function confirmDelete(id, pageType) {
            closeDeleteModal();
            
            // Show loading state
            showLoading(`Deleting ${pageType}...`);
            
            const apiEndpoints = {
                'corporate': 'api/corporate.php',
                'sponsorship': 'api/sponsorship.php',
                'newsletter': 'api/newsletter.php',
                'contact': 'api/contact.php',
                'registrations': 'api/registrations.php'
            };
            
            const successMessages = {
                'corporate': 'Corporate inquiry deleted successfully',
                'sponsorship': 'Sponsorship inquiry deleted successfully',
                'newsletter': 'Subscriber deleted successfully',
                'contact': 'Contact inquiry deleted successfully',
                'registrations': 'Registration deleted successfully'
            };
            
            const loadFunctions = {
                'corporate': 'loadData',
                'sponsorship': 'loadData',
                'newsletter': 'loadNewsletterData',
                'contact': 'loadContactsData',
                'registrations': 'loadRegistrationsData'
            };
            
            fetch(apiEndpoints[pageType], {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'delete',
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.status === 'success') {
                    showSuccess(successMessages[pageType]);
                    // Call the appropriate load function
                    if (typeof window[loadFunctions[pageType]] === 'function') {
                        window[loadFunctions[pageType]]();
                    }
                } else {
                    showError(data.message || `Error deleting ${pageType}`);
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showError(`Error deleting ${pageType}`);
            });
        }

        function showLoading(message) {
            // Create or update loading overlay
            let loadingOverlay = document.getElementById('loading-overlay');
            if (!loadingOverlay) {
                loadingOverlay = document.createElement('div');
                loadingOverlay.id = 'loading-overlay';
                loadingOverlay.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50';
                document.body.appendChild(loadingOverlay);
            }
            
            loadingOverlay.innerHTML = `
                <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                    <i class="fas fa-spinner fa-spin text-gray-600 text-xl"></i>
                    <span class="text-gray-700">${message}</span>
                </div>
            `;
            loadingOverlay.style.display = 'flex';
        }

        function hideLoading() {
            const loadingOverlay = document.getElementById('loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        }

        function exportData() {
            const params = new URLSearchParams({
                action: 'export',
                search: currentSearch,
                status: currentStatus
            });
            
            window.open(`api/sponsorship.php?${params}`, '_blank');
        }

        function showSuccess(message) {
            console.log('Success:', message);
        }

        function showError(message) {
            console.error('Error:', message);
        }
    </script>
</body>
</html>