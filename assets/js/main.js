// Main JavaScript for Krosskhana PHP Application

// Global variables
let currentVideoIndex = 1;
const totalVideos = 4;

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize splash screen first
    initSplashScreen();
    
    initNavigation();
    initForms();
    initAnimations();
    initTypewriter();
    initScrollEffects();
    initBackToTop();
    initEventTabs();
    initScrollBasedNavigation();
    initDropdownMenu();
    initCarousel();
});

// Navigation functionality
function initNavigation() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuBtn && mobileMenu) {
        const mobileMenuSpans = mobileMenuBtn.querySelectorAll('span');
        
        mobileMenuBtn.addEventListener('click', function() {
            const isOpen = mobileMenu.classList.contains('max-h-96');
            
            if (isOpen) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
    }
}

// Close mobile menu function
function closeMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenuSpans = mobileMenuBtn?.querySelectorAll('span');
    
    if (mobileMenu && mobileMenuBtn) {
        mobileMenu.classList.remove('max-h-96', 'opacity-100');
        mobileMenu.classList.add('max-h-0', 'opacity-0', 'overflow-hidden');
        
        // Reset hamburger menu animation
        if (mobileMenuSpans && mobileMenuSpans.length >= 3) {
            mobileMenuSpans[0].classList.remove('rotate-45', 'translate-y-1');
            mobileMenuSpans[1].classList.remove('opacity-0');
            mobileMenuSpans[2].classList.remove('-rotate-45', '-translate-y-1');
        }
    }
}

// Open mobile menu function
function openMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenuSpans = mobileMenuBtn?.querySelectorAll('span');
    
    if (mobileMenu && mobileMenuBtn) {
        mobileMenu.classList.remove('max-h-0', 'opacity-0', 'overflow-hidden');
        mobileMenu.classList.add('max-h-96', 'opacity-100');
        
        // Animate hamburger menu
        if (mobileMenuSpans && mobileMenuSpans.length >= 3) {
            mobileMenuSpans[0].classList.add('rotate-45', 'translate-y-1');
            mobileMenuSpans[1].classList.add('opacity-0');
            mobileMenuSpans[2].classList.add('-rotate-45', '-translate-y-1');
        }
    }
}

// Form handling
function initForms() {
    // News Brief Form
    const newsBriefForm = document.getElementById('news-brief-form');
    if (newsBriefForm) {
        newsBriefForm.addEventListener('submit', handleNewsBriefSubmit);
    }

    // Contact Form
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactSubmit);
    }
}

// Handle News Brief form submission
async function handleNewsBriefSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const name = formData.get('name');
    const email = formData.get('email');
    
    if (!name || !email) {
        showNotification('Please enter your name and email address.', false, 'news');
        return;
    }

    try {
        const response = await fetch('process-newsletter.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.status === 'success') {
            showNotification(data.message, true, 'news');
            e.target.reset();
        } else {
            showNotification(data.message, false, 'news');
        }
    } catch (error) {
        showNotification('Failed to subscribe. Please try again.', false, 'news');
    }
}

// Handle Contact form submission
async function handleContactSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    // Validate required fields
    const name = formData.get('name');
    const email = formData.get('email');
    const subject = formData.get('subject');
    const message = formData.get('message');
    
    if (!name || !email || !subject || !message) {
        showNotification('Please fill in all required fields.', false, 'contact');
        return;
    }

    try {
        const response = await fetch('process-contact.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.status === 'success') {
            showNotification(data.message, true, 'contact');
            e.target.reset();
        } else {
            showNotification(data.message, false, 'contact');
        }
    } catch (error) {
        showNotification('Failed to send message. Please try again.', false, 'contact');
    }
}

