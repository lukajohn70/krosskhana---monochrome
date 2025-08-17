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
                mobileMenu.classList.remove('max-h-96', 'opacity-100');
                mobileMenu.classList.add('max-h-0', 'opacity-0', 'overflow-hidden');
            } else {
                mobileMenu.classList.remove('max-h-0', 'opacity-0', 'overflow-hidden');
                mobileMenu.classList.add('max-h-96', 'opacity-100');
            }

            // Animate hamburger menu
            if (mobileMenuSpans.length >= 3) {
                mobileMenuSpans[0].classList.toggle('rotate-45', !isOpen);
                mobileMenuSpans[0].classList.toggle('translate-y-1', !isOpen);
                mobileMenuSpans[1].classList.toggle('opacity-0', !isOpen);
                mobileMenuSpans[2].classList.toggle('-rotate-45', !isOpen);
                mobileMenuSpans[2].classList.toggle('-translate-y-1', !isOpen);
            }
        });
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
        const response = await fetch('api/subscribe.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ name: name, email: email })
        });

        const data = await response.json();
        
        if (response.ok) {
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
    const data = {
        name: formData.get('name'),
        email: formData.get('email'),
        category: formData.get('category'),
        message: formData.get('message')
    };
    
    if (!data.name || !data.email || !data.category || !data.message) {
        showNotification('Please fill in all fields.', false, 'contact');
        return;
    }

    try {
        const response = await fetch('api/contact.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        
        if (response.ok) {
            showNotification(result.message, true, 'contact');
            e.target.reset();
        } else {
            showNotification(result.message, false, 'contact');
        }
    } catch (error) {
        showNotification('Failed to send message. Please try again.', false, 'contact');
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
            iconClass = success ? 'fas fa-check-circle text-blue-500' : 'fas fa-exclamation-circle text-purple-500';
            gradientClass = success ? 'from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600' : 'from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600';
        } else {
            iconClass = success ? 'fas fa-check-circle text-green-500' : 'fas fa-exclamation-circle text-red-500';
            gradientClass = success ? 'from-green-500 to-teal-500 hover:from-green-600 hover:to-teal-600' : 'from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600';
        }

        modalIcon.className = `mb-4 text-3xl ${iconClass}`;
        modalMessage.textContent = message;
        modalClose.className = `px-6 py-2 bg-gradient-to-r ${gradientClass} text-white font-bold rounded-full shadow transition`;
        
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
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        // Update active navigation state
        updateActiveNavigation(sectionId);
    }
}

// Update active navigation state
function updateActiveNavigation(activeSectionId) {
    // Remove active class from all navigation links
    const allNavLinks = document.querySelectorAll('.nav-link, .mobile-nav-link');
    allNavLinks.forEach(link => {
        link.classList.remove('bg-gradient-to-r', 'from-red-500', 'to-orange-500', 'text-white', 'shadow-lg');
        link.classList.add('text-gray-300');
    });
    
    // Add active class to the current section's navigation link
    const activeNavLink = document.querySelector(`[onclick="scrollToSection('${activeSectionId}')"]`);
    if (activeNavLink) {
        activeNavLink.classList.remove('text-gray-300');
        activeNavLink.classList.add('bg-gradient-to-r', 'from-red-500', 'to-orange-500', 'text-white', 'shadow-lg');
    }
}

