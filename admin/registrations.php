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
    // Get registration statistics
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
        COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
        COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected,
        COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted
        FROM racer_registrations");
    $stats = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Racer Registrations - Krosskhana Admin</title>
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
    <?php $pageTitle = 'Racer Registrations'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Toast Container -->
    <div id="toast-container" class="toast-container"></div>
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
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
                                <dt class="text-sm font-medium text-gray-600 truncate">Total Registrations</dt>
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
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-600 truncate">Approved</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['approved'] ?? 0; ?></dd>
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
                                <dt class="text-sm font-medium text-gray-600 truncate">Rejected</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo $stats['rejected'] ?? 0; ?></dd>
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
        </div>

        <!-- Filters and Search -->
        <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 mb-6">
            <div class="px-6 py-4 border-b border-gray-200/50">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                    <h3 class="text-lg font-medium text-gray-900 font-orbitron">Racer Registrations</h3>
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
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="contacted">Contacted</option>
                        </select>
                        <button onclick="exportRegistrations()" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registrations Table -->
        <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 overflow-hidden">
            <!-- Desktop Table -->
            <div class="hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Racer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact Info
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Experience
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date Applied
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="registrations-table-body-desktop" class="bg-white divide-y divide-gray-200">
                        <!-- Content will be loaded via AJAX -->
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-spinner fa-spin text-gray-600 text-2xl"></i>
                                <p class="text-gray-600 mt-2">Loading registrations...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden" id="registrations-table-body-mobile">
                <!-- Content will be loaded via AJAX -->
                <div class="p-6 text-center">
                    <i class="fas fa-spinner fa-spin text-gray-600 text-2xl"></i>
                    <p class="text-gray-600 mt-2">Loading registrations...</p>
                </div>
            </div>
            
            <!-- Pagination -->
            <div id="pagination" class="bg-gray-50 px-4 py-3 border-t border-gray-700 sm:px-6">
                <!-- Pagination will be loaded via AJAX -->
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
               <div id="status-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-2xl rounded-2xl bg-white/95 backdrop-blur-xl border-gray-200/50">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4 font-orbitron">Update Registration Status</h3>
                <p class="text-sm text-gray-600 mb-4">Are you sure you want to change the status of this registration?</p>
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
        let selectedRegistrationId = null;
        let selectedNewStatus = '';

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadRegistrationsData();
            
            // Add event listeners
            document.getElementById('search').addEventListener('input', debounce(function() {
                currentSearch = this.value;
                currentPage = 1;
                loadRegistrationsData();
            }, 300));
            
            document.getElementById('status-filter').addEventListener('change', function() {
                currentStatus = this.value;
                currentPage = 1;
                loadRegistrationsData();
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

        function loadRegistrationsData() {
            const params = new URLSearchParams({
                page: currentPage,
                search: currentSearch,
                status: currentStatus
            });

            fetch(`api/registrations.php?${params}`, { headers: { 'Accept': 'application/json' } })
                .then(async (response) => {
                    if (!response.ok) {
                        if (response.status === 401) {
                            // Not authenticated: redirect to login
                            window.location.href = 'login.php';
                            return Promise.reject(new Error('Unauthorized'));
                        }
                        const text = await response.text();
                        console.error('Non-OK response:', response.status, text);
                        throw new Error('Request failed');
                    }
                    const contentType = response.headers.get('content-type') || '';
                    if (!contentType.includes('application/json')) {
                        const text = await response.text();
                        console.error('Expected JSON, got:', contentType, text);
                        throw new Error('Invalid response type');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.status === 'success') {
                        renderRegistrationsTable(data.data);
                        renderPagination(data.pagination);
                    } else {
                        console.error('API error payload:', data);
                        showError(data?.message || 'Error loading registrations data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error loading registrations data');
                });
        }

        function renderRegistrationsTable(registrations) {
            const desktopTbody = document.getElementById('registrations-table-body-desktop');
            const mobileContainer = document.getElementById('registrations-table-body-mobile');
            
            if (!registrations || registrations.length === 0) {
                desktopTbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-gray-600 text-2xl"></i>
                            <p class="text-gray-600 mt-2">No registrations found</p>
                        </td>
                    </tr>
                `;
                mobileContainer.innerHTML = `
                    <div class="p-6 text-center">
                        <i class="fas fa-inbox text-gray-600 text-2xl"></i>
                        <p class="text-gray-600 mt-2">No registrations found</p>
                    </div>
                `;
                return;
            }

            // Desktop table
            desktopTbody.innerHTML = registrations.map(reg => `
                <tr class="hover:bg-gray-50 transition-colors duration-300">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${reg.full_name}</div>
                        <div class="text-sm text-gray-600">${reg.racing_alias || 'No alias'}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${reg.email}</div>
                        <div class="text-sm text-gray-600">${reg.phone}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${getExperienceLevel(reg)}</div>
                        <div class="text-sm text-gray-600">${reg.vehicle_make} ${reg.vehicle_model}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(reg.status)}">
                            ${reg.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        ${new Date(reg.created_at).toLocaleDateString()}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="viewRegistration(${reg.id})" class="text-gray-500 hover:text-gray-900 mr-3 transition-colors duration-300">
                            View
                        </button>
                        <button onclick="updateStatus(${reg.id})" class="text-gray-500 hover:text-gray-900 mr-3 transition-colors duration-300">
                            Update
                        </button>
                        <button onclick="deleteRegistration(${reg.id})" class="text-gray-600 hover:text-gray-500 transition-colors duration-300">
                            Delete
                        </button>
                    </td>
                </tr>
            `).join('');

            // Mobile cards - Enhanced for better mobile viewing
            mobileContainer.innerHTML = registrations.map(reg => `
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4 overflow-hidden">
                    <!-- Header with name and status -->
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-gray-900 text-base truncate">${reg.full_name}</h3>
                                ${reg.racing_alias ? `<p class="text-sm text-gray-600 truncate">${reg.racing_alias}</p>` : ''}
                            </div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full ${getStatusColor(reg.status)} flex-shrink-0 ml-3">
                                ${reg.status}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Content section -->
                    <div class="p-4 space-y-3">
                        <!-- Contact Info -->
                        <div class="flex items-start">
                            <i class="fas fa-envelope w-5 h-5 mr-3 text-gray-400 mt-0.5 flex-shrink-0"></i>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-600 break-all">${reg.email}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <i class="fas fa-phone w-5 h-5 mr-3 text-gray-400 mt-0.5 flex-shrink-0"></i>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-600">${reg.phone}</p>
                            </div>
                        </div>
                        
                        <!-- Experience Level -->
                        <div class="flex items-start">
                            <i class="fas fa-trophy w-5 h-5 mr-3 text-gray-400 mt-0.5 flex-shrink-0"></i>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-600">${getExperienceLevel(reg)}</p>
                            </div>
                        </div>
                        
                        <!-- Vehicle Info -->
                        ${reg.vehicle_make || reg.vehicle_model ? `
                        <div class="flex items-start">
                            <i class="fas fa-car w-5 h-5 mr-3 text-gray-400 mt-0.5 flex-shrink-0"></i>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-600">${reg.vehicle_make || ''} ${reg.vehicle_model || ''}</p>
                            </div>
                        </div>
                        ` : ''}
                        
                        <!-- Date -->
                        <div class="flex items-start">
                            <i class="fas fa-calendar w-5 h-5 mr-3 text-gray-400 mt-0.5 flex-shrink-0"></i>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-gray-600">Registered: ${new Date(reg.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action buttons -->
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <button onclick="viewRegistration(${reg.id})" 
                                class="flex-1 px-3 py-2 text-sm font-medium bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors duration-300">
                                <i class="fas fa-eye mr-2"></i>
                                View
                            </button>
                            <button onclick="updateStatus(${reg.id})" 
                                class="flex-1 px-3 py-2 text-sm font-medium bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors duration-300">
                                <i class="fas fa-edit mr-2"></i>
                                Update
                            </button>
                            <button onclick="deleteRegistration(${reg.id})" 
                                class="flex-1 px-3 py-2 text-sm font-medium bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors duration-300">
                                <i class="fas fa-trash mr-2"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function getExperienceLevel(reg) {
            if (reg.professional) return 'Professional';
            if (reg.semi_pro) return 'Semi-Pro';
            if (reg.intermediate) return 'Intermediate';
            if (reg.novice) return 'Novice';
            return 'Not specified';
        }

        function getStatusColor(status) {
            switch(status) {
                case 'pending': return 'bg-gray-300 text-gray-500';
                case 'approved': return 'bg-gray-200 text-gray-500';
                case 'rejected': return 'bg-gray-500 text-gray-600';
                case 'contacted': return 'bg-gray-200 text-gray-500';
                default: return 'bg-gray-300 text-gray-600';
            }
        }

        function renderPagination(pagination) {
            const paginationContainer = document.getElementById('pagination');
            if (!pagination || !paginationContainer) return;

            const current_page = Number(pagination.current_page || 1);
            const total_pages = Number(pagination.total_pages || 1);
            const per_page = Number(pagination.per_page || 10);
            const total_records = Number((pagination.total !== undefined ? pagination.total : pagination.total_records) || 0);
            const startRecord = total_records > 0 ? ((current_page - 1) * per_page + 1) : 0;
            const endRecord = total_records > 0 ? Math.min(current_page * per_page, total_records) : 0;

            let paginationHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        ${current_page > 1 ? 
                            `<button onclick="goToPage(${current_page - 1})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </button>` : 
                            `<button disabled class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-100 cursor-not-allowed">
                                Previous
                            </button>`
                        }
                        ${current_page < total_pages ? 
                            `<button onclick="goToPage(${current_page + 1})" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </button>` : 
                            `<button disabled class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-100 cursor-not-allowed">
                                Next
                            </button>`
                        }
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">${startRecord}</span> to <span class="font-medium">${endRecord}</span> of <span class="font-medium">${total_records}</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            `;

            // Previous button
            if (current_page > 1) {
                paginationHTML += `
                    <button onclick="goToPage(${current_page - 1})" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                `;
            } else {
                paginationHTML += `
                    <button disabled class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                `;
            }

            // Page numbers
            const startPage = Math.max(1, current_page - 2);
            const endPage = Math.min(total_pages, current_page + 2);

            for (let i = startPage; i <= endPage; i++) {
                if (i === current_page) {
                    paginationHTML += `
                        <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-600 text-sm font-medium text-white">
                            ${i}
                        </button>
                    `;
                } else {
                    paginationHTML += `
                        <button onclick="goToPage(${i})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            ${i}
                        </button>
                    `;
                }
            }

            // Next button
            if (current_page < total_pages) {
                paginationHTML += `
                    <button onclick="goToPage(${current_page + 1})" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                `;
            } else {
                paginationHTML += `
                    <button disabled class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                `;
            }

            paginationHTML += `
                            </nav>
                        </div>
                    </div>
                </div>
            `;

            paginationContainer.innerHTML = paginationHTML;
        }

        function goToPage(page) {
            currentPage = page;
            loadRegistrationsData();
        }

        function viewRegistration(id) {
            window.open(`registration-details.php?id=${id}`, '_blank');
        }

        function updateStatus(id) {
            selectedRegistrationId = id;
            document.getElementById('status-modal').classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('status-modal').classList.add('hidden');
            selectedRegistrationId = null;
        }

        function confirmStatusUpdate() {
            // Implementation for status update
            closeStatusModal();
        }

        function deleteRegistration(id) {
            showDeleteModal(id, 'registration', 'registrations');
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

        function exportRegistrations() {
            const params = new URLSearchParams({
                action: 'export',
                search: currentSearch,
                status: currentStatus
            });
            
            window.open(`api/registrations.php?${params}`, '_blank');
        }

        function showSuccess(message) {
            showToast(message, 'success');
        }

        function showError(message) {
            showToast(message, 'error');
        }
    </script>
    </div>

    <!-- Sidebar JavaScript -->
    <script src="includes/sidebar.js"></script>
</body>
</html>