// Tab switching function
function switchTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
        content.classList.remove('active');
    });
    
    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active');
        button.classList.remove('bg-white/20');
        button.classList.add('text-gray-300');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById(tabName + '-content');
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
        selectedContent.classList.add('active');
    }
    
    // Add active class to selected tab button
    const selectedButton = document.getElementById(tabName + '-tab');
    if (selectedButton) {
        selectedButton.classList.add('active');
        selectedButton.classList.add('bg-white/20');
        selectedButton.classList.remove('text-gray-300');
    }
}

// Show notification modal
function showNotification(message, success, context) {
    const modal = document.getElementById('notification-modal');
    const modalIcon = document.getElementById('modal-icon');
    const modalMessage = document.getElementById('modal-message');
    const modalClose = document.getElementById('modal-close');

    if (modal && modalIcon && modalMessage && modalClose) {
        // Set icon and color based on context and success
        let iconClass, gradientClass;
        
        if (context === 'contact') {
            iconClass = success ? 'fas fa-check-circle text-white' : 'fas fa-exclamation-circle text-gray-300';
            gradientClass = success ? 'from-white to-gray-300 hover:from-gray-100 hover:to-gray-400' : 'from-gray-300 to-white hover:from-gray-400 hover:to-gray-100';
        } else {
            iconClass = success ? 'fas fa-check-circle text-white' : 'fas fa-exclamation-circle text-gray-300';
            gradientClass = success ? 'from-white to-gray-300 hover:from-gray-100 hover:to-gray-400' : 'from-gray-300 to-white hover:from-gray-400 hover:to-gray-100';
        }

        modalIcon.className = `mb-4 text-3xl ${iconClass}`;
        modalMessage.textContent = message;
        modalClose.className = `px-6 py-2 bg-gradient-to-r ${gradientClass} text-black font-bold rounded-full shadow transition`;
        
        modal.classList.remove('hidden');
        
        // Close modal functionality
        modalClose.onclick = function() {
            modal.classList.add('hidden');
        };
        
        // Close on outside click
        modal.onclick = function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        };
    }
}

// Smooth scroll to section
function scrollToSection(sectionId) {
    console.log('scrollToSection called with:', sectionId);
    
    const element = document.getElementById(sectionId);
    console.log('Element found:', element);
    
    if (element) {
        console.log('Scrolling to element:', element);
        
        // Get navbar height first
        const navbar = document.querySelector('nav');
        const navbarHeight = navbar ? navbar.offsetHeight : 80;
        console.log('Navbar height:', navbarHeight);
        
        // Check if we're on mobile
        const isMobile = window.innerWidth <= 768;
        
        // Calculate target position
        const elementTop = element.offsetTop;
        let targetPosition;
        
        if (isMobile) {
            // For mobile, adjust the offset to reduce space between navbar and content
            targetPosition = elementTop - navbarHeight + 400; // Add 320px to bring content closer
        } else {
            // For desktop, keep the original behavior unchanged
            targetPosition = elementTop - navbarHeight - 20;
        }
        
        console.log('Target position:', targetPosition, 'isMobile:', isMobile);
        
        // Scroll directly to the calculated position
        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
        
        // Update active navigation state
        updateActiveNavigation(sectionId);
        
        // Close mobile menu if it's open
        closeMobileMenu();
    } else {
        console.error('Element not found for sectionId:', sectionId);
    }
}

// Update active navigation state
function updateActiveNavigation(activeSectionId) {
    // Remove active class from all navigation links
    const allNavLinks = document.querySelectorAll('.nav-link, .mobile-nav-link');
    allNavLinks.forEach(link => {
        link.classList.remove('bg-gradient-to-r', 'from-white', 'to-gray-300', 'text-black', 'shadow-lg');
        link.classList.add('text-gray-300');
    });
    
    // Add active class to the current section's navigation link
    const activeNavLink = document.querySelector(`[onclick="scrollToSection('${activeSectionId}')"]`);
    if (activeNavLink) {
        activeNavLink.classList.remove('text-gray-300');
        activeNavLink.classList.add('bg-gradient-to-r', 'from-white', 'to-gray-300', 'text-black', 'shadow-lg');
    }
}

