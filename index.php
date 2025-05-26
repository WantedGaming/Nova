<?php
// Start the session
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_login']);
$isAdmin = isset($_SESSION['access_level']) && $_SESSION['access_level'] == 1;

// Handle logout
if (isset($_GET['logout'])) {
    // Destroy the session
    session_destroy();
    
    // Redirect to the homepage
    header("Location: index.php");
    exit;
}

// Handle login errors (if any)
$loginError = "";
if (isset($_SESSION['login_error'])) {
    $loginError = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOVA | Digital Solutions</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-inner">
                <a href="index.php" class="logo">NOVA<span>.</span></a>
                
                <nav>
                    <ul class="nav-links">
                        <li><a href="#" class="nav-link">Home</a></li>
                        <li><a href="#" class="nav-link">Services</a></li>
                        <li><a href="#" class="nav-link">Projects</a></li>
                        <li><a href="#" class="nav-link">About</a></li>
                        <li><a href="#" class="nav-link">Contact</a></li>
                    </ul>
                </nav>
                
                <div class="header-buttons">
                    <?php if ($isLoggedIn): ?>
                        <?php if ($isAdmin): ?>
                        <a href="admin.php" class="login-button">Admin Panel</a>
                        <?php endif; ?>
                        <a href="index.php?logout=1" class="login-button">Logout</a>
                    <?php else: ?>
                        <button id="loginBtn" class="login-button">Login</button>
                    <?php endif; ?>
                    <button class="cta-button">Get Started</button>
                </div>
                
                <button class="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>
    
    <div class="mobile-menu">
        <ul class="mobile-nav-links">
            <li><a href="#" class="mobile-nav-link">Home</a></li>
            <li><a href="#" class="mobile-nav-link">Services</a></li>
            <li><a href="#" class="mobile-nav-link">Projects</a></li>
            <li><a href="#" class="mobile-nav-link">About</a></li>
            <li><a href="#" class="mobile-nav-link">Contact</a></li>
            <?php if ($isLoggedIn): ?>
                <?php if ($isAdmin): ?>
                <li><a href="admin.php" class="mobile-nav-link">Admin Panel</a></li>
                <?php endif; ?>
                <li><a href="index.php?logout=1" class="mobile-nav-link">Logout</a></li>
            <?php else: ?>
                <li><a href="#" id="mobileLoginBtn" class="mobile-nav-link">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
    
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Elevate Your Digital <span>Presence</span></h1>
                <p class="hero-description">We create cutting-edge digital solutions that help businesses grow, innovate, and transform in the digital landscape.</p>
                <div class="hero-cta">
                    <button class="cta-button">Get Started</button>
                    <a href="#" class="secondary-cta">Learn More</a>
                </div>
            </div>
        </div>
        
        <div class="hero-visual">
            <div class="hero-visual-inner">
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
                <div class="circle circle-3"></div>
                <div class="grid"></div>
            </div>
        </div>
    </section>
    
    <section class="features">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">We offer a comprehensive range of digital services to help your business thrive in the modern world.</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">💻</div>
                    <h3 class="feature-title">Web Development</h3>
                    <p class="feature-description">We create responsive, user-friendly websites that engage visitors and drive conversions.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3 class="feature-title">Mobile Apps</h3>
                    <p class="feature-description">Native and cross-platform mobile applications that deliver exceptional user experiences.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">⚙️</div>
                    <h3 class="feature-title">Custom Software</h3>
                    <p class="feature-description">Tailor-made software solutions designed to solve your specific business challenges.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🔍</div>
                    <h3 class="feature-title">SEO Optimization</h3>
                    <p class="feature-description">Improve your online visibility and drive more organic traffic to your website.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🎨</div>
                    <h3 class="feature-title">UI/UX Design</h3>
                    <p class="feature-description">Intuitive, beautiful interfaces that enhance user satisfaction and engagement.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">Data Analytics</h3>
                    <p class="feature-description">Turn your data into actionable insights with our advanced analytics solutions.</p>
                </div>
            </div>
        </div>
    </section>
    
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <a href="#" class="logo">NOVA<span>.</span></a>
                    <p style="margin-top: 20px; opacity: 0.7; line-height: 1.6;">
                        We create digital experiences that matter. Let's build something amazing together.
                    </p>
                </div>
                
                <div class="footer-col">
                    <h4>Company</h4>
                    <ul class="footer-links">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Our Team</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul class="footer-links">
                        <li><a href="#">Web Development</a></li>
                        <li><a href="#">Mobile Apps</a></li>
                        <li><a href="#">UI/UX Design</a></li>
                        <li><a href="#">SEO Optimization</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul class="footer-links">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> NOVA Digital Solutions. All rights reserved. | Designed with ❤️
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="login-modal-overlay" id="loginModal">
        <div class="login-modal">
            <button class="login-close" id="closeLoginModal">&times;</button>
            <h2 class="login-title">Account Login</h2>
            
            <?php if (!empty($loginError)): ?>
            <div class="login-error" style="display: block;">
                <?php echo htmlspecialchars($loginError); ?>
            </div>
            <?php endif; ?>
            
            <form action="login.php" method="post" id="loginForm">
                <div class="login-form-group">
                    <label for="login" class="login-label">Username</label>
                    <input type="text" id="login" name="login" class="login-input" required>
                </div>
                
                <div class="login-form-group">
                    <label for="password" class="login-label">Password</label>
                    <input type="password" id="password" name="password" class="login-input" required>
                </div>
                
                <button type="submit" class="login-submit">Login</button>
            </form>
            
            <div class="login-footer">
                Don't have an account? <a href="#">Register</a>
            </div>
        </div>
    </div>

    <script>
        // Mobile Menu Toggle
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        const mobileMenu = document.querySelector('.mobile-menu');
        
        mobileMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
            document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : 'auto';
            
            const spans = mobileMenuToggle.querySelectorAll('span');
            if (mobileMenu.classList.contains('active')) {
                spans[0].style.transform = 'rotate(45deg) translate(5px, 6px)';
                spans[1].style.opacity = '0';
                spans[2].style.transform = 'rotate(-45deg) translate(5px, -6px)';
            } else {
                spans[0].style.transform = 'none';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'none';
            }
        });
        
        // Mobile menu links close menu when clicked
        const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = 'auto';
                
                const spans = mobileMenuToggle.querySelectorAll('span');
                spans[0].style.transform = 'none';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'none';
            });
        });
        
        // Header scroll effect
        const header = document.querySelector('header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                header.style.height = '70px';
                header.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.1)';
            } else {
                header.style.height = '80px';
                header.style.boxShadow = 'none';
            }
        });
        
        // Feature cards animation
        const featureCards = document.querySelectorAll('.feature-card');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 100);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        featureCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.5s ease';
            observer.observe(card);
        });
        
        // Login Modal
        const loginBtn = document.getElementById('loginBtn');
        const mobileLoginBtn = document.getElementById('mobileLoginBtn');
        const loginModal = document.getElementById('loginModal');
        const closeLoginModal = document.getElementById('closeLoginModal');
        
        if (loginBtn) {
            loginBtn.addEventListener('click', () => {
                loginModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        }
        
        if (mobileLoginBtn) {
            mobileLoginBtn.addEventListener('click', () => {
                loginModal.classList.add('active');
                document.body.style.overflow = 'hidden';
                // Close mobile menu
                mobileMenu.classList.remove('active');
                
                const spans = mobileMenuToggle.querySelectorAll('span');
                spans[0].style.transform = 'none';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'none';
            });
        }
        
        if (closeLoginModal) {
            closeLoginModal.addEventListener('click', () => {
                loginModal.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        }
        
        // Close modal when clicking outside
        loginModal.addEventListener('click', (e) => {
            if (e.target === loginModal) {
                loginModal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    </script>
</body>
</html>