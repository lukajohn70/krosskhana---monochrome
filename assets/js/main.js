// Main JavaScript for Krosskhana PHP Application

// Global variables
let currentVideoIndex = 1;
const totalVideos = 4;

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Navbar positioning handled by CSS
    ensureNavbarVisibility();
    
    // Initially hide navbar behind splash screen
    const navbar = document.querySelector('nav');
    if (navbar) {
        navbar.style.opacity = '0';
        navbar.style.visibility = 'hidden';
    }
    
    // Initialize splash screen first
    initSplashScreen();
    
    // Set initial navigation state to home
    setTimeout(() => {
        updateActiveNavigation('home');
    }, 100);
    
    // Add function to reset splash screen for testing (can be removed in production)
    window.resetSplashScreen = function() {
        localStorage.removeItem('krosskhana_splash_seen');
        location.reload();
    };
    
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
    
    // Navbar is already fixed via CSS
    
    // Ensure navbar stays positioned on window resize
    window.addEventListener('resize', ensureNavbarVisibility);
    
    // Continuously ensure navbar is visible during scrolling
    window.addEventListener('scroll', ensureNavbarVisibility);
});



// Navbar positioning is now handled entirely by CSS

// Additional navbar positioning insurance
function ensureNavbarVisibility() {
    const navbar = document.querySelector('nav');
    if (navbar) {
        // Force fixed positioning via JavaScript as backup
        navbar.style.position = 'fixed';
        navbar.style.top = '0';
        navbar.style.left = '0';
        navbar.style.right = '0';
        navbar.style.width = '100%';
        navbar.style.zIndex = '9999';
        navbar.style.display = 'block';
        navbar.style.transform = 'none';
        
        // Ensure it's above all other content
        navbar.style.zIndex = '9999';
        
        // Force background and backdrop filter
        navbar.style.background = 'rgba(0, 0, 0, 0.8)';
        navbar.style.backdropFilter = 'blur(20px)';
        navbar.style.webkitBackdropFilter = 'blur(20px)';
        
        // Prevent any hiding
        navbar.style.clip = 'auto';
        navbar.style.clipPath = 'none';
        navbar.style.overflow = 'visible';
        

    }
}

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
        const response = await fetch('forms/process-newsletter.php', {
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
        const response = await fetch('forms/process-contact.php', {
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
    const element = document.getElementById(sectionId);
    
    if (element) {
        // Get navbar height first
        const navbar = document.querySelector('nav');
        const navbarHeight = navbar ? navbar.offsetHeight : 80;
        
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
        
        // Scroll directly to the calculated position
        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
        
        // Update active navigation state
        updateActiveNavigation(sectionId);
        
        // Close mobile menu if it's open
        closeMobileMenu();
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
        
        // If we're at the very top of the page, always highlight home
        if (window.scrollY < 100) {
            currentSection = 'home';
        } else {
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
        }
        
        updateActiveNavigation(currentSection);
    });
}

// Scroll to top functionality
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
    
    // Ensure home navigation is highlighted when scrolling to top
    setTimeout(() => {
        updateActiveNavigation('home');
    }, 500);
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

        
        // Set initial state - Past Events should be active by default
        pastEventsTab.className = 'px-8 py-3 rounded-full text-black font-semibold transition-all duration-300 bg-gradient-to-r from-white to-gray-300 shadow-lg';
        upcomingEventsTab.className = 'px-8 py-3 rounded-full text-gray-300 font-semibold transition-all duration-300 hover:text-white hover:bg-white/10';
        pastEventsContent.style.display = 'block';
        upcomingEventsContent.style.display = 'none';
        

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













// Initialize splash screen functionality
function initSplashScreen() {
    const splashScreen = document.getElementById('splash-screen');
    const mainApp = document.getElementById('main-app');
    const skipButton = document.getElementById('skip-splash');
    const progressBar = document.getElementById('splash-progress');
    const splashVideo = document.getElementById('splash-video');
    
    if (splashScreen && mainApp) {
        // Check if user has seen the splash screen before
        // Uses localStorage key: 'krosskhana_splash_seen'
        const hasSeenSplash = localStorage.getItem('krosskhana_splash_seen');
        
        if (hasSeenSplash) {
            // User has seen splash before, skip directly to main content
            console.log('Splash screen skipped - user has seen it before');
            skipSplashAndShowMain();
            return;
        }
        
        let progressInterval;
        
        console.log('Showing splash screen - first time visit');
        
        // Fade in splash screen on page load with enhanced animation
        setTimeout(() => {
            splashScreen.style.opacity = '1';
            splashScreen.classList.add('animate-splash-fade-in');
        }, 100);
        
        // Staggered fade in for splash screen elements
        setTimeout(() => {
            if (skipButton) {
                skipButton.style.opacity = '1';
                skipButton.style.transform = 'translateY(0)';
            }
        }, 800);
        
        setTimeout(() => {
            const progressContainer = document.querySelector('#splash-screen .absolute.bottom-8');
            if (progressContainer) {
                progressContainer.style.opacity = '1';
                progressContainer.style.transform = 'translateY(0)';
            }
        }, 1200);
        
        // Start zoom out effect and progress bar after video loads
        if (splashVideo) {
            splashVideo.addEventListener('loadedmetadata', function() {
                const videoDuration = splashVideo.duration;
                
                // Start zoom out effect
                setTimeout(() => {
                    splashVideo.style.transform = 'scale(1)';
                }, 100);
                
                // Real-time progress bar sync with video timeline
                const updateProgress = () => {
                    if (splashVideo && progressBar) {
                        const currentTime = splashVideo.currentTime;
                        const progress = (currentTime / videoDuration) * 100;
                        
                        // Update progress bar
                        progressBar.style.width = Math.min(progress, 100) + '%';
                        
                        // Start homepage overlay 5 seconds before video ends
                        if (currentTime >= videoDuration - 5 && !mainApp.classList.contains('overlay-active')) {
                            startHomepageOverlay();
                        }
                        
                        // Show overlay indicator when approaching the 5-second mark
                        if (currentTime >= videoDuration - 6) {
                            const overlayIndicator = document.getElementById('overlay-indicator');
                            if (overlayIndicator) {
                                overlayIndicator.style.opacity = '1';
                            }
                        }
                        
                        // Check if video has ended
                        if (currentTime >= videoDuration) {
                            clearInterval(progressInterval);
                            completeTransition();
                        }
                    }
                };
                
                // Update progress bar every 50ms for very smooth animation
                progressInterval = setInterval(updateProgress, 50);
                
                // Also listen for video end event as backup
                splashVideo.addEventListener('ended', function() {
                    clearInterval(progressInterval);
                    completeTransition();
                });
            });
        }
        
        // Skip button functionality
        if (skipButton) {
            skipButton.addEventListener('click', function() {
                if (progressInterval) {
                    clearInterval(progressInterval);
                }
                // Skip to complete transition immediately
                skipToComplete();
            });
        }
        
        // Function to skip splash and show main content directly
        function skipSplashAndShowMain() {
            // Mark splash as seen in localStorage
            localStorage.setItem('krosskhana_splash_seen', 'true');
            
            // Hide splash screen immediately
            if (splashScreen) {
                splashScreen.style.display = 'none';
            }
            
            // Show main app immediately
            if (mainApp) {
                mainApp.classList.remove('hidden');
                mainApp.style.opacity = '1';
                mainApp.style.transform = 'translateY(0) scale(1)';
                mainApp.style.zIndex = '10';
                mainApp.style.pointerEvents = 'auto';
                mainApp.classList.remove('overlay-active');
            }
            
            // Show navbar immediately
            const navbar = document.querySelector('nav');
            if (navbar) {
                navbar.style.opacity = '1';
                navbar.style.visibility = 'visible';
                navbar.style.zIndex = '9999';
                navbar.classList.add('splash-cleared');
            }
            
            // Set home as active navigation
            updateActiveNavigation('home');
        }
        
        // Start homepage overlay 5 seconds before video ends
        function startHomepageOverlay() {
            // Mark overlay as active to prevent multiple calls
            mainApp.classList.add('overlay-active');
            
            // Show main app as overlay with very low opacity
            mainApp.classList.remove('hidden');
            mainApp.style.opacity = '0.05'; // Start almost invisible
            mainApp.style.transform = 'translateY(0) scale(1)';
            mainApp.style.zIndex = '40'; // Below splash screen but above background
            
            // Gradually increase opacity over 5 seconds
            let opacity = 0.05;
            const opacityStep = 0.95 / 50; // 50 steps over 5 seconds (100ms each)
            
            const overlayInterval = setInterval(() => {
                opacity += opacityStep;
                if (mainApp) {
                    mainApp.style.opacity = Math.min(opacity, 1);
                }
                
                if (opacity >= 1) {
                    clearInterval(overlayInterval);
                }
            }, 100);
            
            // Set home as the active navigation item
            updateActiveNavigation('home');
            
            // Start showing the navbar as the overlay begins
            const navbar = document.querySelector('nav');
            if (navbar) {
                navbar.style.opacity = '0.1'; // Start very faint
                navbar.style.visibility = 'visible';
                
                // Gradually increase navbar opacity
                let navbarOpacity = 0.1;
                const navbarOpacityStep = 0.9 / 50; // 50 steps over 5 seconds
                
                const navbarInterval = setInterval(() => {
                    navbarOpacity += navbarOpacityStep;
                    if (navbar) {
                        navbar.style.opacity = Math.min(navbarOpacity, 1);
                        if (navbarOpacity >= 1) {
                            navbar.classList.add('splash-cleared');
                        }
                    }
                    
                    if (navbarOpacity >= 1) {
                        clearInterval(navbarInterval);
                    }
                }, 100);
            }
        }
        
        // Complete the transition when video ends
        function completeTransition() {
            // Mark splash as seen in localStorage
            localStorage.setItem('krosskhana_splash_seen', 'true');
            
            // Ensure main app is fully visible and interactive
            if (mainApp) {
                mainApp.style.opacity = '1';
                mainApp.style.zIndex = '10'; // Normal z-index
                mainApp.style.pointerEvents = 'auto'; // Re-enable interactions
                mainApp.classList.remove('overlay-active'); // Remove overlay positioning
            }
            
            // Ensure navbar is fully visible
            const navbar = document.querySelector('nav');
            if (navbar) {
                navbar.style.opacity = '1';
                navbar.style.visibility = 'visible';
                navbar.style.zIndex = '9999';
                navbar.classList.add('splash-cleared');
            }
            
            // Fade out splash screen
            splashScreen.classList.remove('animate-splash-fade-in');
            splashScreen.classList.add('animate-splash-fade-out');
            splashScreen.style.opacity = '0';
            splashScreen.style.pointerEvents = 'none';
            
            // Hide splash screen completely after fade out
            setTimeout(() => {
                splashScreen.style.display = 'none';
            }, 1200);
        }
        
        // Skip to complete transition immediately
        function skipToComplete() {
            // Mark splash as seen in localStorage
            localStorage.setItem('krosskhana_splash_seen', 'true');
            
            // Clear any progress intervals
            if (progressInterval) {
                clearInterval(progressInterval);
            }
            
            // Ensure main app is visible and properly positioned
            if (mainApp) {
                mainApp.classList.remove('hidden');
                mainApp.style.opacity = '1';
                mainApp.style.transform = 'translateY(0) scale(1)';
                mainApp.style.zIndex = '10'; // Normal z-index
                mainApp.style.pointerEvents = 'auto'; // Re-enable interactions
                mainApp.classList.remove('overlay-active'); // Remove overlay positioning
            }
            
            // Set home as the active navigation item
            updateActiveNavigation('home');
            
            // Ensure navbar is fully visible immediately when skipping
            const navbar = document.querySelector('nav');
            if (navbar) {
                navbar.style.opacity = '1';
                navbar.style.visibility = 'visible';
                navbar.style.zIndex = '9999';
                navbar.classList.add('splash-cleared');
            }
            
            // Fade out splash screen immediately
            splashScreen.classList.remove('animate-splash-fade-in');
            splashScreen.classList.add('animate-splash-fade-out');
            splashScreen.style.opacity = '0';
            splashScreen.style.pointerEvents = 'none';
            
            // Hide splash screen completely after fade out
            setTimeout(() => {
                splashScreen.style.display = 'none';
            }, 800); // Faster transition for skip
        }
        
        // Legacy function for skip button (now calls complete transition)
        function hideSplashScreen() {
            if (progressInterval) {
                clearInterval(progressInterval);
            }
            completeTransition();
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
    const pastEventsTab = document.getElementById('past-events-tab');
    const upcomingEventsTab = document.getElementById('upcoming-events-tab');
    const pastEventsContent = document.getElementById('past-events-content');
    const upcomingEventsContent = document.getElementById('upcoming-events-content');
    
    if (pastEventsTab && upcomingEventsTab && pastEventsContent && upcomingEventsContent) {
        // Update tab styles
        pastEventsTab.className = 'px-3 md:px-8 py-1.5 md:py-3 rounded-full text-black font-semibold transition-all duration-300 bg-gradient-to-r from-white to-gray-300 shadow-lg text-xs md:text-base whitespace-nowrap';
        upcomingEventsTab.className = 'px-3 md:px-8 py-1.5 md:py-3 rounded-full text-gray-300 font-semibold transition-all duration-300 hover:text-white hover:bg-white/10 text-xs md:text-base whitespace-nowrap';
        
        // Show/hide content
        pastEventsContent.style.display = 'block';
        upcomingEventsContent.style.display = 'none';
    }
};

window.switchToUpcomingEvents = function() {
    const pastEventsTab = document.getElementById('past-events-tab');
    const upcomingEventsTab = document.getElementById('upcoming-events-tab');
    const pastEventsContent = document.getElementById('past-events-content');
    const upcomingEventsContent = document.getElementById('upcoming-events-content');
    
    if (pastEventsTab && upcomingEventsTab && pastEventsContent && upcomingEventsContent) {
        // Update tab styles
        upcomingEventsTab.className = 'px-3 md:px-8 py-1.5 md:py-3 rounded-full text-black font-semibold transition-all duration-300 bg-gradient-to-r from-white to-gray-300 shadow-lg text-xs md:text-base whitespace-nowrap';
        pastEventsTab.className = 'px-3 md:px-8 py-1.5 md:py-3 rounded-full text-gray-300 font-semibold transition-all duration-300 hover:text-white hover:bg-white/10 text-xs md:text-base whitespace-nowrap';
        
        // Show/hide content
        upcomingEventsContent.style.display = 'block';
        pastEventsContent.style.display = 'none';
    }
};



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
