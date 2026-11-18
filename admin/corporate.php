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
    // Get Corporate Inquiries statistics
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
        COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted,
        COUNT(CASE WHEN status = 'resolved' THEN 1 END) as resolved,
        COUNT(CASE WHEN status = 'spam' THEN 1 END) as spam
        FROM corporate_inquiries");
    $stats = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corporate Inquiries - Krosskhana Admin</title>
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
    <?php $pageTitle = 'Corporate Inquiries'; ?>
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

    <!-- Corporate Inquiries Table -->
    <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <h3 class="text-lg font-medium text-gray-900 font-orbitron">Corporate Inquiries</h3>
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
                                Inquiry
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
                    <tbody id="corporate-table-body-desktop" class="bg-white divide-y divide-gray-200">
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
            <div class="md:hidden" id="corporate-table-body-mobile">
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

            fetch(`api/corporate.php?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderCorporateTable(data.data);
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

        function renderCorporateTable(data) {
            const desktopTbody = document.getElementById('corporate-table-body-desktop');
            const mobileContainer = document.getElementById('corporate-table-body-mobile');
            
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
            desktopTbody.innerHTML = data.map(corporate => {
                const partnerTypes = [];
                if (corporate.brand_collaboration == 1) partnerTypes.push('Brand Collab');
                if (corporate.vendor_opportunities == 1) partnerTypes.push('Vendor');
                if (corporate.media_representative == 1) partnerTypes.push('Media');
                const partnerTypeBadge = partnerTypes.length > 0 ? partnerTypes.join(', ') : 'Partnership';

                const statusColor = corporate.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                    (corporate.status === 'contacted' ? 'bg-blue-100 text-blue-800' :
                                    (corporate.status === 'partnered' || corporate.status === 'qualified' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'));

                const dateStr = corporate.created_at ? new Date(corporate.created_at).toLocaleDateString() : 'N/A';

                return `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">${corporate.company || 'N/A'}</div>
                        <div class="text-xs text-gray-500">${corporate.contact_person || 'N/A'} ${corporate.role_title ? `• ${corporate.role_title}` : ''}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${corporate.email}</div>
                        <div class="text-xs text-gray-500">${corporate.phone || ''}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 text-gray-800 border border-gray-200">
                            ${partnerTypeBadge}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${dateStr}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full ${statusColor}">
                            ${corporate.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="viewCorporate(${corporate.id})" class="text-gray-700 hover:text-black mr-3 font-semibold">
                            <i class="fas fa-eye mr-1"></i> View
                        </button>
                        <button onclick="deleteCorporate(${corporate.id})" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `}).join('');

            // Render mobile cards
            mobileContainer.innerHTML = data.map(corporate => {
                const partnerTypes = [];
                if (corporate.brand_collaboration == 1) partnerTypes.push('Brand Collab');
                if (corporate.vendor_opportunities == 1) partnerTypes.push('Vendor');
                if (corporate.media_representative == 1) partnerTypes.push('Media');
                const partnerTypeBadge = partnerTypes.length > 0 ? partnerTypes.join(', ') : 'Partnership';

                const statusColor = corporate.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                    (corporate.status === 'contacted' ? 'bg-blue-100 text-blue-800' :
                                    (corporate.status === 'partnered' || corporate.status === 'qualified' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'));

                return `
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="font-bold text-gray-900">${corporate.company || 'N/A'}</h4>
                            <p class="text-xs text-gray-500">${corporate.contact_person || ''} ${corporate.role_title ? `(${corporate.role_title})` : ''}</p>
                        </div>
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full ${statusColor}">
                            ${corporate.status}
                        </span>
                    </div>
                    <div class="space-y-1.5 text-xs text-gray-600 mb-3">
                        <div><i class="fas fa-envelope w-4 text-gray-400"></i> ${corporate.email}</div>
                        <div><i class="fas fa-phone w-4 text-gray-400"></i> ${corporate.phone}</div>
                        <div><i class="fas fa-handshake w-4 text-gray-400"></i> ${partnerTypeBadge}</div>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2 border-t border-gray-100">
                        <button onclick="viewCorporate(${corporate.id})" class="px-3 py-1.5 bg-gray-900 text-white rounded-lg text-xs font-medium">View Dossier</button>
                        <button onclick="deleteCorporate(${corporate.id})" class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium">Delete</button>
                    </div>
                </div>
            `}).join('');
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

        function viewCorporate(id) {
            // Show loading state
            showLoading('Loading corporate inquiry details...');
            
            fetch(`api/corporate.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showCorporateModal(data.data);
                    } else {
                        showError('Error loading corporate inquiry details');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showError('Error loading corporate inquiry details');
                });
        }

        function showCorporateModal(corporate) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4';
            modal.id = 'corporate-modal';

            const dateStr = corporate.created_at ? new Date(corporate.created_at).toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' }) : 'N/A';
            const statusColor = corporate.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                (corporate.status === 'contacted' ? 'bg-blue-100 text-blue-800' :
                                (corporate.status === 'partnered' || corporate.status === 'qualified' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'));

            // Partnership type items
            const partnerTypes = [];
            if (corporate.brand_collaboration == 1) partnerTypes.push({ name: 'Brand Collaboration', icon: 'fas fa-handshake' });
            if (corporate.vendor_opportunities == 1) partnerTypes.push({ name: 'Vendor Opportunities', icon: 'fas fa-store' });
            if (corporate.media_representative == 1) partnerTypes.push({ name: 'Media Representation', icon: 'fas fa-camera' });

            // Interests items
            const interests = [];
            if (corporate.event_integration == 1) interests.push('Event Integration');
            if (corporate.product_showcase == 1) interests.push('Product Showcase');
            if (corporate.brand_activation == 1) interests.push('Brand Activation');
            if (corporate.content_collaboration == 1) interests.push('Content Collaboration');
            if (corporate.rd_demonstration == 1) interests.push('R&D Demonstration');
            if (corporate.other_interest == 1) interests.push(corporate.other_interest_text ? `Other: ${corporate.other_interest_text}` : 'Other');

            modal.innerHTML = `
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden border border-gray-100 my-8">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-black px-6 py-5 text-white flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center font-bold text-lg text-white border border-white/20">
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">${corporate.company || 'Corporate Partner'}</h3>
                                <div class="flex items-center space-x-2 text-xs text-gray-300 mt-0.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full ${statusColor} font-bold">
                                        ${corporate.status || 'pending'}
                                    </span>
                                    <span>•</span>
                                    <span>Received ${dateStr}</span>
                                </div>
                            </div>
                        </div>
                        <button onclick="closeCorporateModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                        <!-- Key Contact Details -->
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Key Representative</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Contact Person</p>
                                    <p class="text-sm font-bold text-gray-900">${corporate.contact_person || 'N/A'}</p>
                                    ${corporate.role_title ? `<p class="text-xs text-gray-500 mt-0.5">${corporate.role_title}</p>` : ''}
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Email Address</p>
                                    <a href="mailto:${corporate.email}" class="text-sm font-semibold text-blue-600 hover:underline block truncate">
                                        ${corporate.email}
                                    </a>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Phone</p>
                                    <a href="tel:${corporate.phone}" class="text-sm font-semibold text-gray-900 hover:underline">
                                        ${corporate.phone || 'N/A'}
                                    </a>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Website / Social</p>
                                    ${corporate.website_social ? `
                                        <a href="${corporate.website_social.startsWith('http') ? corporate.website_social : 'https://' + corporate.website_social}" target="_blank" class="text-sm font-semibold text-blue-600 hover:underline truncate block flex items-center gap-1">
                                            <span>${corporate.website_social}</span>
                                            <i class="fas fa-external-link-alt text-xs"></i>
                                        </a>
                                    ` : '<p class="text-sm text-gray-400">None provided</p>'}
                                </div>
                            </div>
                        </div>

                        <!-- Partnership Types -->
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Partnership Type</h4>
                            <div class="flex flex-wrap gap-2">
                                ${partnerTypes.length > 0 ? partnerTypes.map(t => `
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-900 text-white text-xs font-semibold shadow-sm">
                                        <i class="${t.icon}"></i> ${t.name}
                                    </span>
                                `).join('') : '<span class="text-sm text-gray-400">No type specified</span>'}
                            </div>
                        </div>

                        <!-- Primary Interests -->
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Areas of Interest</h4>
                            <div class="flex flex-wrap gap-2">
                                ${interests.length > 0 ? interests.map(int => `
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-blue-50 text-blue-800 border border-blue-200 text-xs font-semibold">
                                        <i class="fas fa-check-circle text-blue-500 mr-1.5"></i> ${int}
                                    </span>
                                `).join('') : '<span class="text-sm text-gray-400">No interests specified</span>'}
                            </div>
                        </div>

                        <!-- Admin Notes -->
                        <div class="pt-2 border-t border-gray-100">
                            <label class="text-xs font-bold uppercase tracking-wider text-gray-600 mb-2 block">Internal Notes</label>
                            <textarea id="corp-admin-notes" rows="3" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:border-black" placeholder="Add internal team notes about this partnership discussion...">${corporate.admin_notes || ''}</textarea>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center space-x-2">
                            <label class="text-xs font-semibold text-gray-500">Status:</label>
                            <select id="corp-status-select" class="text-xs font-semibold rounded-lg border-gray-300 py-1.5 px-2.5 bg-white text-gray-700">
                                <option value="pending" ${corporate.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="contacted" ${corporate.status === 'contacted' ? 'selected' : ''}>Contacted</option>
                                <option value="qualified" ${corporate.status === 'qualified' || corporate.status === 'partnered' ? 'selected' : ''}>Qualified / Partnered</option>
                                <option value="rejected" ${corporate.status === 'rejected' || corporate.status === 'declined' ? 'selected' : ''}>Declined</option>
                            </select>
                        </div>

                        <div class="flex items-center space-x-2">
                            <a href="mailto:${corporate.email}?subject=Krosskhana%20Partnership%20Discussion" class="px-3.5 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-1.5">
                                <i class="fas fa-envelope"></i> Email Partner
                            </a>
                            <button onclick="saveCorporateStatusAndNotes(${corporate.id})" class="px-4 py-2 text-xs font-bold text-white bg-black rounded-lg hover:bg-gray-800 transition-colors flex items-center gap-1.5">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
        }

        function saveCorporateStatusAndNotes(id) {
            const status = document.getElementById('corp-status-select').value;
            const notes = document.getElementById('corp-admin-notes').value;
            
            showLoading('Saving changes...');
            fetch('api/corporate.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_status',
                    id: id,
                    status: status,
                    admin_notes: notes
                })
            })
            .then(res => res.json())
            .then(data => {
                hideLoading();
                if (data.status === 'success') {
                    showSuccess('Inquiry updated successfully');
                    closeCorporateModal();
                    loadData();
                } else {
                    showError(data.message || 'Failed to update');
                }
            })
            .catch(err => {
                hideLoading();
                showError('Failed to save changes');
            });
        }

        function closeCorporateModal() {
            const modal = document.getElementById('corporate-modal');
            if (modal) {
                modal.remove();
            }
        }

        function deleteCorporate(id) {
            showDeleteModal(id, 'corporate inquiry', 'corporate');
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
            
            window.open(`api/corporate.php?${params}`, '_blank');
        }


    </script>
</body>
</html>