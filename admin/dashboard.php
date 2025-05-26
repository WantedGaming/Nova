<?php
// Start the session
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_login']) || !isset($_SESSION['access_level']) || $_SESSION['access_level'] != 1) {
    // Redirect to login page
    $_SESSION['login_error'] = "You must be logged in as an administrator to access this page.";
    header("Location: index.php");
    exit;
}

// Get the username from the session
$username = $_SESSION['user_login'];

// Database connection (you'll need to replace these with your actual database credentials)
$host = 'localhost';
$dbname = 'nova_db';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Count total users
    $stmt = $pdo->query("SELECT COUNT(*) FROM accounts");
    $totalUsers = $stmt->fetchColumn();
    
    // Count admin users
    $stmt = $pdo->query("SELECT COUNT(*) FROM accounts WHERE access_level = 1");
    $adminUsers = $stmt->fetchColumn();
    
    // Get some recent accounts
    $stmt = $pdo->query("SELECT login, lastactive, access_level, ip FROM accounts ORDER BY lastactive DESC LIMIT 5");
    $recentAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Handle database connection error
    $dbError = "Database connection failed: " . $e->getMessage();
    $totalUsers = 0;
    $adminUsers = 0;
    $recentAccounts = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOVA | Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
    <!-- Include Chart.js from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="logo">NOVA<span>.</span></a>
            <a href="index.php" class="back-to-site">
                <i class="icon">🏠</i> Back to Site
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">MAIN</div>
                <div class="nav-item active">
                    <i class="icon">📊</i>
                    <span class="nav-item-title">Dashboard</span>
                </div>
                <div class="nav-item">
                    <i class="icon">👥</i>
                    <span class="nav-item-title">Accounts</span>
                </div>
                <div class="nav-item">
                    <i class="icon">🛡️</i>
                    <span class="nav-item-title">Admin Access</span>
                </div>
                <div class="nav-item">
                    <i class="icon">📜</i>
                    <span class="nav-item-title">Logs</span>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">MANAGEMENT</div>
                <div class="nav-item">
                    <i class="icon">🛒</i>
                    <span class="nav-item-title">Shop Items</span>
                </div>
                <div class="nav-item">
                    <i class="icon">💰</i>
                    <span class="nav-item-title">Transactions</span>
                </div>
                <div class="nav-item">
                    <i class="icon">🎮</i>
                    <span class="nav-item-title">Game Settings</span>
                </div>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">SETTINGS</div>
                <div class="nav-item">
                    <i class="icon">⚙️</i>
                    <span class="nav-item-title">System Settings</span>
                </div>
                <div class="nav-item">
                    <i class="icon">🔒</i>
                    <span class="nav-item-title">Security</span>
                </div>
                <div class="nav-item">
                    <i class="icon">📋</i>
                    <span class="nav-item-title">Backup</span>
                </div>
            </div>
        </nav>
        
        <div class="user-profile">
            <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 2)); ?></div>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($username); ?></span>
                <span class="user-role">Administrator</span>
            </div>
            <a href="index.php?logout=1" class="logout-button">
                <i class="icon">↪️</i>
            </a>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="mobile-header">
            <button class="mobile-toggle" id="toggleSidebar">
                <i class="icon">☰</i>
            </button>
        </div>
        
        <div class="content-header">
            <h1 class="page-title">Dashboard</h1>
            
            <div class="header-actions">
                <div class="search-box">
                    <i class="icon search-icon">🔍</i>
                    <input type="text" class="search-input" placeholder="Search...">
                </div>
                
                <button class="notification-bell">
                    <i class="icon">🔔</i>
                    <span class="notification-indicator"></span>
                </button>
            </div>
        </div>
        
        <?php if (isset($dbError)): ?>
        <div class="alert alert-error" style="display: block;">
            <i class="alert-icon">⚠️</i>
            <div class="alert-message"><?php echo htmlspecialchars($dbError); ?></div>
        </div>
        <?php endif; ?>
        
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Accounts</span>
                    <div class="stat-icon users">👥</div>
                </div>
                <div class="stat-value"><?php echo number_format($totalUsers); ?></div>
                <div class="stat-change positive">
                    <i class="icon">↗️</i> Active platform
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Admin Accounts</span>
                    <div class="stat-icon revenue">🛡️</div>
                </div>
                <div class="stat-value"><?php echo number_format($adminUsers); ?></div>
                <div class="stat-change positive">
                    <i class="icon">↗️</i> Protected access
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Active Sessions</span>
                    <div class="stat-icon orders">🔄</div>
                </div>
                <div class="stat-value">127</div>
                <div class="stat-change positive">
                    <i class="icon">↗️</i> 12.3% since yesterday
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">System Status</span>
                    <div class="stat-icon traffic">🟢</div>
                </div>
                <div class="stat-value">Online</div>
                <div class="stat-change positive">
                    <i class="icon">↗️</i> All systems operational
                </div>
            </div>
        </div>
        
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h2 class="chart-title">Account Activity</h2>
                    <div class="chart-filters">
                        <button class="chart-filter" data-period="day">Day</button>
                        <button class="chart-filter" data-period="week">Week</button>
                        <button class="chart-filter active" data-period="month">Month</button>
                        <button class="chart-filter" data-period="year">Year</button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header">
                    <h2 class="chart-title">Access Level Distribution</h2>
                </div>
                <div class="chart-container">
                    <canvas id="accessLevelChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="accounts-section">
            <div class="accounts-header">
                <h2 class="accounts-title">Recent Accounts</h2>
                <button class="add-account-btn" id="openAddAccountModal">
                    <i class="icon">➕</i> Add Account
                </button>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Last Active</th>
                        <th>Access Level</th>
                        <th>IP Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAccounts as $account): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($account['login']); ?></td>
                        <td><?php echo $account['lastactive'] ? date('M d, Y H:i', strtotime($account['lastactive'])) : 'Never'; ?></td>
                        <td><?php echo $account['access_level'] == 1 ? 'Admin' : 'User'; ?></td>
                        <td><?php echo htmlspecialchars($account['ip']); ?></td>
                        <td>
                            <div class="table-actions">
                                <button class="action-btn" title="View Details">👁️</button>
                                <button class="action-btn" title="Edit Account">✏️</button>
                                <button class="action-btn" title="Delete Account">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($recentAccounts)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No accounts found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <!-- Add Account Modal -->
    <div class="modal-overlay" id="addAccountModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New Account</h3>
                <button class="close-modal" id="closeAddAccountModal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addAccountForm" action="account_actions.php" method="post">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="login" class="form-input" placeholder="Enter username" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" placeholder="Enter password" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Access Level</label>
                        <select name="access_level" class="form-select">
                            <option value="0">Regular User</option>
                            <option value="1">Administrator</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Character Slots</label>
                        <input type="number" name="charslot" class="form-input" value="6" min="1" max="10">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-cancel" id="cancelAddAccount">Cancel</button>
                <button class="btn btn-primary" id="submitAddAccount">Add Account</button>
            </div>
        </div>
    </div>
    
    <!-- Success Alert -->
    <div class="alert alert-success" id="successAlert" style="display: none;">
        <i class="alert-icon">✓</i>
        <div class="alert-message">Operation completed successfully!</div>
        <button class="alert-close" id="closeSuccessAlert">&times;</button>
    </div>
    
    <!-- Error Alert -->
    <div class="alert alert-error" id="errorAlert" style="display: none;">
        <i class="alert-icon">⚠️</i>
        <div class="alert-message" id="errorMessage">An error occurred!</div>
        <button class="alert-close" id="closeErrorAlert">&times;</button>
    </div>

    <script>
        // Toggle sidebar on mobile
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebar = document.querySelector('.sidebar');
        
        if (toggleSidebar) {
            toggleSidebar.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }
        
        // Add Account Modal
        const openAddAccountModal = document.getElementById('openAddAccountModal');
        const addAccountModal = document.getElementById('addAccountModal');
        const closeAddAccountModal = document.getElementById('closeAddAccountModal');
        const cancelAddAccount = document.getElementById('cancelAddAccount');
        const submitAddAccount = document.getElementById('submitAddAccount');
        const addAccountForm = document.getElementById('addAccountForm');
        
        if (openAddAccountModal) {
            openAddAccountModal.addEventListener('click', () => {
                addAccountModal.classList.add('active');
            });
        }
        
        if (closeAddAccountModal) {
            closeAddAccountModal.addEventListener('click', () => {
                addAccountModal.classList.remove('active');
            });
        }
        
        if (cancelAddAccount) {
            cancelAddAccount.addEventListener('click', () => {
                addAccountModal.classList.remove('active');
            });
        }
        
        if (submitAddAccount && addAccountForm) {
            submitAddAccount.addEventListener('click', () => {
                addAccountForm.submit();
            });
        }
        
        // Close modal when clicking outside
        if (addAccountModal) {
            addAccountModal.addEventListener('click', (e) => {
                if (e.target === addAccountModal) {
                    addAccountModal.classList.remove('active');
                }
            });
        }
        
        // Close alerts
        const closeSuccessAlert = document.getElementById('closeSuccessAlert');
        const closeErrorAlert = document.getElementById('closeErrorAlert');
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');
        
        if (closeSuccessAlert && successAlert) {
            closeSuccessAlert.addEventListener('click', () => {
                successAlert.style.display = 'none';
            });
        }
        
        if (closeErrorAlert && errorAlert) {
            closeErrorAlert.addEventListener('click', () => {
                errorAlert.style.display = 'none';
            });
        }
        
        // Charts
        // Activity Chart
        const activityChartCtx = document.getElementById('activityChart');
        if (activityChartCtx) {
            const activityChart = new Chart(activityChartCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Login Activity',
                        data: [65, 78, 90, 85, 92, 110, 120, 130, 125, 140, 155, 165],
                        borderColor: '#f94b1f',
                        backgroundColor: 'rgba(249, 75, 31, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'New Accounts',
                        data: [30, 40, 35, 45, 50, 55, 60, 65, 70, 75, 80, 85],
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#ffffff'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                color: '#ffffff'
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                color: '#ffffff'
                            }
                        }
                    }
                }
            });
        }
        
        // Access Level Chart
        const accessLevelChartCtx = document.getElementById('accessLevelChart');
        if (accessLevelChartCtx) {
            const accessLevelChart = new Chart(accessLevelChartCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Regular Users', 'Administrators'],
                    datasets: [{
                        data: [<?php echo $totalUsers - $adminUsers; ?>, <?php echo $adminUsers; ?>],
                        backgroundColor: ['#17a2b8', '#f94b1f'],
                        borderColor: ['#17a2b8', '#f94b1f'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#ffffff'
                            }
                        }
                    }
                }
            });
        }
        
        // Chart filters
        const chartFilters = document.querySelectorAll('.chart-filter');
        chartFilters.forEach(filter => {
            filter.addEventListener('click', () => {
                // Remove active class from all filters
                chartFilters.forEach(f => f.classList.remove('active'));
                // Add active class to clicked filter
                filter.classList.add('active');
                
                // Here you would update the chart data based on the selected period
                // For now just show an alert
                // In a real implementation, you would fetch data for the selected period via AJAX
                console.log('Filter changed to:', filter.dataset.period);
            });
        });
    </script>
</body>
</html>