// Handle scroll-based navigation highlighting
function initScrollBasedNavigation() {
    const sections = ['home', 'about', 'mission', 'explore', 'events', 'team', 'highlights', 'participate', 'sponsors', 'news-brief'];
    const navHeight = 80; // Approximate navigation height
    
    window.addEventListener('scroll', function() {
        const scrollPosition = window.scrollY + navHeight + 100; // Add offset for better detection
        
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
        const scrollPosition = window.scrollY + 100;
        
        for (let i = sections.length - 1; i >= 0; i--) {
            const section = document.getElementById(sections[i]);
            if (section && section.offsetTop <= scrollPosition) {
                const activeSection = sections[i];
                
                // Update desktop navigation
                navLinks.forEach(link => {
                    const targetId = link.getAttribute('onclick')?.match(/scrollToSection\('([^']+)'\)/)?.[1];
                    if (targetId === activeSection) {
                        link.classList.add('active');
                        link.classList.add('text-red-400', 'bg-red-500/20', 'border', 'border-red-500/30');
                        link.classList.remove('text-white/80', 'hover:text-white', 'hover:bg-white/10');
                    } else {
                        link.classList.remove('active');
                        link.classList.remove('text-red-400', 'bg-red-500/20', 'border', 'border-red-500/30');
                        link.classList.add('text-white/80', 'hover:text-white', 'hover:bg-white/10');
                    }
                });
                
                // Update mobile navigation
                mobileNavLinks.forEach(link => {
                    const targetId = link.getAttribute('onclick')?.match(/scrollToSection\('([^']+)'\)/)?.[1];
                    if (targetId === activeSection) {
                        link.classList.add('active');
                        link.classList.add('text-red-400', 'bg-red-500/20', 'border', 'border-red-500/30');
                        link.classList.remove('text-white/80', 'hover:text-white', 'hover:bg-white/10');
                    } else {
                        link.classList.remove('active');
                        link.classList.remove('text-red-400', 'bg-red-500/20', 'border', 'border-red-500/30');
                        link.classList.add('text-white/80', 'hover:text-white', 'hover:bg-white/10');
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
        pastEventsTab.className = 'px-8 py-3 rounded-full text-white font-semibold transition-all duration-300 bg-gradient-to-r from-red-500 to-orange-500 shadow-lg';
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
    const data = {
        // Initial Selection
        brand_collaboration: formData.get('brand_collaboration') === 'on',
        vendor_opportunities: formData.get('vendor_opportunities') === 'on',
        media_representative: formData.get('media_representative') === 'on',
        
        // Company/Contact Information
        company: formData.get('company'),
        contact_person: formData.get('contact_person'),
        role_title: formData.get('role_title'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        website_social: formData.get('website_social'),
        
        // Primary Interest
        event_integration: formData.get('event_integration') === 'on',
        product_showcase: formData.get('product_showcase') === 'on',
        brand_activation: formData.get('brand_activation') === 'on',
        content_collaboration: formData.get('content_collaboration') === 'on',
        rd_demonstration: formData.get('rd_demonstration') === 'on',
        other_interest: formData.get('other_interest') === 'on',
        other_interest_text: formData.get('other_interest_text')
    };
    
    // Validate required fields
    if (!data.company || !data.contact_person || !data.email || !data.phone) {
        showNotification('Please fill in all required fields.', false, 'corporate');
        return;
    }
    
    try {
        // For now, show success message (you can add actual API call here)
        showNotification('Thank you! Your corporate partnership inquiry has been submitted successfully. We will contact you soon.', true, 'corporate');
        e.target.reset();
        closeCorporateForm();
    } catch (error) {
        showNotification('Failed to submit form. Please try again.', false, 'corporate');
    }
}

async function handleRacerFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = {
        // Participation Type
        autocross_competitor: formData.get('autocross_competitor') === 'on',
        drifting_challenge: formData.get('drifting_challenge') === 'on',
        exhibition_driver: formData.get('exhibition_driver') === 'on',
        safety_training: formData.get('safety_training') === 'on',
        
        // Personal Contact Information
        full_name: formData.get('full_name'),
        racing_alias: formData.get('racing_alias'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        location: formData.get('location'),
        
        // Vehicle Details
        make: formData.get('make'),
        model: formData.get('model'),
        year: formData.get('year'),
        modifications: formData.get('modifications'),
        
        // Racing Experience
        novice: formData.get('novice') === 'on',
        intermediate: formData.get('intermediate') === 'on',
        semi_pro: formData.get('semi_pro') === 'on',
        professional: formData.get('professional') === 'on',
        
        // Previous Series
        previous_series: formData.get('previous_series')
    };
    
    // Validate required fields
    if (!data.full_name || !data.email || !data.phone) {
        showNotification('Please fill in all required fields.', false, 'racer');
        return;
    }
    
    try {
        // For now, show success message (you can add actual API call here)
        showNotification('Thank you! Your racer registration has been submitted successfully. We will contact you soon.', true, 'racer');
        e.target.reset();
        closeRacerForm();
    } catch (error) {
        showNotification('Failed to submit form. Please try again.', false, 'racer');
    }
}

async function handleSponsorshipFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = {
        // Sponsorship Type Selection
        title_sponsorship: formData.get('title_sponsorship') === 'on',
        event_series_sponsor: formData.get('event_series_sponsor') === 'on',
        innovation_partner: formData.get('innovation_partner') === 'on',
        community_platform_sponsor: formData.get('community_platform_sponsor') === 'on',
        safety_initiative_partner: formData.get('safety_initiative_partner') === 'on',
        
        // Contact Information
        organization: formData.get('organization'),
        contact_person: formData.get('contact_person'),
        title: formData.get('title'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        website: formData.get('website'),
        
        // Previous Motorsport Sponsorship Experience
        none_experience: formData.get('none_experience') === 'on',
        local_experience: formData.get('local_experience') === 'on',
        national_experience: formData.get('national_experience') === 'on',
        international_experience: formData.get('international_experience') === 'on',
        
        // Target Activation Type
        track_branding: formData.get('track_branding') === 'on',
        digital_integration: formData.get('digital_integration') === 'on',
        vip_experiences: formData.get('vip_experiences') === 'on',
        product_placement: formData.get('product_placement') === 'on',
        tech_showcase: formData.get('tech_showcase') === 'on',
        safety_campaign: formData.get('safety_campaign') === 'on',
        
        // Investment Range
        investment_5k_15k: formData.get('investment_5k_15k') === 'on',
        investment_15k_50k: formData.get('investment_15k_50k') === 'on',
        investment_50k_100k: formData.get('investment_50k_100k') === 'on',
        investment_100k_plus: formData.get('investment_100k_plus') === 'on'
    };
    
    // Validate required fields
    if (!data.organization || !data.contact_person || !data.email || !data.phone) {
        showNotification('Please fill in all required fields.', false, 'sponsorship');
        return;
    }
    
    try {
        // For now, show success message (you can add actual API call here)
        showNotification('Thank you! Your sponsorship inquiry has been submitted successfully. We will contact you soon.', true, 'sponsorship');
        e.target.reset();
        closeSponsorshipForm();
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
        pastEventsTab.className = 'px-8 py-3 rounded-full text-white font-semibold transition-all duration-300 bg-gradient-to-r from-red-500 to-orange-500 shadow-lg';
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
        upcomingEventsTab.className = 'px-8 py-3 rounded-full text-white font-semibold transition-all duration-300 bg-gradient-to-r from-red-500 to-orange-500 shadow-lg';
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
