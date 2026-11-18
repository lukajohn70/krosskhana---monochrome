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
    // Get contact inquiry statistics - check if table exists first
    try {
        $stmt = $pdo->query("SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
            COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted,
            COUNT(CASE WHEN status = 'resolved' THEN 1 END) as resolved,
            COUNT(CASE WHEN status = 'spam' THEN 1 END) as spam
            FROM contact_inquiries");
        $stats = $stmt->fetch();
    } catch (PDOException $e) {
        // Contact inquiries table doesn't exist yet, use default values
        $stats = [
            'total' => 0,
            'pending' => 0,
            'contacted' => 0,
            'resolved' => 0,
            'spam' => 0
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Inquiries - Krosskhana Admin</title>
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
    <?php $pageTitle = 'Contact Inquiries'; ?>
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
                            <i class="fas fa-envelope text-white"></i>
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

    <!-- Contact Inquiries Table -->
    <div class="bg-white/90 backdrop-blur-xl shadow-2xl rounded-2xl border border-gray-200/50 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <h3 class="text-lg font-medium text-gray-900 font-orbitron">Contact Inquiries</h3>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                    <div class="relative">
                        <input type="text" id="search" placeholder="Search by name or email..." 
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <select id="status-filter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="contacted">Contacted</option>
                        <option value="resolved">Resolved</option>
                        <option value="spam">Spam</option>
                    </select>
                    <button onclick="exportContacts()" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
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
                                Contact
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Message
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
                    <tbody id="contact-table-body-desktop" class="bg-white divide-y divide-gray-200">
                        <!-- Content will be loaded via AJAX -->
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <i class="fas fa-spinner fa-spin text-gray-400 text-2xl"></i>
                                <p class="text-gray-500 mt-2">Loading inquiries...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden" id="contact-table-body-mobile">
                <!-- Content will be loaded via AJAX -->
                <div class="p-6 text-center">
                    <i class="fas fa-spinner fa-spin text-gray-400 text-2xl"></i>
                    <p class="text-gray-500 mt-2">Loading inquiries...</p>
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
            loadContactsData();
            
            // Add event listeners
            document.getElementById('search').addEventListener('input', debounce(function() {
                currentSearch = this.value;
                currentPage = 1;
                loadContactsData();
            }, 300));
            
            document.getElementById('status-filter').addEventListener('change', function() {
                currentStatus = this.value;
                currentPage = 1;
                loadContactsData();
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

        function loadContactsData() {
            const params = new URLSearchParams({
                page: currentPage,
                search: currentSearch,
                status: currentStatus
            });

            fetch(`api/contact.php?${params}`)
                .then(async response => {
                    const text = await response.text();
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error(text || 'Invalid server response');
                    }
                })
                .then(data => {
                    if (data.status === 'success') {
                        renderContactsTable(data.data);
                        renderPagination(data.pagination);
                    } else {
                        showError(data.message || 'Error loading contacts data');
                    }
                })
                .catch(error => {
                    console.error('Error loading contacts:', error);
                    showError('Error loading contacts data: ' + error.message);
                });
        }

        function renderContactsTable(contacts) {
            const desktopTbody = document.getElementById('contact-table-body-desktop');
            const mobileContainer = document.getElementById('contact-table-body-mobile');
            
            if (!contacts || contacts.length === 0) {
                desktopTbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                            <p class="text-gray-500 mt-2">No inquiries found</p>
                        </td>
                    </tr>
                `;
                mobileContainer.innerHTML = `
                    <div class="p-6 text-center">
                        <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                        <p class="text-gray-500 mt-2">No inquiries found</p>
                    </div>
                `;
                return;
            }

            // Desktop table
            desktopTbody.innerHTML = contacts.map(contact => `
                <tr class="hover:bg-gray-50 transition-colors duration-300">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${contact.name}</div>
                        <div class="text-sm text-gray-500">${contact.email}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">${contact.subject}</div>
                        <div class="text-sm text-gray-500 truncate max-w-xs">${contact.message}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(contact.status)}">
                            ${contact.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${new Date(contact.created_at).toLocaleDateString()}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="viewContact(${contact.id})" class="text-gray-600 hover:text-gray-900 mr-3 transition-colors duration-300">
                            View
                        </button>
                        <button onclick="deleteContact(${contact.id})" class="text-gray-400 hover:text-gray-600 transition-colors duration-300">
                            Delete
                        </button>
                    </td>
                </tr>
            `).join('');

            // Mobile cards
            mobileContainer.innerHTML = contacts.map(contact => `
                <div class="p-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-900 text-sm">${contact.name}</h3>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(contact.status)}">
                            ${contact.status}
                        </span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-envelope w-4 mr-2 text-gray-400"></i>
                            <span>${contact.email}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-tag w-4 mr-2 text-gray-400"></i>
                            <span>${contact.subject}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-comment w-4 mr-2 text-gray-400"></i>
                            <span class="truncate">${contact.message}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-calendar w-4 mr-2 text-gray-400"></i>
                            <span>${new Date(contact.created_at).toLocaleDateString()}</span>
                        </div>
                    </div>
                    <div class="flex space-x-3 mt-4">
                        <button onclick="viewContact(${contact.id})" class="text-gray-600 hover:text-gray-900 text-sm transition-colors duration-300">View</button>
                        <button onclick="deleteContact(${contact.id})" class="text-gray-400 hover:text-gray-600 text-sm transition-colors duration-300">Delete</button>
                    </div>
                </div>
            `).join('');
        }

        function getStatusColor(status) {
            switch(status) {
                case 'pending': return 'bg-yellow-100 text-yellow-800';
                case 'contacted': return 'bg-blue-100 text-blue-800';
                case 'resolved': return 'bg-green-100 text-green-800';
                case 'spam': return 'bg-red-100 text-red-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        function viewContact(id) {
            // Show loading state
            showLoading('Loading contact inquiry details...');
            
            fetch(`api/contact.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.status === 'success') {
                        showContactModal(data.data);
                    } else {
                        showError('Error loading contact inquiry details');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showError('Error loading contact inquiry details');
                });
        }

        function showContactModal(contact) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4';
            modal.id = 'contact-modal';
            
            const initials = (contact.name || 'U').split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
            const dateStr = contact.created_at ? new Date(contact.created_at).toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' }) : 'N/A';

            modal.innerHTML = `
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden border border-gray-100 my-8">
                    <!-- Header Banner -->
                    <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-black px-6 py-5 text-white flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center font-bold text-lg text-white border border-white/20">
                                ${initials}
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">${contact.name || 'Anonymous Inquiry'}</h3>
                                <div class="flex items-center space-x-2 text-xs text-gray-300 mt-0.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full ${getStatusColor(contact.status)} font-medium">
                                        ${contact.status || 'pending'}
                                    </span>
                                    <span>•</span>
                                    <span>${dateStr}</span>
                                    ${contact.inquiry_type ? `<span>•</span><span class="capitalize bg-white/10 px-2 py-0.5 rounded text-white">${contact.inquiry_type}</span>` : ''}
                                </div>
                            </div>
                        </div>
                        <button onclick="closeContactModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                        <!-- Contact Action Bar -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Email Address</p>
                                    <a href="mailto:${contact.email}" class="text-sm font-semibold text-blue-600 hover:underline truncate block">
                                        ${contact.email || 'N/A'}
                                    </a>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Phone</p>
                                    ${contact.phone ? `<a href="tel:${contact.phone}" class="text-sm font-semibold text-gray-800 hover:underline">${contact.phone}</a>` : `<span class="text-sm text-gray-400">Not provided</span>`}
                                </div>
                            </div>
                        </div>

                        <!-- Subject & Inquiry Content -->
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Subject</p>
                            <h4 class="text-lg font-bold text-gray-900 pb-2">${contact.subject || 'No Subject'}</h4>
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Message</p>
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-gray-800 text-sm leading-relaxed whitespace-pre-wrap font-sans">
                                ${contact.message || 'No message provided.'}
                            </div>
                        </div>

                        <!-- Direct Reply Form -->
                        <div class="pt-2 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-bold uppercase tracking-wider text-gray-600 flex items-center gap-1.5">
                                    <i class="fas fa-reply text-gray-400"></i> Compose Reply via Email
                                </label>
                                <span class="text-xs text-gray-400">Sent directly to ${contact.email || 'recipient'}</span>
                            </div>
                            <textarea id="reply-message" rows="4" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:border-black transition-all" placeholder="Write your professional response to ${contact.name || 'the customer'}..."></textarea>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center space-x-2">
                            <label class="text-xs font-semibold text-gray-500">Status:</label>
                            <select onchange="updateContactStatusQuick(${contact.id}, this.value)" class="text-xs font-semibold rounded-lg border-gray-300 py-1.5 px-2.5 bg-white text-gray-700">
                                <option value="pending" ${contact.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="contacted" ${contact.status === 'contacted' ? 'selected' : ''}>Contacted</option>
                                <option value="resolved" ${contact.status === 'resolved' ? 'selected' : ''}>Resolved</option>
                                <option value="spam" ${contact.status === 'spam' ? 'selected' : ''}>Spam</option>
                            </select>
                        </div>

                        <div class="flex items-center space-x-2">
                            <button onclick="closeContactModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                                Close
                            </button>
                            <button onclick="sendContactReply(${contact.id}, false)" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-black transition-colors flex items-center gap-1.5">
                                <i class="fas fa-paper-plane text-xs"></i> Send Reply
                            </button>
                            <button onclick="sendContactReply(${contact.id}, true)" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors flex items-center gap-1.5">
                                <i class="fas fa-check-circle text-xs"></i> Reply & Resolve
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        function closeContactModal() {
            const modal = document.getElementById('contact-modal');
            if (modal) {
                modal.remove();
            }
        }

        function updateContactStatusQuick(id, status) {
            fetch('api/contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update_status',
                    id: id,
                    status: status
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showSuccess('Status updated to ' + status);
                    loadContactsData();
                } else {
                    showError(data.message || 'Failed to update status');
                }
            })
            .catch(err => showError('Status update failed'));
        }

        function sendContactReply(id, markResolved = false) {
            const textarea = document.getElementById('reply-message');
            const replyMessage = textarea ? textarea.value.trim() : '';

            if (!replyMessage) {
                showError('Please enter a reply message before sending.');
                return;
            }

            showLoading('Sending reply...');

            fetch('api/contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'send_reply',
                    id: id,
                    reply_message: replyMessage,
                    mark_resolved: markResolved
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();

                if (data.status === 'success') {
                    showSuccess(data.message || 'Reply sent successfully');
                    closeContactModal();
                    loadContactsData();
                } else {
                    showError(data.message || 'Failed to send reply');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showError('Failed to send reply');
            });
        }

        function deleteContact(id) {
            showDeleteModal(id, 'contact inquiry', 'contact');
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

        function exportContacts() {
            const params = new URLSearchParams({
                action: 'export',
                search: currentSearch,
                status: currentStatus
            });
            
            window.open(`api/contact.php?${params}`, '_blank');
        }

        function showSuccess(message) {
            if (typeof showToast === 'function') {
                showToast(message, 'success');
                return;
            }
            console.log('Success:', message);
        }

        function showError(message) {
            if (typeof showToast === 'function') {
                showToast(message, 'error');
                return;
            }
            console.error('Error:', message);
        }
    </script>
</body>
</html>
