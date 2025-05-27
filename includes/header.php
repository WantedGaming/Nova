<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && $_SESSION['access_level'] >= 1;
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>L1J-R Remastered</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-inner">
                <div class="logo">
                    <a href="/">L1J-R <span>Database</span></a>
                </div>
                
                <nav class="nav-links">
                    <a href="/" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a>
                    
                    <div class="nav-dropdown">
                        <a href="#" class="nav-link">Database</a>
                        <div class="dropdown-menu">
                            <a href="/weapon/weapon_list.php" class="dropdown-item">Weapons</a>
                            <a href="/armor/armor_list.php" class="dropdown-item">Armor</a>
                            <a href="/items/item_list.php" class="dropdown-item">Items</a>
                            <a href="/dolls/doll_list.php" class="dropdown-item">Magic Dolls</a>
                            <a href="/maps/map_list.php" class="dropdown-item">Maps</a>
                            <a href="/monsters/monster_list.php" class="dropdown-item">Monsters</a>
                        </div>
                    </div>
                    
                    <?php if ($isAdmin): ?>
                    <div class="nav-dropdown">
                        <a href="#" class="nav-link">Admin</a>
                        <div class="dropdown-menu">
                            <a href="/admin/dashboard.php" class="dropdown-item">Dashboard</a>
                            <a href="/admin/categories/weapon/admin_weapon_list.php" class="dropdown-item">Manage Weapons</a>
                            <a href="/admin/categories/armor/admin_armor_list.php" class="dropdown-item">Manage Armor</a>
                            <a href="/admin/users.php" class="dropdown-item">User Management</a>
                            <a href="/admin/settings.php" class="dropdown-item">Settings</a>
                        </div>
                    </div>
                    <?php endif; ?>
                </nav>

                <div class="header-buttons">
                    <?php if ($isLoggedIn): ?>
                        <div class="user-dropdown">
                            <button class="user-menu-toggle" onclick="toggleUserMenu()">
                                <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                                <span class="user-name"><?php echo htmlspecialchars($username); ?></span>
                                <span class="dropdown-arrow">▼</span>
                            </button>
                            <div class="user-dropdown-menu" id="userDropdownMenu">
                                <a href="/profile.php" class="dropdown-item">
                                    <span>👤</span> Profile
                                </a>
                                <?php if ($isAdmin): ?>
                                <a href="/admin/dashboard.php" class="dropdown-item">
                                    <span>⚙️</span> Admin Panel
                                </a>
                                <?php endif; ?>
                                <hr class="dropdown-divider">
                                <a href="/logout.php" class="dropdown-item logout">
                                    <span>🚪</span> Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <button class="login-button" onclick="openLoginModal()">Login</button>
                        <a href="/register.php" class="cta-button">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Login Modal -->
    <?php if (!$isLoggedIn): ?>
    <div class="login-modal-overlay" id="loginModal">
        <div class="login-modal">
            <button class="login-close" onclick="closeLoginModal()">&times;</button>
            <h2 class="login-title">Login to L1J Remastered</h2>
            <div class="login-error" id="loginError"></div>
            <form id="loginForm" onsubmit="handleLogin(event)">
                <div class="login-form-group">
                    <label class="login-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="login-input" required>
                </div>
                <div class="login-form-group">
                    <label class="login-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="login-input" required>
                </div>
                <button type="submit" class="login-submit">Login</button>
            </form>
            <div class="login-footer">
                <p>Don't have an account? <a href="/register.php">Register here</a></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // User dropdown functionality
        function toggleUserMenu() {
            const menu = document.getElementById('userDropdownMenu');
            const dropdown = menu.parentElement;
            menu.classList.toggle('active');
            dropdown.classList.toggle('active');
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userDropdown = document.querySelector('.user-dropdown');
            if (userDropdown && !userDropdown.contains(event.target)) {
                const menu = document.getElementById('userDropdownMenu');
                const dropdown = menu.parentElement;
                menu.classList.remove('active');
                dropdown.classList.remove('active');
            }
        });

        // Login modal functionality
        function openLoginModal() {
            document.getElementById('loginModal').classList.add('active');
        }

        function closeLoginModal() {
            document.getElementById('loginModal').classList.remove('active');
            document.getElementById('loginError').style.display = 'none';
            document.getElementById('loginForm').reset();
        }

        // Handle login form submission
        async function handleLogin(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const loginError = document.getElementById('loginError');
            
            try {
                const response = await fetch('./auth/login.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    window.location.reload();
                } else {
                    loginError.textContent = result.message;
                    loginError.style.display = 'block';
                }
            } catch (error) {
                console.error('Login error:', error);
                loginError.textContent = 'An error occurred: ' + error.message;
                loginError.style.display = 'block';
            }
        }

        // Navigation dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.nav-dropdown');
            
            dropdowns.forEach(dropdown => {
                const link = dropdown.querySelector('.nav-link');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Close other dropdowns
                    dropdowns.forEach(other => {
                        if (other !== dropdown) {
                            other.classList.remove('active');
                        }
                    });
                    
                    dropdown.classList.toggle('active');
                });
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.nav-dropdown')) {
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.remove('active');
                    });
                }
            });
        });
    </script>