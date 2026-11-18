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
    // Get newsletter statistics
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN status = 'active' THEN 1 END) as active,
        COUNT(CASE WHEN status = 'unsubscribed' THEN 1 END) as unsubscribed
        FROM newsletter_subscriptions");
    $stats = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Management - Krosskhana Admin</title>
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
<?php $pageTitle = 'Newsletter Management'; ?>
<?php include 'includes/sidebar.php'; ?>

    <!-- Toast Container -->
    <div id="toast-container" class="toast-container"></div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white/90 backdrop-blur-xl overflow-hidden shadow-2xl rounded-2xl border border-gray-200/50 hover:shadow-lg transition-all duration-300">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gradient-to-r from-gray-600 to-gray-700 rounded-md flex items-center justify-center">
                                <i class="fas fa-users text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">Total Subscribers</dt>
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
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">Active</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['active'] ?? 0; ?></dd>
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
                                <i class="fas fa-times-circle text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">Unsubscribed</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['unsubscribed'] ?? 0; ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 mb-6">
            <div class="px-6 py-4 border-b border-gray-200/50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                    <h3 class="text-lg font-medium text-gray-900 font-orbitron">Newsletter Subscribers</h3>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                        <div class="relative">
                            <input type="text" id="search" placeholder="Search by name or email..." 
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-600"></i>
                            </div>
                        </div>
                        <select id="status-filter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="unsubscribed">Unsubscribed</option>
                        </select>
                        <button onclick="exportNewsletter()" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscribers Table -->
        <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 overflow-hidden">
            <!-- Desktop Table -->
            <div class="hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subscriber
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date Subscribed
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="newsletter-table-body-desktop" class="bg-white divide-y divide-gray-200">
                        <!-- Content will be loaded via AJAX -->
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <i class="fas fa-spinner fa-spin text-gray-600 text-2xl"></i>
                                <p class="text-gray-600 mt-2">Loading subscribers...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden" id="newsletter-table-body-mobile">
                <!-- Content will be loaded via AJAX -->
                <div class="p-6 text-center">
                    <i class="fas fa-spinner fa-spin text-gray-600 text-2xl"></i>
                    <p class="text-gray-600 mt-2">Loading subscribers...</p>
                </div>
            </div>
            
            <!-- Pagination -->
            <div id="pagination" class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                <!-- Pagination will be loaded via AJAX -->
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
                    <div id="status-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-2xl rounded-2xl bg-white/95 backdrop-blur-xl border-gray-200/50">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4 font-orbitron">Update Subscription Status</h3>
                <p class="text-sm text-gray-600 mb-4">Are you sure you want to change the status of this subscriber?</p>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeStatusModal()" class="px-4 py-2 bg-gray-300 text-gray-500 rounded-lg hover:bg-gray-400 transition-colors duration-300">
                        Cancel
                    </button>
                    <button onclick="confirmStatusUpdate()" class="px-4 py-2 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let currentStatus = '';
        let currentSearch = '';
        let selectedSubscriberId = null;
        let selectedNewStatus = '';

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadNewsletterData();
            
            // Add event listeners
            document.getElementById('search').addEventListener('input', debounce(function() {
                currentSearch = this.value;
                currentPage = 1;
                loadNewsletterData();
            }, 300));
            
            document.getElementById('status-filter').addEventListener('change', function() {
                currentStatus = this.value;
                currentPage = 1;
                loadNewsletterData();
            });
        });

        function loadNewsletterData() {
            const params = new URLSearchParams({
                page: currentPage,
                search: currentSearch,
                status: currentStatus
            });

            fetch(`api/newsletter.php?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderNewsletterTable(data.data);
                        renderPagination(data.pagination);
                    } else {
                        showError('Error loading newsletter data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error loading newsletter data');
                });
        }

        function renderNewsletterTable(subscribers) {
            const desktopTbody = document.getElementById('newsletter-table-body-desktop');
            const mobileContainer = document.getElementById('newsletter-table-body-mobile');
            
            if (!subscribers || subscribers.length === 0) {
                desktopTbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <i class="fas fa-inbox text-gray-600 text-2xl"></i>
                        <p class="text-gray-600 mt-2">No subscribers found</p>
                    </td>
                </tr>
            `;
                mobileContainer.innerHTML = `
                <div class="p-6 text-center">
                    <i class="fas fa-inbox text-gray-600 text-2xl"></i>
                    <p class="text-gray-600 mt-2">No subscribers found</p>
                </div>
            `;
                return;
            }

            // Desktop table
            desktopTbody.innerHTML = subscribers.map(sub => `
                <tr class="hover:bg-gray-50 transition-colors duration-300">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${sub.name}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-600">${sub.email}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${sub.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${sub.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        ${new Date(sub.created_at).toLocaleDateString()}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="updateStatus(${sub.id}, '${sub.status === 'active' ? 'unsubscribed' : 'active'}')" 
                            class="text-gray-500 hover:text-gray-900 mr-3 transition-colors duration-300">
                            ${sub.status === 'active' ? 'Unsubscribe' : 'Activate'}
                        </button>
                        <button onclick="deleteSubscriber(${sub.id})" class="text-gray-600 hover:text-gray-500 transition-colors duration-300">
                            Delete
                        </button>
                    </td>
                </tr>
            `).join('');

            // Mobile cards - Enhanced for better mobile viewing
            mobileContainer.innerHTML = subscribers.map(sub => `
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4 overflow-hidden">
                    <!-- Header with name and status -->
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900 text-base truncate">${sub.name}</h3>
                            <span class="px-3 py-1 text-xs font-medium rounded-full ${sub.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} flex-shrink-0">
                                ${sub.status}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Content section -->
                    <div class="p-4 space-y-3">
                        <!-- Email -->
                        <div class="flex items-start">
                            <i class="fas fa-envelope w-5 h-5 mr-3 text-gray-400 mt-0.5 flex-shrink-0"></i>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-600 break-all">${sub.email}</p>
                            </div>
                        </div>
                        
                        <!-- Date -->
                        <div class="flex items-start">
                            <i class="fas fa-calendar w-5 h-5 mr-3 text-gray-400 mt-0.5 flex-shrink-0"></i>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-600">Subscribed: ${new Date(sub.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action buttons -->
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <button onclick="updateStatus(${sub.id}, '${sub.status === 'active' ? 'unsubscribed' : 'active'}')" 
                                class="flex-1 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-300 ${sub.status === 'active' ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200'}">
                                <i class="fas ${sub.status === 'active' ? 'fa-user-times' : 'fa-user-check'} mr-2"></i>
                                ${sub.status === 'active' ? 'Unsubscribe' : 'Activate'}
                            </button>
                            <button onclick="deleteSubscriber(${sub.id})" 
                                class="flex-1 px-3 py-2 text-sm font-medium bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors duration-300">
                                <i class="fas fa-trash mr-2"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function renderPagination(pagination) {
            const paginationDiv = document.getElementById('pagination');
            
            if (!pagination || pagination.total_pages <= 1) {
                paginationDiv.innerHTML = '';
                return;
            }

            let paginationHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button onclick="changePage(${pagination.current_page - 1})" 
                            ${pagination.current_page <= 1 ? 'disabled' : ''}
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white hover:bg-gray-200 ${pagination.current_page <= 1 ? 'opacity-50 cursor-not-allowed' : ''}">
                            Previous
                        </button>
                        <button onclick="changePage(${pagination.current_page + 1})" 
                            ${pagination.current_page >= pagination.total_pages ? 'disabled' : ''}
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white hover:bg-gray-200 ${pagination.current_page >= pagination.total_pages ? 'opacity-50 cursor-not-allowed' : ''}">
                            Next
                        </button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-600">
                                Showing <span class="font-medium text-gray-900">${pagination.from}</span> to <span class="font-medium text-gray-900">${pagination.to}</span> of <span class="font-medium text-gray-900">${pagination.total}</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
            `;

            // Previous button
            paginationHTML += `
                <button onclick="changePage(${pagination.current_page - 1})" 
                    ${pagination.current_page <= 1 ? 'disabled' : ''}
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-600 hover:bg-gray-200 ${pagination.current_page <= 1 ? 'opacity-50 cursor-not-allowed' : ''}">
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;

            // Page numbers
            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i === pagination.current_page) {
                    paginationHTML += `
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-200 text-sm font-medium text-gray-900">
                            ${i}
                        </span>
                    `;
                } else {
                    paginationHTML += `
                        <button onclick="changePage(${i})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-200">
                            ${i}
                        </button>
                    `;
                }
            }

            // Next button
            paginationHTML += `
                <button onclick="changePage(${pagination.current_page + 1})" 
                    ${pagination.current_page >= pagination.total_pages ? 'disabled' : ''}
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-600 hover:bg-gray-200 ${pagination.current_page >= pagination.total_pages ? 'opacity-50 cursor-not-allowed' : ''}">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;

            paginationHTML += `
                            </nav>
                        </div>
                    </div>
                </div>
            `;

            paginationDiv.innerHTML = paginationHTML;
        }

        function changePage(page) {
            currentPage = page;
            loadNewsletterData();
        }

        function updateStatus(id, newStatus) {
            selectedSubscriberId = id;
            selectedNewStatus = newStatus;
            document.getElementById('status-modal').classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('status-modal').classList.add('hidden');
            selectedSubscriberId = null;
            selectedNewStatus = '';
        }

        function confirmStatusUpdate() {
            if (!selectedSubscriberId || !selectedNewStatus) return;

            fetch('api/newsletter.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update_status',
                    id: selectedSubscriberId,
                    status: selectedNewStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    closeStatusModal();
                    loadNewsletterData();
                    showSuccess('Status updated successfully');
                } else {
                    showError(data.message || 'Error updating status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error updating status');
            });
        }

        function viewNewsletter(id) {
            // Show loading state
            showLoading('Loading subscriber details...');
            
            fetch(`api/newsletter.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showNewsletterModal(data.data);
                    } else {
                        showError('Error loading subscriber details');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showError('Error loading subscriber details');
                });
        }

        function showNewsletterModal(subscriber) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.id = 'newsletter-modal';
            
            modal.innerHTML = `
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Newsletter Subscriber Details</h3>
                            <button onclick="closeNewsletterModal()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">${subscriber.email || 'N/A'}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full ${subscriber.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${subscriber.status || 'N/A'}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created At</label>
                                    <p class="mt-1 text-sm text-gray-900">${subscriber.created_at || 'N/A'}</p>
                                </div>
                                ${subscriber.updated_at ? `
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                                    <p class="mt-1 text-sm text-gray-900">${subscriber.updated_at}</p>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                            <button onclick="closeNewsletterModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        function closeNewsletterModal() {
            const modal = document.getElementById('newsletter-modal');
            if (modal) {
                modal.remove();
            }
        }

        function deleteSubscriber(id) {
            showDeleteModal(id, 'subscriber', 'newsletter');
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

        function exportNewsletter() {
            const params = new URLSearchParams({
                action: 'export',
                search: currentSearch,
                status: currentStatus
            });

            window.open(`api/newsletter.php?${params}`, '_blank');
        }

        function showSuccess(message) {
            // You can implement a toast notification here
            alert(message);
        }

        function showError(message) {
            // You can implement a toast notification here
            alert('Error: ' + message);
        }

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
    </script>
    </div>

    <!-- Sidebar JavaScript -->
    <script src="includes/sidebar.js"></script>
</body>
</html>