// Handle scroll-based navigation highlighting
function initScrollBasedNavigation() {
    const sections = ['home', 'about', 'mission', 'explore', 'events', 'team', 'highlights', 'participate', 'sponsors', 'news-brief', 'contact'];
    
    window.addEventListener('scroll', function() {
        // Get navbar height dynamically
        const navbar = document.querySelector('nav');
        const navbarHeight = navbar ? navbar.offsetHeight : 80;
        
        const scrollPosition = window.scrollY + navbarHeight + 100; // Add offset for better detection
        
        let currentSection = 'home';
        
        sections.forEach(sectionId => {
            const section = document.getElementById(sectionId);
            if (section) {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    currentSection = sectionId;
                }
            }
        });
        
        updateActiveNavigation(currentSection);
    });
}

// Scroll to top functionality
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Initialize back to top button
function initBackToTop() {
    const backToTopBtn = document.getElementById('back-to-top');
    
    if (backToTopBtn) {
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopBtn.classList.remove('hidden');
            } else {
                backToTopBtn.classList.add('hidden');
            }
        });
        
        // Click handler
        backToTopBtn.addEventListener('click', scrollToTop);
    }
}

// Initialize animations
function initAnimations() {
    // Add animation classes to elements when they come into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
            }
        });
    }, observerOptions);

    // Observe all elements with animation classes
    const animatedElements = document.querySelectorAll('.animate-fade-in-up, .animate-fade-in-down, .animate-fade-in-left, .animate-fade-in-right');
    animatedElements.forEach(el => observer.observe(el));
}

// Advanced typewriter effect for hero section (matching React version)
function initTypewriter() {
    const typewriterText = 'CARS, ARTS, MUSIC AND FASHION';
    const typewriterElement = document.getElementById('typewriter-text');
    
    if (typewriterElement) {
        let reveal = 0;
        let direction = 1; // 1 = reveal, -1 = hide
        let isPaused = false; // Add pause state
        let frame;
        let pauseTimer;
        
        function animate() {
            if (isPaused) {
                // Don't animate while paused
                frame = requestAnimationFrame(animate);
                return;
            }
            
            reveal += direction * 0.15; // Slower, smoother speed
            if (reveal > typewriterText.length) {
                // Text is fully revealed, pause for 3 seconds
                isPaused = true;
                pauseTimer = setTimeout(() => {
                    direction = -1;
                    isPaused = false;
                }, 3000); // 3 second pause
                reveal = typewriterText.length;
            } else if (reveal < 0) {
                direction = 1;
                reveal = 0;
            }
            
            typewriterElement.style.width = `${reveal}ch`;
            frame = requestAnimationFrame(animate);
        }
        
        frame = requestAnimationFrame(animate);
        
        // Cleanup function
        return () => {
            cancelAnimationFrame(frame);
            if (pauseTimer) clearTimeout(pauseTimer);
        };
    }
}

