<?php
require_once '../config/database.php';

// Check if user is logged in and has admin access
if (!isset($_SESSION['user_id']) || $_SESSION['access_level'] < 1) {
    header('Location: /');
    exit;
}

$pageTitle = 'Login Test & Account Management';
include '../includes/header.php';
?>

<div style="margin-top: 80px; min-height: calc(100vh - 80px); background-color: var(--background);">
    <div class="container" style="padding: 40px 20px;">
        <div style="background-color: var(--primary); border-radius: 8px; padding: 40px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);">
            <div style="display: flex; align-items: center; margin-bottom: 30px;">
                <div style="font-size: 2rem; margin-right: 15px;">🔧</div>
                <h1 style="font-size: 2rem; font-weight: 700; color: var(--text); margin: 0;">Database Connection Test & Account Management</h1>
            </div>

<?php
try {
    // Test database connection
    echo "<div class='test-section'>";
    echo "<h3>🔌 Database Connection Test</h3>";
    $result = $pdo->query("SELECT 1")->fetchColumn();
    echo "<div class='success'>✅ Database connection successful!</div>";
    echo "</div>";
    
    // Check if accounts table exists
    echo "<div class='test-section'>";
    echo "<h3>📋 Accounts Table Check</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE 'accounts'");
    if ($stmt->rowCount() > 0) {
        echo "<div class='success'>✅ Accounts table exists!</div>";
        
        // Check current accounts
        echo "<h4>Current Accounts in Database:</h4>";
        $accounts = $pdo->query("SELECT login, access_level FROM accounts ORDER BY access_level DESC LIMIT 10")->fetchAll();
        if (empty($accounts)) {
            echo "<div class='warning'>❌ No accounts found.</div>";
        } else {
            echo "<div class='table-container'>";
            echo "<table class='admin-table'>";
            echo "<thead><tr><th>Username</th><th>Access Level</th><th>Role</th></tr></thead>";
            echo "<tbody>";
            foreach ($accounts as $account) {
                $role = $account['access_level'] >= 1 ? 'Admin' : 'User';
                $roleClass = $account['access_level'] >= 1 ? 'role-admin' : 'role-user';
                echo "<tr>";
                echo "<td>" . htmlspecialchars($account['login']) . "</td>";
                echo "<td>" . $account['access_level'] . "</td>";
                echo "<td><span class='role-badge {$roleClass}'>{$role}</span></td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            echo "</div>";
        }
        echo "</div>";
        
        // Admin account statistics
        echo "<div class='test-section'>";
        echo "<h3>📊 Account Statistics</h3>";
        $totalAccounts = $pdo->query("SELECT COUNT(*) FROM accounts")->fetchColumn();
        $adminCount = $pdo->query("SELECT COUNT(*) FROM accounts WHERE access_level >= 1")->fetchColumn();
        $userCount = $totalAccounts - $adminCount;
        
        echo "<div class='stats-grid'>";
        echo "<div class='stat-card'><div class='stat-number'>{$totalAccounts}</div><div class='stat-label'>Total Accounts</div></div>";
        echo "<div class='stat-card admin'><div class='stat-number'>{$adminCount}</div><div class='stat-label'>Admin Accounts</div></div>";
        echo "<div class='stat-card user'><div class='stat-number'>{$userCount}</div><div class='stat-label'>User Accounts</div></div>";
        echo "</div>";
        echo "</div>";
        
        // Create test admin if needed
        if ($adminCount == 0) {
            echo "<div class='test-section'>";
            echo "<h3>⚠️ Admin Account Creation</h3>";
            echo "<div class='warning'>No admin accounts found. Creating test admin account...</div>";
            $stmt = $pdo->prepare("INSERT INTO accounts (login, password, access_level) VALUES (?, ?, ?)");
            $stmt->execute(['testadmin', 'password123', 1]);
            echo "<div class='success'>✅ Test admin account created!</div>";
            echo "<div class='credentials-box'>";
            echo "<strong>Test Admin Credentials:</strong><br>";
            echo "Username: <code>testadmin</code><br>";
            echo "Password: <code>password123</code><br>";
            echo "Access Level: <code>1 (Admin)</code>";
            echo "</div>";
            echo "</div>";
        }
        
    } else {
        echo "<div class='error'>❌ Accounts table does not exist!</div>";
    }
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ General error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                <a href="dashboard.php" style="display: inline-flex; align-items: center; gap: 8px; background-color: var(--accent); color: var(--text); padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    ← Back to Admin Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.test-section {
    background-color: var(--secondary);
    border-radius: 6px;
    padding: 24px;
    margin-bottom: 20px;
    border-left: 4px solid var(--accent);
}

.test-section h3 {
    color: var(--text);
    margin: 0 0 16px 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.test-section h4 {
    color: var(--text);
    margin: 20px 0 12px 0;
    font-size: 1rem;
    font-weight: 500;
}

.success {
    color: var(--success);
    background-color: rgba(40, 167, 69, 0.1);
    padding: 12px;
    border-radius: 4px;
    margin: 8px 0;
    font-weight: 500;
}

.warning {
    color: var(--warning);
    background-color: rgba(255, 193, 7, 0.1);
    padding: 12px;
    border-radius: 4px;
    margin: 8px 0;
    font-weight: 500;
}

.error {
    color: var(--danger);
    background-color: rgba(220, 53, 69, 0.1);
    padding: 12px;
    border-radius: 4px;
    margin: 8px 0;
    font-weight: 500;
}

.table-container {
    overflow-x: auto;
    margin: 16px 0;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    background-color: var(--background);
    border-radius: 6px;
    overflow: hidden;
}

.admin-table th,
.admin-table td {
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.admin-table th {
    background-color: var(--accent);
    color: var(--text);
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.admin-table td {
    color: var(--text);
}

.admin-table tr:hover {
    background-color: rgba(255, 255, 255, 0.05);
}

.role-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.role-admin {
    background-color: var(--accent);
    color: var(--text);
}

.role-user {
    background-color: rgba(255, 255, 255, 0.2);
    color: var(--text);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
    margin: 16px 0;
}

.stat-card {
    background-color: var(--background);
    padding: 20px;
    border-radius: 6px;
    text-align: center;
    border: 2px solid rgba(255, 255, 255, 0.1);
}

.stat-card.admin {
    border-color: var(--accent);
}

.stat-card.user {
    border-color: var(--info);
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: var(--accent);
    margin-bottom: 8px;
}

.stat-card.admin .stat-number {
    color: var(--accent);
}

.stat-card.user .stat-number {
    color: var(--info);
}

.stat-label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    font-weight: 500;
}

.credentials-box {
    background-color: var(--background);
    padding: 16px;
    border-radius: 6px;
    border: 1px solid var(--accent);
    margin: 12px 0;
}

.credentials-box code {
    background-color: rgba(249, 75, 31, 0.2);
    color: var(--accent);
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}
</style>

<?php include '../includes/footer.php'; ?>
