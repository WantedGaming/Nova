<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

// Check if user is logged in and has admin access
if (!isset($_SESSION['user_id']) || $_SESSION['access_level'] < 1) {
    header('Location: /');
    exit;
}

$pageTitle = 'Admin Dashboard';

// Get database statistics
try {
    $stats = [
        'weapons' => $pdo->query("SELECT COUNT(*) FROM weapon")->fetchColumn(),
        'armor' => $pdo->query("SELECT COUNT(*) FROM armor")->fetchColumn(),
        'items' => $pdo->query("SELECT COUNT(*) FROM etcitem")->fetchColumn(),
        'npcs' => $pdo->query("SELECT COUNT(*) FROM npc")->fetchColumn(),
        'accounts' => $pdo->query("SELECT COUNT(*) FROM accounts")->fetchColumn(),
        'characters' => $pdo->query("SELECT COUNT(*) FROM characters")->fetchColumn(),
        'online_characters' => $pdo->query("SELECT COUNT(*) FROM characters WHERE OnlineStatus = 1")->fetchColumn(),
        'admin_accounts' => $pdo->query("SELECT COUNT(*) FROM accounts WHERE access_level >= 1")->fetchColumn(),
    ];

    // Recent activity
    $recentLogins = $pdo->query("
        SELECT login, lastactive 
        FROM accounts 
        WHERE lastactive IS NOT NULL 
        ORDER BY lastactive DESC 
        LIMIT 5
    ")->fetchAll();

    $topCharacters = $pdo->query("
        SELECT char_name, level, Class 
        FROM characters 
        ORDER BY level DESC 
        LIMIT 5
    ")->fetchAll();

    // System stats
    $serverUptime = "24 hours 15 minutes"; // You can calculate this dynamically
    $avgPlayersOnline = round($stats['online_characters'] * 1.2); // Example calculation

} catch (PDOException $e) {
    $stats = array_fill_keys(['weapons', 'armor', 'items', 'npcs', 'accounts', 'characters', 'online_characters', 'admin_accounts'], 0);
    $recentLogins = [];
    $topCharacters = [];
    $serverUptime = "Unknown";
    $avgPlayersOnline = 0;
}

// Class names mapping
$classNames = [
    0 => 'Prince', 1 => 'Knight', 2 => 'Elf', 3 => 'Wizard', 4 => 'Dark Elf',
    5 => 'Dragon Knight', 6 => 'Illusionist', 7 => 'Warrior', 8 => 'Fencer', 9 => 'Lancer'
];

include '../includes/header.php';
?>

<!-- Styles now loaded from admin.css -->

<div class="dashboard-container">
    <div class="container">
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">
                    Admin Dashboard
                </h1>
                <p class="dashboard-subtitle">
                    Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>
                </p>
            </div>
            <div class="dashboard-icon">⚙️</div>
        </div>

        <!-- Statistics Cards -->
        <div class="overview-cards" style="margin-bottom: 40px;">
            <div class="feature-card metric-card">
                <div class="metric-icon">⚔️</div>
                <div>
                    <div class="metric-value">
                        <?php echo number_format($stats['weapons']); ?>
                    </div>
                    <div class="metric-label">Weapons</div>
                    <a href="/admin/categories/weapon/admin_weapon_list.php" class="manage-link">Manage</a>
                </div>
            </div>

            <div class="feature-card metric-card">
                <div class="metric-icon">🛡️</div>
                <div>
                    <div class="metric-value">
                        <?php echo number_format($stats['armor']); ?>
                    </div>
                    <div class="metric-label">Armor Pieces</div>
                    <a href="/admin/categories/armor/admin_armor_list.php" class="manage-link">Manage</a>
                </div>
            </div>

            <div class="feature-card metric-card">
                <div class="metric-icon">💎</div>
                <div>
                    <div class="metric-value">
                        <?php echo number_format($stats['items']); ?>
                    </div>
                    <div class="metric-label">Items</div>
                    <a href="/admin/items/admin_item_list.php" class="manage-link">Manage</a>
                </div>
            </div>

            <div class="feature-card metric-card">
                <div class="metric-icon">👹</div>
                <div>
                    <div class="metric-value">
                        <?php echo number_format($stats['npcs']); ?>
                    </div>
                    <div class="metric-label">NPCs</div>
                    <a href="/admin/npcs/admin_npc_list.php" class="manage-link">Manage</a>
                </div>
            </div>
        </div>

        <!-- Server Statistics -->
        <div class="overview-cards" style="margin-bottom: 40px;">
            <div class="feature-card metric-card">
                <div class="metric-icon">👥</div>
                <div>
                    <div class="metric-value">
                        <?php echo number_format($stats['accounts']); ?>
                    </div>
                    <div class="metric-label">Total Accounts</div>
                </div>
            </div>

            <div class="feature-card metric-card">
                <div class="metric-icon">🧙‍♂️</div>
                <div>
                    <div class="metric-value">
                        <?php echo number_format($stats['characters']); ?>
                    </div>
                    <div class="metric-label">Total Characters</div>
                </div>
            </div>

            <div class="feature-card metric-card success">
                <div class="metric-icon">🟢</div>
                <div>
                    <div class="metric-value" style="color: var(--success);">
                        <?php echo number_format($stats['online_characters']); ?>
                    </div>
                    <div class="metric-label">Online Now</div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Top Characters -->
        <div class="activity-panels" style="margin-bottom: 40px;">
            <!-- Recent Logins -->
            <div class="panel">
                <div class="panel-header">
                    <h3 class="panel-title">Recent Logins</h3>
                </div>
                <div class="panel-content">
                    <?php if (!empty($recentLogins)): ?>
                        <?php foreach ($recentLogins as $login): ?>
                            <div class="activity-item">
                                <div class="activity-avatar"><?php echo strtoupper(substr($login['login'], 0, 1)); ?></div>
                                <div class="activity-info">
                                    <div class="activity-name"><?php echo htmlspecialchars($login['login']); ?></div>
                                </div>
                                <div class="activity-time">
                                    <?php echo $login['lastactive'] ? date('M j, H:i', strtotime($login['lastactive'])) : 'Never'; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">No recent login data</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Top Characters -->
            <div class="panel">
                <div class="panel-header">
                    <h3 class="panel-title">Top Characters</h3>
                </div>
                <div class="panel-content">
                    <?php if (!empty($topCharacters)): ?>
                        <?php foreach ($topCharacters as $index => $char): ?>
                            <div class="activity-item">
                                <div class="activity-rank">
                                    <?php echo $index + 1; ?>
                                </div>
                                <div class="activity-info">
                                    <div class="activity-name"><?php echo htmlspecialchars($char['char_name']); ?></div>
                                    <div class="activity-time">
                                        <?php echo $classNames[$char['Class']] ?? 'Unknown'; ?>
                                    </div>
                                </div>
                                <div class="activity-level">Lv. <?php echo $char['level']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">No character data</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-section">
            <div class="section-header">
                <h3 class="section-title">Quick Actions</h3>
            </div>
            <div class="actions-grid">
                <a href="/admin/categories/weapon/admin_weapon_list.php" class="action-card">
                    <span class="action-icon">⚔️</span>
                    <div>
                        <div class="action-title">Manage Weapons</div>
                    </div>
                </a>
                <a href="/admin/categories/armor/admin_armor_list.php" class="action-card">
                    <span class="action-icon">🛡️</span>
                    <div>
                        <div class="action-title">Manage Armor</div>
                    </div>
                </a>
                <a href="/admin/users.php" class="action-card">
                    <span class="action-icon">👥</span>
                    <div>
                        <div class="action-title">User Management</div>
                    </div>
                </a>
                <a href="/admin/settings.php" class="action-card">
                    <span class="action-icon">⚙️</span>
                    <div>
                        <div class="action-title">Settings</div>
                    </div>
                </a>
                <a href="/admin/logs.php" class="action-card">
                    <span class="action-icon">📋</span>
                    <div>
                        <div class="action-title">View Logs</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>