// Scroll effects for navigation highlighting
function initScrollEffects() {
    const sections = ['home', 'about', 'team', 'highlights', 'participate', 'sponsors', 'news-brief', 'contact'];
    const navLinks = document.querySelectorAll('.nav-link');
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
    
    function updateActiveSection() {
        // Get navbar height dynamically
        const navbar = document.querySelector('nav');
        const navbarHeight = navbar ? navbar.offsetHeight : 80;
        
        const scrollPosition = window.scrollY + navbarHeight + 100;
        
        for (let i = sections.length - 1; i >= 0; i--) {
            const section = document.getElementById(sections[i]);
            if (section && section.offsetTop <= scrollPosition) {
                const activeSection = sections[i];
                
                // Update desktop navigation
                navLinks.forEach(link => {
                    const targetId = link.getAttribute('onclick')?.match(/scrollToSection\('([^']+)'\)/)?.[1];
                    if (targetId === activeSection) {
                        link.classList.add('active');
                        link.classList.add('bg-gradient-to-r', 'from-white', 'to-gray-300', 'text-black', 'shadow-lg');
                        link.classList.remove('text-gray-300');
                    } else {
                        link.classList.remove('active');
                        link.classList.remove('bg-gradient-to-r', 'from-white', 'to-gray-300', 'text-black', 'shadow-lg');
                        link.classList.add('text-gray-300');
                    }
                });
                
                // Update mobile navigation
                mobileNavLinks.forEach(link => {
                    const targetId = link.getAttribute('onclick')?.match(/scrollToSection\('([^']+)'\)/)?.[1];
                    if (targetId === activeSection) {
                        link.classList.add('active');
                        link.classList.add('bg-gradient-to-r', 'from-white', 'to-gray-300', 'text-black', 'shadow-lg');
                        link.classList.remove('text-gray-300');
                    } else {
                        link.classList.remove('active');
                        link.classList.remove('bg-gradient-to-r', 'from-white', 'to-gray-300', 'text-black', 'shadow-lg');
                        link.classList.add('text-gray-300');
                    }
                });
                
                break;
            }
        }
    }
    
    // Update on scroll
    window.addEventListener('scroll', updateActiveSection);
    
    // Initial update
    updateActiveSection();
}

// Video cycling for about section (matching React version)
function cycleAboutVideo() {
    const videoElement = document.querySelector('#about video');
    if (videoElement) {
        currentVideoIndex = (currentVideoIndex % totalVideos) + 1;
        videoElement.src = `assets/videos/${currentVideoIndex}.mp4`;
        videoElement.load();
        videoElement.play();
    }
}

// Make function globally accessible
window.cycleAboutVideo = cycleAboutVideo;

// Event tabs functionality
function initEventTabs() {
    const pastEventsTab = document.getElementById('past-events-tab');
    const upcomingEventsTab = document.getElementById('upcoming-events-tab');
    const pastEventsContent = document.getElementById('past-events-content');
    const upcomingEventsContent = document.getElementById('upcoming-events-content');

    if (pastEventsTab && upcomingEventsTab && pastEventsContent && upcomingEventsContent) {
        // Debug logging
        console.log('Event tabs initialized:', {
            pastEventsTab: pastEventsTab,
            upcomingEventsTab: upcomingEventsTab,
            pastEventsContent: pastEventsContent,
            upcomingEventsContent: upcomingEventsContent
        });
        
        // Set initial state - Past Events should be active by default
        pastEventsTab.className = 'px-8 py-3 rounded-full text-black font-semibold transition-all duration-300 bg-gradient-to-r from-white to-gray-300 shadow-lg';
        upcomingEventsTab.className = 'px-8 py-3 rounded-full text-gray-300 font-semibold transition-all duration-300 hover:text-white hover:bg-white/10';
        pastEventsContent.style.display = 'block';
        upcomingEventsContent.style.display = 'none';
        
        console.log('Initial tab state set');
    } else {
        console.error('Event tab elements not found:', {
            pastEventsTab: !!pastEventsTab,
            upcomingEventsTab: !!upcomingEventsTab,
            pastEventsContent: !!pastEventsContent,
            upcomingEventsContent: !!upcomingEventsContent
        });
    }
}

// Event gallery function
function openEventGallery(eventId) {
    // This would typically open a modal or navigate to a gallery page
    // For now, we'll show a notification
    showNotification(`Opening gallery for ${eventId}...`, true, 'events');
}

// Make functions globally accessible
window.openEventGallery = openEventGallery;

// Form opening function
function openForm(formType) {
    console.log('Opening form for:', formType);
    
    switch(formType) {
        case 'corporate':
            openCorporateForm();
            break;
        case 'racer':
            openRacerForm();
            break;
        case 'sponsorship':
            openSponsorshipForm();
            break;
        default:
            showNotification('Opening form...', true, 'forms');
    }
}

// Corporate Partnership Form
function openCorporateForm() {
    const modal = document.getElementById('corporate-form-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

// Close corporate form
function closeCorporateForm() {
    const modal = document.getElementById('corporate-form-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Racer Registration Form
function openRacerForm() {
    const modal = document.getElementById('racer-form-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

// Close racer form
function closeRacerForm() {
    const modal = document.getElementById('racer-form-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Sponsorship Form
function openSponsorshipForm() {
    const modal = document.getElementById('sponsorship-form-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

// Close sponsorship form
function closeSponsorshipForm() {
    const modal = document.getElementById('sponsorship-form-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Make form function globally accessible
window.openForm = openForm;
window.closeCorporateForm = closeCorporateForm;
window.closeRacerForm = closeRacerForm;
window.closeSponsorshipForm = closeSponsorshipForm;

// Handle form submissions
document.addEventListener('DOMContentLoaded', function() {
    const corporateForm = document.getElementById('corporate-partnership-form');
    if (corporateForm) {
        corporateForm.addEventListener('submit', handleCorporateFormSubmit);
    }
    
    const racerForm = document.getElementById('racer-registration-form');
    if (racerForm) {
        racerForm.addEventListener('submit', handleRacerFormSubmit);
    }
    
    const sponsorshipForm = document.getElementById('sponsorship-form');
    if (sponsorshipForm) {
        sponsorshipForm.addEventListener('submit', handleSponsorshipFormSubmit);
    }
});

async function handleCorporateFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    // Validate required fields
    const company = formData.get('company');
    const contactPerson = formData.get('contact_person');
    const email = formData.get('email');
    const phone = formData.get('phone');
    
    if (!company || !contactPerson || !email || !phone) {
        showNotification('Please fill in all required fields.', false, 'corporate');
        return;
    }
    
    try {
        const response = await fetch('process-corporate.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.status === 'success') {
            showNotification(data.message, true, 'corporate');
            e.target.reset();
            closeCorporateForm();
        } else {
            showNotification(data.message, false, 'corporate');
        }
    } catch (error) {
        showNotification('Failed to submit form. Please try again.', false, 'corporate');
    }
}

async function handleRacerFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    // Validate required fields
    const fullName = formData.get('full_name');
    const email = formData.get('email');
    const phone = formData.get('phone');
    
    if (!fullName || !email || !phone) {
        showNotification('Please fill in all required fields.', false, 'racer');
        return;
    }
    
    try {
        const response = await fetch('process-registration.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.status === 'success') {
            showNotification(data.message, true, 'racer');
            e.target.reset();
            closeRacerForm();
        } else {
            showNotification(data.message, false, 'racer');
        }
    } catch (error) {
        showNotification('Failed to submit form. Please try again.', false, 'racer');
    }
}

async function handleSponsorshipFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    // Validate required fields
    const organization = formData.get('organization');
    const contactPerson = formData.get('contact_person');
    const email = formData.get('email');
    const phone = formData.get('phone');
    
    if (!organization || !contactPerson || !email || !phone) {
        showNotification('Please fill in all required fields.', false, 'sponsorship');
        return;
    }
    
    try {
        const response = await fetch('process-sponsorship.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.status === 'success') {
            showNotification(data.message, true, 'sponsorship');
            e.target.reset();
            closeSponsorshipForm();
        } else {
            showNotification(data.message, false, 'sponsorship');
        }
    } catch (error) {
        showNotification('Failed to submit form. Please try again.', false, 'sponsorship');
    }
}

// Initialize splash screen functionality
function initSplashScreen() {
    const splashScreen = document.getElementById('splash-screen');
    const mainApp = document.getElementById('main-app');
    const skipButton = document.getElementById('skip-splash');
    const progressBar = document.getElementById('splash-progress');
    const splashVideo = document.getElementById('splash-video');
    
    if (splashScreen && mainApp) {
        let progressInterval;
        
        // Start zoom out effect and progress bar after video loads
        if (splashVideo) {
            splashVideo.addEventListener('loadedmetadata', function() {
                const videoDuration = splashVideo.duration;
                const progressStep = 100 / (videoDuration * 20); // Update 20 times per second
                
                // Start zoom out effect
                setTimeout(() => {
                    splashVideo.style.transform = 'scale(1)';
                }, 100);
                
                // Progress bar animation based on video duration
                let progress = 0;
                progressInterval = setInterval(() => {
                    progress += progressStep;
                    if (progressBar) {
                        progressBar.style.width = Math.min(progress, 100) + '%';
                    }
                    
                    if (progress >= 100) {
                        clearInterval(progressInterval);
                        hideSplashScreen();
                    }
                }, 50); // Update every 50ms
            });
        }
        
        // Skip button functionality
        if (skipButton) {
            skipButton.addEventListener('click', function() {
                if (progressInterval) {
                    clearInterval(progressInterval);
                }
                hideSplashScreen();
            });
        }
        
        // Hide splash screen function
        function hideSplashScreen() {
            splashScreen.style.opacity = '0';
            splashScreen.style.pointerEvents = 'none';
            mainApp.classList.remove('hidden');
            
            setTimeout(() => {
                splashScreen.style.display = 'none';
            }, 1000);
        }
    }
}

// Initialize dropdown menu functionality
function initDropdownMenu() {
    const moreDropdownBtn = document.getElementById('more-dropdown-btn');
    const moreDropdown = document.getElementById('more-dropdown');
    
    if (moreDropdownBtn && moreDropdown) {
        // Toggle dropdown on click
        moreDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isVisible = moreDropdown.classList.contains('opacity-100');
            
            if (isVisible) {
                moreDropdown.classList.remove('opacity-100', 'visible');
                moreDropdown.classList.add('opacity-0', 'invisible');
            } else {
                moreDropdown.classList.remove('opacity-0', 'invisible');
                moreDropdown.classList.add('opacity-100', 'visible');
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!moreDropdownBtn.contains(e.target) && !moreDropdown.contains(e.target)) {
                moreDropdown.classList.remove('opacity-100', 'visible');
                moreDropdown.classList.add('opacity-0', 'invisible');
            }
        });
        
        // Close dropdown when pressing Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                moreDropdown.classList.remove('opacity-100', 'visible');
                moreDropdown.classList.add('opacity-0', 'invisible');
            }
        });
    }
}

// Global tab switching functions
window.switchToPastEvents = function() {
    console.log('switchToPastEvents called');
    const pastEventsTab = document.getElementById('past-events-tab');
    const upcomingEventsTab = document.getElementById('upcoming-events-tab');
    const pastEventsContent = document.getElementById('past-events-content');
    const upcomingEventsContent = document.getElementById('upcoming-events-content');
    
    if (pastEventsTab && upcomingEventsTab && pastEventsContent && upcomingEventsContent) {
        // Update tab styles
        pastEventsTab.className = 'px-8 py-3 rounded-full text-black font-semibold transition-all duration-300 bg-gradient-to-r from-white to-gray-300 shadow-lg';
        upcomingEventsTab.className = 'px-8 py-3 rounded-full text-gray-300 font-semibold transition-all duration-300 hover:text-white hover:bg-white/10';
        
        // Show/hide content
        pastEventsContent.style.display = 'block';
        upcomingEventsContent.style.display = 'none';
        
        console.log('Past events tab activated');
        console.log('Content display states:', {
            pastEventsContent: pastEventsContent.style.display,
            upcomingEventsContent: upcomingEventsContent.style.display
        });
    } else {
        console.error('Some elements not found');
    }
};

window.switchToUpcomingEvents = function() {
    console.log('switchToUpcomingEvents called');
    const pastEventsTab = document.getElementById('past-events-tab');
    const upcomingEventsTab = document.getElementById('upcoming-events-tab');
    const pastEventsContent = document.getElementById('past-events-content');
    const upcomingEventsContent = document.getElementById('upcoming-events-content');
    
    if (pastEventsTab && upcomingEventsTab && pastEventsContent && upcomingEventsContent) {
        // Update tab styles
        upcomingEventsTab.className = 'px-8 py-3 rounded-full text-black font-semibold transition-all duration-300 bg-gradient-to-r from-white to-gray-300 shadow-lg';
        pastEventsTab.className = 'px-8 py-3 rounded-full text-gray-300 font-semibold transition-all duration-300 hover:text-white hover:bg-white/10';
        
        // Show/hide content
        upcomingEventsContent.style.display = 'block';
        pastEventsContent.style.display = 'none';
        
        console.log('Upcoming events tab activated');
        console.log('Content display states:', {
            pastEventsContent: pastEventsContent.style.display,
            upcomingEventsContent: upcomingEventsContent.style.display
        });
    } else {
        console.error('Some elements not found');
    }
};

// Test if functions are defined
console.log('Functions defined:', {
    switchToPastEvents: typeof window.switchToPastEvents,
    switchToUpcomingEvents: typeof window.switchToUpcomingEvents
});

// Test function call
setTimeout(() => {
    console.log('Testing function availability after 1 second...');
    if (typeof window.switchToUpcomingEvents === 'function') {
        console.log('switchToUpcomingEvents is available and ready to use');
    } else {
        console.error('switchToUpcomingEvents is not available');
    }
}, 1000);

// Add video end event listener
document.addEventListener('DOMContentLoaded', function() {
    const aboutVideo = document.querySelector('#about video');
    if (aboutVideo) {
        aboutVideo.addEventListener('ended', cycleAboutVideo);
    }
});

// Utility functions
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

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Export functions for global access
window.scrollToSection = scrollToSection;
window.showNotification = showNotification;

// Carousel functionality
function initCarousel() {
    const carouselTrack = document.getElementById('carousel-track');
    const prevBtn = document.getElementById('carousel-prev');
    const nextBtn = document.getElementById('carousel-next');
    const indicators = document.querySelectorAll('.carousel-indicator');
    
    if (!carouselTrack || !prevBtn || !nextBtn) {
        console.log('Carousel elements not found, skipping carousel initialization');
        return;
    }
    
    let currentSlide = 0;
    const totalSlides = 2; // We have 2 slides with 3 cards each
    
    function updateCarousel() {
        const translateX = -currentSlide * 100;
        carouselTrack.style.transform = `translateX(${translateX}%)`;
        
        // Update indicators
        indicators.forEach((indicator, index) => {
            if (index === currentSlide) {
                indicator.classList.add('active', 'bg-white');
                indicator.classList.remove('bg-white/30');
            } else {
                indicator.classList.remove('active', 'bg-white');
                indicator.classList.add('bg-white/30');
            }
        });
        
        // Update button states
        prevBtn.style.opacity = currentSlide === 0 ? '0.5' : '1';
        nextBtn.style.opacity = currentSlide === totalSlides - 1 ? '0.5' : '1';
    }
    
    function nextSlide() {
        if (currentSlide < totalSlides - 1) {
            currentSlide++;
            updateCarousel();
        }
    }
    
    function prevSlide() {
        if (currentSlide > 0) {
            currentSlide--;
            updateCarousel();
        }
    }
    
    // Event listeners
    nextBtn.addEventListener('click', nextSlide);
    prevBtn.addEventListener('click', prevSlide);
    
    // Indicator clicks
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentSlide = index;
            updateCarousel();
        });
    });
    
    // Touch/swipe support
    let startX = 0;
    let endX = 0;
    
    carouselTrack.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
    });
    
    carouselTrack.addEventListener('touchend', (e) => {
        endX = e.changedTouches[0].clientX;
        handleSwipe();
    });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = startX - endX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swipe left - next slide
                nextSlide();
            } else {
                // Swipe right - previous slide
                prevSlide();
            }
        }
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        }
    });
    
    // Initialize carousel
    updateCarousel();
}
